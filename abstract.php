<?php
/** 
 * 
 * This is used to regsiter a custom post type, custom taxonomy and provide template redirecting.
 * 
 * This abstract class defines some base functions for using Custom Post Types. You should not have to
 * edit this abstract, only add additional methods if need be. You must use what is provided for you
 * in the interface.
 *
 */
abstract class CustomPostTypeBase implements ICustomPostType {
    
    public function __construct() {
        add_filter( 'post_class', array( &$this, 'addPostClass' ) );        
        add_action( 'wp_ajax_postTypeSubmit', array( &$this, 'postTypeSubmit' ) );                
        add_action( 'wp_ajax_postTypeUpdate', array( &$this, 'postTypeUpdate' ) );
        add_action( 'wp_ajax_postTypeDelete', array( &$this, 'postTypeDelete' ) );
        add_action( 'wp_ajax_defaultUtilityUpdate', array( &$this, 'defaultUtilityUpdate' ) );                
        add_action( 'wp_ajax_addComment', array( &$this, 'addComment' ) );        
        add_action( 'wp_ajax_nopriv_loadTemplate', array( &$this, 'loadTemplate' ) );
        add_action( 'wp_ajax_loadTemplate', array( &$this, 'loadTemplate' ) );         
        add_action( 'wp_head', array( &$this, 'baseAjaxUrl' ) );                    
        add_action( 'template_redirect', array( &$this, 'templateRedirect' ) );            
    }

