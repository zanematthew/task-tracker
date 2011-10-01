<?php
if ( is_admin() ) {
    ini_set('display_errors', 0);
    error_reporting( E_ALL );
}

/**
 * Registers custom post type: "Task" with custom taxonoimes: "Type", "Status", "Priority", "Milestone", "Assigned" and "Project".
 */

/**
 * Plugin Name: Task Tracker
 * Plugin URI: --
 * Description: Turns WP into a Task Tracking system.
 * Version: .1
 * Author: Zane M. Kolnik
 * Author URI: http://zanematthew.com/
 * License: GP
 */

/**
 * NON-SPECIFIC functions, a few useful WP functions I've come up with.
 */
 require_once 'wordpress-helper-functions.php';

/**
 * SPECIFIC functions for the TaskTracker, nothing OOP.
 */
require_once 'functions.php';

/**
 * A predetermined list of methods our class MUST implement
 *
 * The goal of this interface is to provide a set of base methods for making Custom Post Types in WordPress 3.2+. 
 * With these methods you should be able to create an unlimted number of CPTs and CTs, with as less code as possible.
 * Along with having basic functionality either already built or guided for you.
 *
 * Your plugin MUST do the following:
 * - Regsiter a custom post type(s)
 * - Regsiter a custom taxonomy(s)
 * - Provide a default archive template, which can be overriden by the theme, i.e template redirect
 * - Provide a default single template, which can be overriden by the theme 
 * - Activation script
 * - Deactivation script (to come)
 * - enqueue a base stylesheet (to come)
 */
 
 // @todo namespace
interface ICustomPostType {

    public function registerPostType( $param=array() );
    public function registerTaxonomy( $param=array() );        
    // @todo http://codex.wordpress.org/Function_Reference/locate_template
    public function templateRedirect();
    public function regsiterActivation();
    // public function regsiterDeactivation();
    // public function baseStyleSheet( $param=array() );

} // End 'ICustomPostType'


/**
 * This is used to regsiter a custom post type, custom taxonomy and provide template redirecting.
 * 
 * Use the following templates if they exisits, fall back on normal WordPress template hierarchy:
 * plugin_dir/themes/single-[my custom post type].php
 * plugin_dir/themes/archive-[my custom post type].php 
 * plugin_dir/thems/taxonomy-[my custom taxonomy].php 
 *
 */
abstract class CustomPostTypeBase implements ICustomPostType {

    public $plugin_url = WP_PLUGIN_URL;
    public $plugin_dir = WP_PLUGIN_DIR;
    
    /**
     * Regsiter an unlimited number of CPTs based on an array of parmas.
     * 
     * Note, some args are still hard coded.
     * Full list @ http://codex.wordpress.org/Function_Reference/register_post_type     
     */
    public function registerPostType( $args=NULL ) {
        $taxonomies = array();

        // our white list taken from http://codex.wordpress.org/Function_Reference/register_post_type see 'supports'
        $white_list = array();
        
        // Default, title, editor
        $white_list['supports'] = array(
                'title',
                'editor',
                'author',
                'thumbnail',
                'excerpt',
                'comments',
                'custom-fields',
                'trackbacks'
                );
                    
        foreach ( $this->post_type as $post_type ) {

            if ( !empty( $post_type['taxonomies'] ) )                                
                $taxonomies = $post_type['taxonomies'];            
        
            $post_type['type'] = strtolower( $post_type['type'] );
            
            if ( empty( $post_type['singular_name'] ) )
                $post_type['singular_name'] = $post_type['name'];

            if ( empty( $post_type['slug'] ) )
                $post_type['slug'] = $post_type['type'];
                                            
            $labels = array(
                'name' => _x( $post_type['name'], 'post type general name'),
                'singular_name' => _x( $post_type['singular_name'], 'post type singular name'),
                'add_new' => _x('Add New ' . $post_type['singular_name'] . '', 'something'),
                'add_new_item' => __('Add New ' . $post_type['singular_name'] . ''),
                'edit_item' => __('Edit '. $post_type['singular_name'] .''),
                'new_item' => __('New '. $post_type['singular_name'] .''),
                'view_item' => __('View '. $post_type['singular_name'] . ''),
                'search_items' => __('Search ' . $post_type['singular_name'] . ''),
                'not_found' => __('No ' . $post_type['singular_name'] . ' found'),
                'not_found_in_trash' => __('No ' . $post_type['singular_name'] . ' found in Trash'),
                'parent_item_colon' => ''
                );

            if ( in_array( $post_type['supports'], $white_list['supports'] ) )
                $supports = $post_type['supports'];
            else
                $supports = null;
                
            $capabilities = array(
                'edit_article'
                );
             
            // @todo make defaults optional
            $args = array(
                'labels' => $labels,
                'public' => true,
                'capability_type' => 'post',                
                'supports' => $supports,
                'rewrite' => array( 'slug' => $post_type['slug'] ),
                'hierarchical' => true,
                'description' => 'None for now GFYS',
                'taxonomies' => $taxonomies,
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 5,
                'show_in_nav_menus' => true,
                'publicly_queryable' => true,
                'exclude_from_search' => false,
                'has_archive' => true,
                'query_var' => true,
                'can_export' => true,
                'rewrite' => true,
                'capability_type' => 'post'
                );
            
            register_post_type( $post_type['type'], $args);

        } // End 'foreach'         

        return $this->post_type;

    } // End 'function'

