<?php
/**
 * Our class
 */
class CustomPostType extends CustomPostTypeBase { 
    
    // @todo do we need this?
    static $instance;

    public $dependencies = array();
        
    /**
     * Every thing that is "custom" to our CPT goes here.
     */
    public function __construct() {
        
        wp_localize_script( 'my-ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );                

        //self::$instance = $this;       
        
        $this->dependencies['script'] = array(
            'jquery',
            'jquery-ui-core',
            'jquery-ui-dialog'
        );

        $this->dependencies['style'] = array(
            'tt-base-style',
            'inplace-edit-style'
            );

        // @todo the abstract should possibly be responsible for doing this
        add_action( 'init', array( &$this, 'registerPostType' ) );        
        add_action( 'init', array( &$this, 'registerTaxonomy' ) );                            
        add_action( 'template_redirect', array( &$this, 'templateRedirect' ) );            
        add_action( 'wp_head', array( &$this, 'baseAjaxUrl' ) );                    
        add_action( 'wp_ajax_loadTemplate', array( &$this, 'loadTemplate' ) ); 
        add_action( 'wp_ajax_nopriv_loadTemplate', array( &$this, 'loadTemplate' ) ); // for public use
        add_action( 'wp_footer', array( &$this, 'createPostTypeDiv' ) );            
        add_action( 'wp_footer', array( &$this, 'createDeleteDiv' ) );            
        add_action( 'wp_ajax_postTypeSubmit', array( &$this, 'postTypeSubmit' ) );                
        add_action( 'wp_ajax_postTypeUpdate', array( &$this, 'postTypeUpdate' ) );
        add_action( 'wp_ajax_postTypeDelete', array( &$this, 'postTypeDelete' ) );
        add_action( 'wp_ajax_defaultUtilityUpdate', array( &$this, 'defaultUtilityUpdate' ) );        
        add_action( 'wp_ajax_addComment', array( &$this, 'addComment' ) );

        add_filter( 'post_class', array( &$this, 'addPostClass' ) );
                        
        register_activation_hook( __FILE__, array( &$this, 'regsiterActivation') );        

        if ( !is_admin() ) {
            wp_register_style( 'tt-base-style',      plugin_dir_url( __FILE__ ) . 'theme/css/style.css', '', 'all' );
            wp_register_style( 'qtip-nightly-style', plugin_dir_url( __FILE__ ) . 'library/js/qtip-nightly/jquery.qtip.min.css', '', 'all' );
            wp_register_style( 'inplace-edit-style', plugin_dir_url( __FILE__ ) . 'library/js/inplace-edit/inplace-edit.css', '', 'all' );        
            
            wp_register_script( 'tt-script',           plugin_dir_url( __FILE__ ) . 'theme/js/script.js', $this->dependencies['script'], '1.0' );        
            wp_register_script( 'qtip-nightly',        plugin_dir_url( __FILE__ ) . 'library/js/qtip-nightly/jquery.qtip.min.js', $this->dependencies['script'], '0.0.1' );            
            wp_register_script( 'jquery-ui-effects',   plugin_dir_url( __FILE__ ) . 'library/js/jquery-ui/jquery-ui-1.8.13.effects.min.js', $this->dependencies['script'], '1.8.13' );        
            wp_register_script( 'inplace-edit-script', plugin_dir_url( __FILE__ ) . 'library/js/inplace-edit/inplace-edit.js', $this->dependencies['script'], '0.1' );                
        }
        $this->loginSetup();                
    }
        

    public function regsiterActivation() {

        /**
         * Dont forget registration hook is called 
         * BEFORE! taxonomies are regsitered! therefore
         * these terms and taxonomies are NOT derived from our object!
         */
        $taxonomies = array( 'priority', 'status', 'type' );
        $this->registerTaxonomy( $taxonomies );
                
        // Set to we know its been installed at least once before
        $installed = get_option( 'zm_tt_number_installed' );

        if ( $installed == '1' )
            return;
    
        // Priority 
        wp_insert_term( 'High',   'priority', array( 'description' => '', 'slug' => 'high' ) );
        wp_insert_term( 'Low',    'priority', array( 'description' => '', 'slug' => 'low' ) );
        wp_insert_term( 'Medium', 'priority', array( 'description' => '', 'slug' => 'medium' ) );

        // Status
        wp_insert_term( 'Aborted',  'status', array( 'description' => 'A Task that will NOT be completed.', 'slug' => 'aborted' ) );
        wp_insert_term( 'Closed',   'status', array( 'description' => 'A Task that has been resolved and reviewed is completed.', 'slug' => 'closed' ) );
        wp_insert_term( 'New',      'status', array( 'description' => 'A New Task is a Task that is waiting to be worked on.', 'slug' => 'new' ) );
        wp_insert_term( 'Open',     'status', array( 'description' => 'A Task that is currently being worked on.', 'slug' => 'open' ) );
        wp_insert_term( 'Resolved', 'status', array( 'description' => 'The Task has been finished, but needs to be approved before it is closed.', 'slug' => 'resolved' ) );

        // Type 
        wp_insert_term( 'Car',         'type', array( 'description' => 'Anything related to your car, cleaning, vacuming, oil change and so on.') );
        wp_insert_term( 'Computer',    'type', array( 'description' => 'Checking emails, programming, writing papers.' ) );
        wp_insert_term( 'Freelance',   'type', array( 'description' => 'What ever makes you some extra side cash.' ) );
        wp_insert_term( 'House',       'type', array( 'description' => 'House work, be it laundry, vacuuming or just cleaning.' ) );
        wp_insert_term( 'Personal',    'type', array( 'description' => 'Anything you can think of.' ) );
        wp_insert_term( 'Photography', 'type', array( 'description' => 'Taking photos, grooming your photo library or some quick editing.' ) );
            
        // insert sample task  
        $author_ID = get_current_user_id();
        $post = array(
            'post_title' => 'Your first Task!',
            'post_content' => 'This is a sample Task make it short and sweet, hopefully this system will help you get a tad more stuff done :D',
            'post_author' => $author_ID,
            'post_type' => 'task',
            'post_status' => 'publish'
        );
        $post_id = wp_insert_post( $post, true );

        // assign a term for our sample Task  
        if ( isset( $post_id ) ) {
            $term_id = term_exists( 'medium', 'priority' );
            wp_set_post_terms( $post_id, $term_id, 'priority' );

            $term_id = term_exists( 'new', 'status' );
            wp_set_post_terms( $post_id, $term_id, 'status' );

            $term_id = term_exists( 'personal', 'type' );
            wp_set_post_terms( $post_id, $term_id, 'type' );

            update_option( 'zm_tt_number_installed', '1' );
        }        
    }    

    /**
     * Add additional classes to post_class() for additional CSS styling and JavaScript manipulation.
     *
     * Adds public and NOT builtin terms to the post_class function call outputing the following:
     * term_slug-taxonomy_id
     * @todo addPostClass() consider moving this to the abstract
     */
    public function addPostClass( $classes ) {
        global $post;
        $cpt = $post->post_type;
                    
        $cpt_obj = get_post_types( array( 'name' => $cpt ), 'objects' );

        foreach( $cpt_obj[ $cpt ]->taxonomies  as $name ) {
            $terms = get_the_terms( $post->ID, $name );
            if ( !is_wp_error( $terms ) && !empty( $terms )) {
                foreach( $terms as $term ) {
                    $classes[] = $name . '-' . $term->term_id;
                }
            } 
        }
        
        return $classes;        
    } // End 'addPostClass'
    
    /**
     * to be used in AJAX submission, gets the $_POST data and logs the user in.
     */    
    public function siteLoginSubmit() {
        // @todo needs to be generic for cpt
        check_ajax_referer( 'tt-ajax-forms', 'security' );
        
        // @todo this should include EVEERYTHING needed for ajax login to work!
        // js, css, actions etc.
        $creds = array();
        $creds['user_login'] = $_POST['user_name'];
        $creds['user_password'] = $_POST['password'];

        if ( $_POST['remember'] == 'on' )
            $creds['remember'] = true;
        else            
            $creds['remember'] = false;
            
        $user = wp_signon( $creds, false );

        if ( is_wp_error( $user ) )
            $user->get_error_message();

        die();
    } // siteLoginSubmit

    /**
     * Login set-up
     * 
     * Note this does NOT hook into the default WordPress login! In essence 
     * you will need custom mark-up. Telling it which template to call
     * and create you own, see theme/default/login.php 
     */
    public function loginSetup() {

        add_action( 'wp_footer', array( &$this, 'createLoginDiv' ) );            
        add_action( 'wp_ajax_siteLoginSubmit', array( &$this, 'siteLoginSubmit' ) );        
        add_action( 'wp_ajax_nopriv_siteLoginSubmit', array( &$this, 'siteLoginSubmit' ) ); 

        $dependencies['style'] = array(
            'tt-base-style',
            'wp-jquery-ui-dialog'
        );
        
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
        wp_enqueue_style( 'tt-login-style', plugin_dir_url( __FILE__ ) . 'theme/css/login.css', $dependencies['style'], 'all' );        
        wp_enqueue_script( 'jquery-ui-effects' );                
    } // End 'loginSetup'

    public function createLoginDiv(){ ?>
    <div id="login_dialog" class="dialog-container">
            <div id="login_target" style="display: none;">login hi</div>
        </div>
    <?php }

    public function createPostTypeDiv(){ ?>
        <div id="create_ticket_dialog" class="dialog-container">
            <div id="create_ticket_target" style="display: none;">hi</div>
        </div>
    <?php } 

    public function createDeleteDiv(){ ?>
        <div id="delete_dialog" class="dialog-container">
            <p>These items will be permanently deleted and cannot be recovered. Are you sure?</p>
            <div id="delete_target" style="display: none">delete hi</div>
        </div>
    <?php }
} // End 'CustomPostType'