    /**
     * Regsiter an unlimited number of CPTs based on an array of parmas.
     * 
     * @uses register_post_type()
     * @uses wp_die()
     *
     * @todo re-map more stuff, current NOT ALL the args are params
     */
    public function registerPostType( $args=NULL ) {
        $taxonomies = $supports = array();

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

            foreach ( $post_type['supports'] as $temp ) {

                if ( in_array( $temp, $white_list['supports'] ) ) {
                    array_push( $supports, $temp );
                } else {
                    wp_die('gtfo with this sh!t: <b>' . $temp . '</b> it ain\'t in my white list mofo!' );
                }
            }

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

    /**
     * Wrapper for register_taxonomy() to register an unlimited
     * number of taxonomies for a given CPT.
     *     
     * @uses register_taxonomy
     *
     * @todo re-map more stuff, current NOT ALL the args are params
     * @todo this 'hierarchical' => false' fucks up on wp_set_post_terms() for submitting and updating a cpt
     */
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

            if ( !isset( $taxonomy['hierarchical'] ) ) {
                $taxonomy['hierarchical'] = true;
            }

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

            $args = array(               
                'labels'  => $labels,
                'hierarchical' => $taxonomy['hierarchical'],
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
     * Get somethings and do a little bit of thinking before calling the redirect methods.
     * @package Template Redirect
     *
     * @uses wp_enqueue_style()
     * @uses wp_enqueue_script()
     * @uses is_admin()
     * @uses get_query_var()
     * @uses $this->singleRedirect()
     * @uses $this->taxonomyRedirect()
     * @uses $this->archiveRedirect()
     */
    public function templateRedirect() {
        
        if ( !is_admin() ) {
            wp_enqueue_style( 'qtip-nightly-style' );
            wp_enqueue_style( 'tt-base-style' );
            wp_enqueue_script( 'tt-script' );
            wp_enqueue_script( 'qtip-nightly' );
            wp_enqueue_script( 'jquery-ui-effects' );
        }

        $current_post_type = get_query_var( 'post_type' );
        
        $this->singleRedirect( $current_post_type );
        $this->taxonomyRedirect( $current_post_type );
        $this->archiveRedirect( $current_post_type );
    } // End 'function templateRedirect'


    /**
     * Load the taxonomy template, css, js files and allow the users theme folder
     * override our default theme.
     *
     * This should work for an unlimited number of custom post types. If the user
     * made a custom tempalte in their theme folder load that, if not we load our
     * custom template, and fall back on the default template.
     *
     * @param current_post_type
     * @package Template Redirect     
     *
     * @uses wp_die()
     * @uses wp_register_style()
     * @uses wp_enqueue_style()
     * @uses plugin_dir_url()
     * @uses plugin_dir_path()
     * @uses get_post_types()
     * @uses is_tax()
     * @uses load_template()     
     */
    public function taxonomyRedirect( $current_post_type=null ) {

        global $wp_query;

        if ( is_null( $current_post_type ) )
            wp_die( 'I need a CPT');

        wp_register_style( 'taxonomy-default-style', plugin_dir_url( __FILE__ ) . 'theme/css/taxonomy.css', $this->dependencies['style'] , 'all' );   

        foreach( $this->post_type as $wtf ) {
            
            $my_cpt = get_post_types( array( 'name' => $wtf['type']), 'objects' );                    
            
            $custom_template  = plugin_dir_path( __FILE__ ) . 'theme/custom/' . $wtf['type'] . '-taxonomy.php';
            $default_template = plugin_dir_path( __FILE__ ) . 'theme/default/taxonomy.php';
            $theme_template   = STYLESHEETPATH . '/index.php';

            if ( is_tax( $wtf['taxonomies'] ) ) {                                                

                if ( in_array( $wp_query->query_vars['taxonomy'], $wtf['taxonomies'] ) ) {
                
                    if ( file_exists( $custom_template ) ) {                        
                        load_template( $custom_template );
                    }
                    
                    elseif ( file_exists( $default_template ) ) {                                                                        
                        wp_enqueue_style( 'taxonomy-default-style' );                        
                        load_template( $default_template );
                    } 

                    else {                                  
                        wp_enqueue_style( 'taxonomy-default-style' );
                        load_template( $default_template );
                    } 

                } else {
                    wp_die( 'Sorry the following taxonomies: ' . print_r( $wtf['taxonomies'] ) . ' are not in my array' );
                }  
                exit;
            }      
        }
    } // End 'taxonomyRedirect'
    
    /**
     * Load the archive template for a given post type.
     *
     * We check in the users current theme folder first in:
     * wp-content/theme/[user theme]/archive-[post type].php then
     * in our: wp-content/plugin/[plugin name]/theme/custom/arcvhie-[post type].php
     * if none of those exisits load: wp-content/plugin/[plugin name]/theme/default/arcvhie.php
     *
     * @package Template Redirect
     *
     * @uses wp_register_style()
     * @uses wp_enqueue_style()
     * @uses wp_die()
     * @uses plugin_dir_url()
     * @uses plugin_dir_path()
     * @uses is_post_type_archive()
     * @uses load_template()
     */
    public function archiveRedirect( $current_post_type=null ) {

        wp_register_style( 'tt-archive-style', plugin_dir_url( __FILE__ ) . 'theme/css/archive.css', $this->dependencies['style'] , 'all' );   
        wp_register_style( 'tt-archive-default-style', plugin_dir_url( __FILE__ ) . 'theme/css/archive-default.css', $this->dependencies['style'] , 'all' );   

        if ( is_null( $current_post_type ) ) {
            wp_die( 'I need a CPT');        
        }

        $default_template = plugin_dir_path( __FILE__ ) . 'theme/default/archive.php';
        $custom_template  = plugin_dir_path( __FILE__ ) . 'theme/custom/archive-' . $current_post_type . '.php';
        $theme_template   = STYLESHEETPATH . '/archive-' . $current_post_type . '.php';

        // @todo this will need a loop to handle multiple cpt's
        if ( is_post_type_archive( $current_post_type ) ) {            

            if ( file_exists( $theme_template ) ) {                
                load_template( $theme_template );
            } 

            elseif ( file_exists( $custom_template ) ) {
                wp_enqueue_style( 'tt-archive-style' );
                load_template( $custom_template );
            } 

            elseif ( file_exists( $default_template ) ) {
                wp_enqueue_style( 'tt-archive-default-style' );
                load_template( $default_template );
            }

            else {
                print '<p>Default: ' . $default_template . '</p>';
                print '<p>Custom: ' . $custom_template . '</p>';
                print '<p>Theme: ' . $theme_template . '</p>';
                wp_die('Unable to load any template');
            }

            exit;   
        }
    } // End 'archiveRedirect'

    /**
     * Load the single template and needed css/js allowing the template to be overriden
     * via the users theme folder.
     *
     * If we do NOT have a "current post type", just load the default single.php 
     * from the users custom theme folder. If we do have one we do some checking, 
     * first we check to see if we (plugin developer) have a custom template in:
     * /custom/single-[custom post type].php if we do load that if we don't use 
     * the default in /default/single.php     
     *
     * wp-content/theme/[users theme]/single-[custom_post_type].php
     * wp-content/plugins/[plugin name]]/theme/single-[$]custom_post_type].php
     * wp-content/plugins/[plugin name]/default/single.php
     *
     * @package Template Redirect
     *
     * @param current_post_type
     *
     * @uses is_single()
     * @uses load_template()
     * @uses wp_register_style()
     * @uses wp_enqueue_style()
     * @uses wp_enqueue_script()
     * @uses plugin_dir_url()
     * @uses plugin_dir_path()
     * @uses current_user_can()     
     */
    public function singleRedirect( $current_post_type=null ) {

        if ( is_single() ) {
            if ( is_null( $current_post_type ) || empty( $current_post_type ) ) {
                load_template( STYLESHEETPATH . '/single.php' );
                exit;
            }

            wp_register_style( 'tt-single-style', plugin_dir_url( __FILE__ ) . 'theme/css/single.css', $this->dependencies['style'] , 'all' );   
            
            $default_template = plugin_dir_path( __FILE__ ) . 'theme/default/single.php';
            $custom_template  = plugin_dir_path( __FILE__ ) . 'theme/custom/single-' . $current_post_type . '.php';
            $theme_template   = STYLESHEETPATH . 'theme/single-' . $current_post_type . '.php';
                            
            if ( file_exists( $theme_template ) ) {                
                
                load_template( $theme_template );          

            } elseif ( file_exists( $custom_template ) ) {

                wp_enqueue_style( 'tt-single-style' );

                if ( current_user_can( 'publish_posts' ) ) {
                    wp_enqueue_script( 'inplace-edit-script' );
                    wp_enqueue_style( 'inplace-edit-style' );
                }
                
                load_template( $custom_template );
                        
            } else {                                
                wp_enqueue_style( 'tt-single-style' );

                if ( current_user_can( 'publish_posts' ) ) {
                    wp_enqueue_script( 'inplace-edit-script' );
                    wp_enqueue_style( 'inplace-edit-style' );
                }
                
                load_template( $default_template );
            }
         exit;
        }
    } // End 'singleRedirect'

    /** 
     * loads a template from a specificed path
     *
     * @package Ajax
     *
     * @uses load_template()
     */
    public function loadTemplate() {

        $template = $_POST['template'];
    
        if ( $template == null )
            wp_die( 'Yo, you need a template!');
    
        load_template( plugin_dir_path( __FILE__ ) . $template );
        die();
    } // loadTemplate
    
    /**
     * Basic post submission for use with an ajax request
     *
     * @package Ajax
     *
     * @uses wp_insert_post();
     * @uses get_current_user_id()
     * @uses is_user_logged_in()
     * @uses is_wp_error()
     * @uses check_ajax_referer()     
     */
    public function postTypeSubmit() {

        // @todo needs to be generic for cpt
        check_ajax_referer( 'tt-ajax-forms', 'security' );
    
        if ( !is_user_logged_in() )
            return false;
    
        $error = null;        

        if ( empty( $_POST['post_title'] ) ) {
            $error .= '<div class="message">Please enter a <em>title</em>.</div>';
        }

        if ( empty( $_POST['content'] ) ) {
            $error .= '<div class="message">Please enter a some <em>content</em>.</div>';
        }

        if ( !is_null( $error ) ) {
            print '<div class="error-container">' . $error . '</div>';
            exit;
        }

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
        
        $taxonomies = $_POST;

        $author_ID = get_current_user_id();
    
        $post = array(
            'post_title' => $title,
            'post_content' => $content,
            'post_author' => $author_ID,            
            'post_type' => $type,
            'post_status' => $status,
            'tax_input' => $_POST
        );

        $post_id = wp_insert_post( $post, true );
        
        if ( is_wp_error( $post_id ) ) {         
            print_r( $post_id->get_error_message() );              
            print_r( $post_id->get_error_messages() );              
            print_r( $post_id->get_error_data() );                      
        } else {            
            print '<div class="success-container"><div class="message">Your content was successfully <strong>Saved</strong></div></div>';
        }
        die();
    } // End 'postTypeSubmit'

    /**
     * Update a Post using the Current Users ID
     *
     * @package Ajax
     *
     * @uses wp_update_post()
     * @uses wp_get_current_user()
     * @uses is_user_logged_in()
     * @uses current_user_can()
     *
     * @todo add check_ajax_refere()
     */
    public function postTypeUpdate( $post ) {
        
        // @todo add check_ajax_referer
        if ( !is_user_logged_in() )
            return false;

        if ( current_user_can( 'publish_posts' ) )
            $status = 'publish';
        else
            $status = 'pending';

        unset( $_POST['action'] );
                
        // @todo validateWhiteList( $white_list, $data )

        $current_user = wp_get_current_user();                
        $_POST['post_author'] = $current_user->ID;
        $_POST['post_modified'] = current_time('mysql');                

        $update = wp_update_post( $_POST );

        die();
    } // postTypeUpdate

    /**
     * Inserts a comment for the current post if the user is logged in
     *
     * @package Ajax
     *
     * @uses is_user_logged_in()
     * @uses wp_insert_comment()
     * @uses wp_get_current_user()
     * @uses current_time()
     *
     * @todo add check_ajax_refer()
     */
    public function addComment() {
        
        if ( !is_user_logged_in() )
            return false;
        
        if ( !empty( $_POST['comment'] ) ) {

            $current_user = wp_get_current_user();
            
            $post_id = (int)$_POST['post_id'];

            $time = current_time('mysql');
            $data = array(
                'comment_post_ID' => $post_id,
                'comment_author' => $current_user->user_nicename,
                'comment_author_email' => $current_user->user_email,
                'comment_author_url' => $current_user->user_url,
                'comment_content' => $_POST['comment'],
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
    } // End 'commentAdd'

    /**
     * Updates the 'utiltily', i.e. taxonomies, of a post
     *
     * @package Ajax
     *
     * @param (int)post id, (array)taxonomies
     *
     * @uses is_user_logged_in()
     * @uses current_user_can()
     * @uses wp_set_post_terms()
     *
     * @todo add chcek_ajax_refer()
     */
    public function defaultUtilityUpdate( $post_id=null, $taxonomies=null) {

        if ( !is_user_logged_in() )
            return false;

        if ( current_user_can( 'publish_posts' ) )
            $status = 'publish';
        else
            $status = 'pending';

        $post_id = (int)$_POST['PostID'];    

        unset( $_POST['action'] );
        unset( $_POST['PostID'] );
        
        $taxonomies = $_POST;

        foreach( $taxonomies as $taxonomy => $term ) {
            wp_set_post_terms( $post_id, $term, $taxonomy );
            // add check to see if terms are new
            //$new_terms[]['term'] = get_term_by( 'id', $term, &$taxonomy );
        }
                   
        die();
    } // entryUtilityUpdate

    /**
     * Delets a post given the post ID, post will be moved to the trash
     *
     * @package Ajax
     *
     * @param (int) post id
     *
     * @uses check_ajax_referer
     * @uses is_wp_error
     * @uses is_user_logged_in
     * @uses wp_trash_post     
     *
     * @todo generic validateUser method, check ajax refer and if user can (?)     
     */
    public function postTypeDelete( $id=null ) {
        
        check_ajax_referer( 'tt-ajax-forms', 'security' );

        $id = (int)$_POST['post_id'];

        if ( !is_user_logged_in() )
            return false;        

        if ( is_null( $id )  ) {
            wp_die( 'I need a post_id to kill!');
        } else {        
            $result = wp_trash_post( $id );
            if ( is_wp_error( $result ) ) {                
                print_r( $result );
            } else {
                print_r( $result );
            }
        }

        die();
    } // postTypeDelete

    /**
     * Print our ajax url in the footer 
     *
     * @uses plugin_dir_url()
     * @uses admin_url()
     *
     * @todo baseAjaxUrl() consider moving to abstract
     * @todo consider using localize script     
     */
    public function baseAjaxUrl() {
        // @todo use localize for this
        // http://www.garyc40.com/2010/03/5-tips-for-using-ajax-in-wordpress/#js-global
        print '<script type="text/javascript"> var ajaxurl = "'. admin_url("admin-ajax.php") .'"; var _pluginurl="'. plugin_dir_url( __FILE__ ) .'";</script>';    
    } // End 'baseAjaxUrl'
    
    /**
     * Adds additional classes to post_class() for additional CSS styling and JavaScript manipulation.     
     * term_slug-taxonomy_id
     *
     * @param classes
     *
     * @uses get_post_types()
     * @uses get_the_terms()
     * @uses is_wp_error()
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
} // End 'CustomPostTypeBase'