    public function registerTaxonomy( $args=NULL ) {
        foreach ( $this->taxonomy as $taxonomy ) {
            
            if ( empty( $taxonomy['taxonomy'] ) )
                $taxonomy['taxonomy'] = strtolower( str_replace( " ", "-", $taxonomy['name'] ) );

            if ( empty( $taxonomy['slug'] ) )
                $taxonomy['slug'] = $taxonomy['taxonomy'];
            
            if ( empty( $taxonomy['singular_name'] ) )
                $taxonomy['singular_name'] = $taxonomy['name'];

            if ( empty( $taxonomy['plural_name'] ) )
                $taxonomy['plural_name'] = $taxonomy['name'] . 's';

            /** @todo if this as fasle fucks up on wp_set_post_terms() for submitting and updating a cpt */
            if ( empty( $taxonomy['hierarchical']) )
                $taxonomy['hierarchical'] = false;

            $labels = array(
                'name'              => _x( $taxonomy['name'], 'taxonomy general name' ),
                'singular_name'     => _x( $taxonomy['singular_name'], 'taxonomy singular name' ),
                'search_items'      => __( 'Search ' . $taxonomy['plural_name'] . ''),
                'all_items'         => __( 'All ' . $taxonomy['plural_name'] . '' ),
                'parent_item'       => __( 'Parent ' . $taxonomy['singular_name'] . '' ),
                'parent_item_colon' => __( 'Parent ' . $taxonomy['singular_name'] . ': ' ),
                'edit_item'         => __( 'Edit ' . $taxonomy['singular_name'] . '' ),
                'update_item'       => __( 'Update ' . $taxonomy['singular_name'] . ''),
                'add_new_item'      => __( 'Add New ' . $taxonomy['singular_name'] . ''),
                'new_item_name'     => __( 'New ' . $taxonomy['singular_name'] . ' Name' )
                );

// @todo needs options
            $args = array(
                'labels'  => $labels,
                // 'hierarchical' => $taxonomy['hierarchical'],
                'hierarchical' => true, // non-hierarchical taxes break alot of stuff
                'query_var' => true,
                'public' => true,
                'rewrite' => array('slug' => $taxonomy['slug']),
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'show_tagcloud' => true
                );
                
            register_taxonomy( $taxonomy['taxonomy'], $taxonomy['post_type'], $args );
            
        } // End 'foreach'
       
        return $this->taxonomy;
    
    } // End 'function'   

    /**
     * Get somethings and do a little bit of thinking before 
     * calling the redirect methods.
     */
    public function templateRedirect() {

        // @todo this needs to be generic
        wp_enqueue_style( 'qtip-nightly-style' );
        wp_enqueue_style( 'tt-base-style' );
        wp_enqueue_script( 'tt-script' );
        wp_enqueue_script( 'qtip-nightly' );
        wp_enqueue_script( 'jquery-ui-effects' );

        $current_post_type = get_query_var( 'post_type' );
                
        $this->taxonomyRedirect( $current_post_type );
        // @todo single template first
        $this->singleRedirect( $current_post_type );        
        $this->archiveRedirect( $current_post_type );
        
    } // End 'function templateRedirect'    

    // Did I make one?
    // Use MY default?
    // Use WP archive
    // Use WP index
    public function taxonomyRedirect( $current_post_type=null ) {

        if ( is_null( $current_post_type ) )
            wp_die( 'I need a CPT');

        foreach( $this->post_type as $wtf ) {
            $my_cpt = get_post_types( array( 'name' => $wtf['type']), 'objects' );                    
            if ( is_tax( $wtf['taxonomies'] ) ) {                    
                global $wp_query;
                if ( in_array( $wp_query->query_vars['taxonomy'], $wtf['taxonomies'] ) ) {                    
                    // custom plugin theme
                    if ( file_exists( MY_PLUGIN_DIR . 'theme/custom/' . $wtf['type'] . '-taxonomy.php' ) ) {                        
                        // @todo enqueue needed css/js
                        load_template( MY_PLUGIN_DIR . 'theme/custom/' . $wtf['type'] . '-taxonomy.php' );                    
                    // default plugin theme               
                    } elseif ( file_exists( MY_PLUGIN_DIR . 'theme/default/taxonomy.php' ) ) {                        
                        // @todo enqueue needed css/js
                        load_template( MY_PLUGIN_DIR . 'theme/default/taxonomy.php' );                    
                    // theme archive
                    } elseif ( file_exists( STYLESHEETPATH . '/archive.php' ) ) {                    
                        load_template( STYLESHEETPATH . '/archive.php' );                                        
                    // theme index
                    } else {                        
                        load_template( STYLESHEETPATH . '/index.php' );                                                                        
                    }                        
                } else {
                    wp_die( 'Sorry the following taxonomies: ' . print_r( $wtf['taxonomies'] ) . ' are not in my array' );
                }  
                exit;
            }      
        }
    } // End 'taxonomyRedirect'

    // Did I make one?
    // Did you make one?
    // Use MY default
    public function archiveRedirect( $current_post_type=null ) {

        if ( is_null( $current_post_type ) )
            wp_die( 'I need a CPT');

        // @todo this needs a loop for cpt's
        if ( is_post_type_archive( $current_post_type ) ) {            

            // custom plugin theme
            if ( file_exists( MY_PLUGIN_DIR . 'theme/archive-' . $current_post_type . '.php' ) ) {
                
                // @todo enqueue css/js
                load_template( MY_PLUGIN_DIR . 'theme/archive-' . $current_post_type . '.php' );
            
            // custom theme
            } elseif ( file_exists( STYLESHEETPATH . '/archive-' . $current_post_type . '.php' ) ) {
                
                load_template( STYLESHEETPATH . '/archive-' . $current_post_type . '.php' );                    
            
            // default 
            } elseif ( file_exists( MY_PLUGIN_DIR . 'theme/default/archive-default.php' ) ) {
                
                // @todo enqueue css/js
                load_template( MY_PLUGIN_DIR . 'theme/default/archive-default.php' );
                
            }
            exit;   
        }
    } // End 'archiveRedirect'

    // Did I make one?
    // Did you make one?
    // Use the default
    public function singleRedirect( $current_post_type=null ) {        
        if ( is_null( $current_post_type ) )
            wp_die( 'I need a CPT');

        // @todo this needs a loop for cpt's
        if ( is_single() ) {
            
            // custom template from plugin
            if ( file_exists( MY_PLUGIN_DIR . 'theme/single-' . $current_post_type . '.php' ) ) {
                
                // @todo enqueue css/js
                load_template( MY_PLUGIN_DIR . 'theme/single-' . $current_post_type . '.php' );
            
            // custom template from theme
            } elseif ( file_exists( STYLESHEETPATH . 'theme/single-' . $current_post_type . '.php'  ) ) {
                
                load_template( STYLESHEETPATH . 'theme/single-' . $current_post_type . '.php' );
                    
            } else {
                
                // default templte
                load_template( STYLESHEETPATH . '/single.php' );            
            
            }
         exit;
        }

    } // End 'singleRedirect'
    
} // End 'CustomPostTypeBase'

/**
 * Our class
 */
class CustomPostType extends CustomPostTypeBase { 
    
    // @todo do we need this?
    static $instance;

    public $plugin_dir = WP_PLUGIN_DIR;
    public $plugin_url = WP_PLUGIN_URL;    
    public $dependencies = array();
        
    /**
     * Every thing that is "custom" to our CPT goes here.
     */
    public function __construct() {
        //self::$instance = $this;       

        $this->plugin_dir = $this->plugin_dir . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) );
        $this->plugin_url = $this->plugin_url . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) );
        
        define( 'MY_PLUGIN_DIR', $this->plugin_dir );
        define( 'MY_PLUGIN_URL', $this->plugin_url );

        $this->dependencies['script'] = array(
            'jquery',
            'jquery-ui-core',
            'jquery-ui-dialog'
        );

        // @todo the abstract should possibly be responsible for doing this
        add_action( 'init', array( &$this, 'registerPostType' ) );        
        add_action( 'init', array( &$this, 'registerTaxonomy' ) );                            
        add_action( 'template_redirect', array( &$this, 'templateRedirect' ) );        
        
        /** @todo consider, moving the following to the abstract */
        add_action( 'wp_head', array( &$this, 'baseAjaxUrl' ) );        
        
        // @todo move to abstract
        add_action( 'wp_ajax_loadTemplate', array( &$this, 'loadTemplate' ) ); 
        
        // @todo move to abstract
        add_action( 'wp_ajax_nopriv_loadTemplate', array( &$this, 'loadTemplate' ) ); // for public use
        
        // @todo move to abstract
        add_filter( 'post_class', array( &$this, 'addPostClass' ) );
                        
        // Only our container divs are loaded, the contents is injected via ajax :)
        // @todo createDiv( $element_id=null )
        add_action( 'wp_footer', array( &$this, 'createPostTypeDiv' ) );            
        add_action( 'wp_footer', array( &$this, 'createDeleteDiv' ) );            
        
        // @todo see if we can move this to the abstract
        add_action( 'wp_ajax_postTypeSubmit', array( &$this, 'postTypeSubmit' ) );                
        add_action( 'wp_ajax_postTypeUpdate', array( &$this, 'postTypeUpdate' ) );
        add_action( 'wp_ajax_postTypeDelete', array( &$this, 'postTypeDelete' ) );
        
        register_activation_hook( __FILE__, array( &$this, 'regsiterActivation') );        
                
        // add_action( 'admin_notices', 'tt_warning' );
        
        // @todo break css into; single.css, taxonomy.css, archvie.css, base.css only load on pages that need them
        // let total cache or what ever combine your css
        wp_register_style(  'tt-base-style', $this->plugin_url . 'theme/css/style.css', '', 'all' );
        
        // this is global to our plugin
        wp_register_style(  'qtip-nightly-style', $this->plugin_url . 'library/js/qtip-nightly/jquery.qtip.min.css', '', 'all' );
        wp_register_script( 'tt-script', $this->plugin_url . 'theme/js/script.js', $this->dependencies['script'], '1.0' );        
        wp_register_script( 'qtip-nightly', $this->plugin_url . 'library/js/qtip-nightly/jquery.qtip.min.js', $this->dependencies['script'], '0.0.1' );            
        wp_register_script( 'jquery-ui-effects', $this->plugin_url . 'library/js/jquery-ui/jquery-ui-1.8.13.effects.min.js', $this->dependencies['script'], '1.8.13' );        
        
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
     * Basic post submission for use with an ajax request
     */
    public function postTypeSubmit() {
        // @todo needs to be generic for cpt
        check_ajax_referer( 'tt-ajax-forms', 'security' );
    
        if ( !is_user_logged_in() )
            return false;
    
        if ( current_user_can( 'publish_posts' ) )
            $status = 'publish';
        else
            $status = 'pending';
    
        unset( $_POST['action'] );
    
        foreach( $_POST as $k => $v )
            $_POST[$k] = esc_attr( $v );
    
        $type = $_POST['post_type'];
        $title = $_POST['post_title'];
        $content = $_POST['content'];
    
        unset( $_POST['post_title'] );
        unset( $_POST['content'] );
        unset( $_POST['post_author'] );
        unset( $_POST['post_type'] );
        unset( $_POST['security'] );
    
        $author_ID = get_current_user_id();
    
        $post = array(
            'post_title' => $title,
            'post_content' => $content,
            'post_author' => $author_ID,
            'post_type' => $type,
            'post_status' => $status
        );
        
        $post_id = wp_insert_post( $post, true );
        
        if ( is_wp_error( $post_id ) )
            return;
        
        /**
         * if insert was successful we take everything left in post and submit, yeah, should be while listed, I'm dumb or lazy
         */
        if ( !empty( $post_id ) ) {
            $taxonomies = $_POST;
            foreach( $taxonomies as $taxonomy => $term ) {
                if ( isset( $term ) )
                    wp_set_post_terms( $post_id, $term, &$taxonomy );
            }
        }
        die();
    } // End 'postTypeSubmit'

    
    /**
     * Simple form submission to be used in AJAX request!
     */
    public function postTypeUpdate( $post ) {
        // @todo add check_ajax_referer

        if ( !is_user_logged_in() )
            return false;

        if ( current_user_can( 'publish_posts' ) )
            $status = 'publish';
        else
            $status = 'pending';

        $post_id = (int)$_POST['PostID'];
        $comment = $_POST['comment'];
    
        /** What's left is our taxonomies */
        unset( $_POST['action'] );
        unset( $_POST['PostID'] );
        unset( $_POST['comment'] );
        $taxonomies = $_POST;
    
        /** insert terms */
        /** @todo should only do the insert if they change? */
        foreach( $taxonomies as $taxonomy => $term )
            wp_set_post_terms( $post_id, $term, &$taxonomy );
    
        if ( !empty( $comment ) ) {
            $current_user = wp_get_current_user();
            $time = current_time('mysql');
            $data = array(
                'comment_post_ID' => $post_id,
                'comment_author' => $current_user->user_nicename,
                'comment_author_email' => $current_user->user_email,
                'comment_author_url' => $current_user->user_url,
                'comment_content' => $comment,
                'comment_type' => '',
                'comment_parent' => 0,
                'user_id' => $current_user->ID,
                'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
                'comment_date' => $time,
                'comment_approved' => 1
                );
            wp_insert_comment( $data );
        }
        die();
    } // postTypeUpdate

    public function postTypeDelete( $id=null ) {
        // @todo needs to be generic for cpt
        check_ajax_referer( 'tt-ajax-forms', 'security' );

        $id = (int)$_POST['post_id'];

        if ( !is_user_logged_in() )
            return false;        

        if ( is_null( $id )  ) {
            wp_die( 'I need a post_id to kill!');
        } else {        

            // Yes we do what the mafia does, true == no trash, just kill the mofo
            // and everyone that stood behind him! i.e. terms, meta, att. etc.
            // if you had relations with her your dead to me!
            $result = wp_delete_post( $id, true );
            if ( is_wp_error( $result ) ) {                
                print_r( $result );
            } else {
                print_r( $result );
            }
        }

        die();
    } // postTypeDelete

    /**
     * Login set-up
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
        wp_register_style( 'tt-login-style', $this->plugin_url . 'theme/css/login.css', $dependencies['style'], 'all' );        
        wp_enqueue_script( 'jquery-ui-effects' );        
        
    } // End 'loginSetup'

    public function createLoginDiv(){ ?>
    <div id="login_dialog" class="dialog-container">
            <div id="login_target" style="display: none;">login hi</div>
        </div>
    <?php }

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
     * load our template 
     * uh, why not make it ajaxy? :D
     */
    public function loadTemplate() {

        $template = $_POST['template'];
    
        if ( $template == null )
            tt_debug( 'Yo, you need a fucking template!');
    
        load_template( MY_PLUGIN_DIR . $template );
        die();
    } // loadTemplate

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
    
    /**
     * Print our ajax url in the footer 
     * @todo baseAjaxUrl() consider moving to abstract
     */
    public function baseAjaxUrl() {
        // @todo use localize for this
        // http://www.garyc40.com/2010/03/5-tips-for-using-ajax-in-wordpress/#js-global
        print '<script type="text/javascript"> var ajaxurl = "'. admin_url("admin-ajax.php") .'"; var _pluginurl="'. MY_PLUGIN_URL.'";</script>';    
    } // End 'baseAjaxUrl'
    
} // End 'CustomPostType'

$_GLOBALS['task'] = new CustomPostType();
$_GLOBALS['task']->post_type = array(
    array(
        'name' => 'Task',
        'type' => 'task',
        'supports' => array(
            'title',
            'editor',
            'author',
            'comments'
        ),
        // @todo automate mother fuckergrrrrr
        'taxonomies' => array(
            'assigned', 
            'milestone', 
            'priority', 
            'project', 
            'status', 
            'type'
        )      
    )
);

$_GLOBALS['task']->taxonomy = array(
    array(
        'name' => 'assigned', 
        'post_type' => 'task'
        ),
    array( 
        'name' => 'milestone', 
        'post_type' => 'task'
         ),
    array( 
        'name' => 'priority', 
        'post_type' => 'task'
         ),            
    array( 
        'name' => 'project', 
        'post_type' => 'task'
         ),            
    array( 
        'name' => 'status', 
        'post_type' => 'task'
        ),            
    array( 
        'name' => 'type', 
        'post_type' => 'task'
        )
);
