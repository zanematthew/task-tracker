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
    }

    /**
     * Regsiter an unlimited number of CPTs based on an array of parmas.
     * 
     * Note, some args are still hard coded.
     * Full list @ http://codex.wordpress.org/Function_Reference/register_post_type     
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
                //'hierarchical' => true, // non-hierarchical taxes break alot of stuff
                'query_var' => true,
                'public' => true,
                'rewrite' => array('slug' => $taxonomy['slug']),
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'show_tagcloud' => true
                );

//            if ( $taxonomy['post_tag'] )
                // register_taxonomy_for_object_type( 'post_tag', $taxonomy['post_type'] );
                
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

    // Did I make one?
    // Use MY default?
    // Use WP archive
    // Use WP index
    public function taxonomyRedirect( $current_post_type=null ) {

        global $wp_query;

        if ( is_null( $current_post_type ) )
            wp_die( 'I need a CPT');

        wp_register_style( 'tt-taxonomy-default-style', plugin_dir_url( __FILE__ ) . 'theme/css/taxonomy.css', $this->dependencies['style'] , 'all' );   

        foreach( $this->post_type as $wtf ) {
            
            $my_cpt = get_post_types( array( 'name' => $wtf['type']), 'objects' );                    
            
            if ( is_tax( $wtf['taxonomies'] ) ) {                                
                
                if ( in_array( $wp_query->query_vars['taxonomy'], $wtf['taxonomies'] ) ) {                    
                    // custom plugin theme                    
                    if ( file_exists( MY_PLUGIN_DIR . 'theme/custom/' . $wtf['type'] . '-taxonomy.php' ) ) {                        
                                                                    
                        load_template( MY_PLUGIN_DIR . 'theme/custom/' . $wtf['type'] . '-taxonomy.php' );                    

                    // default plugin theme               
                    } elseif ( file_exists( MY_PLUGIN_DIR . 'theme/default/taxonomy.php' ) ) {                                                
                        
                        wp_enqueue_style( 'tt-taxonomy-default-style' );
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
    
    public function archiveRedirect( $current_post_type=null ) {

        wp_register_style( 'tt-archive-style', plugin_dir_url( __FILE__ ) . 'theme/css/archive.css', $this->dependencies['style'] , 'all' );   
        wp_register_style( 'tt-archive-default-style', plugin_dir_url( __FILE__ ) . 'theme/css/archive-default.css', $this->dependencies['style'] , 'all' );   

        if ( is_null( $current_post_type ) )
            wp_die( 'I need a CPT');

        // @todo this needs a loop for cpt's
        if ( is_post_type_archive( $current_post_type ) ) {            

            // Did you make a custom one?    
            if ( file_exists( STYLESHEETPATH . '/archive-' . $current_post_type . '.php' ) ) {
                                
                load_template( STYLESHEETPATH . '/archive-' . $current_post_type . '.php' );                    

            // Did I make a custom one?    
            } elseif ( file_exists( plugin_dir_path( __FILE__ ) . 'theme/archive-' . $current_post_type . '.php' ) ) {
                wp_enqueue_style( 'tt-archive-style' );
                load_template( plugin_dir_path( __FILE__ ) . 'theme/archive-' . $current_post_type . '.php' );            
            
            // Use MY default
            } elseif ( file_exists( plugin_dir_path( __FILE__ ) . 'theme/default/archive-default.php' ) ) {
                            
                wp_enqueue_style( 'tt-archive-default-style' );
                load_template( plugin_dir_path( __FILE__ ) . 'theme/default/archive-default.php' );
                
            }
            exit;   
        }
    } // End 'archiveRedirect'

    public function singleRedirect( $current_post_type=null ) {     
        
        wp_register_style( 'tt-single-style', plugin_dir_url( __FILE__ ) . 'theme/css/single.css', $this->dependencies['style'] , 'all' );   

        if ( is_null( $current_post_type ) )
            wp_die( 'I need a CPT');

        // @todo this needs a loop for cpt's
        if ( is_single() ) {
            
            // Did you make one?
            if ( file_exists( STYLESHEETPATH . 'theme/single-' . $current_post_type . '.php'  ) ) {                

                load_template( STYLESHEETPATH . 'theme/single-' . $current_post_type . '.php' );                    
            
            // Did I make one?
            } elseif ( file_exists( plugin_dir_path( __FILE__ ) . 'theme/single-' . $current_post_type . '.php' ) ) {                
                                
                wp_enqueue_style( 'tt-single-style' );

                if ( current_user_can( 'editor' ) )
                    wp_enqueue_script( 'inplace-edit-script' );
                    wp_enqueue_style( 'inplace-edit-style' );

                    load_template( plugin_dir_path( __FILE__ ) . 'theme/single-' . $current_post_type . '.php' );
            
            // Use the the curent themes single template
            } else {                                

                load_template( STYLESHEETPATH . '/single.php' );                        

            }
         exit;
        }
    } // End 'singleRedirect'

    /** 
     * load our template 
     * uh, why not make it ajaxy? :D
     */
    public function loadTemplate() {

        $template = $_POST['template'];
    
        if ( $template == null )
            wp_die( 'Yo, you need a fucking template!');
    
        load_template( plugin_dir_path( __FILE__ ) . $template );
        die();
    } // loadTemplate
    
    /**
     * Basic post submission for use with an ajax request
     */
    public function postTypeSubmit() {

        // @todo needs to be generic for cpt
        check_ajax_referer( 'tt-ajax-forms', 'security' );
    
        if ( !is_user_logged_in() )
            return false;
    
        $error = null;        

        if ( empty( $_POST['post_title'] ) ) {
            $error .= '<div class="message">Please enter a fucking <em>title</em>.</div>';
        }

        if ( empty( $_POST['content'] ) ) {
            $error .= '<div class="message">Please enter a some fucking <em>content</em>.</div>';
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
            'post_status' => $status
        );

        $post_id = wp_insert_post( $post, true );
        
        // Clean up our error message and return it
        // This will be shown in the alert msg via js.
        if ( is_wp_error( $post_id ) ) { 
            //print_r( $post_id ); 
            print_r( $post_id->get_error_message() );              
            print_r( $post_id->get_error_messages() );              
            print_r( $post_id->get_error_data() );              

            //print $post_id->errors['empty_content'][0];     
        }            
        
        /**
         * if insert was successful we take everything left in post and submit, yeah, should be while listed, I'm dumb or lazy
         */
        if ( !empty( $post_id ) ) {
            foreach( $taxonomies as $taxonomy => $term ) {
                if ( isset( $term ) )
                    wp_set_post_terms( $post_id, $term, $taxonomy );
            }
        }
        die();
    } // End 'postTypeSubmit'

    /**
     * Simple form submission to be used in AJAX request!0
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

    public function defaultUtilityUpdate(){

        if ( !is_user_logged_in() )
            return false;

        if ( current_user_can( 'publish_posts' ) )
            $status = 'publish';
        else
            $status = 'pending';

        $post_id = (int)$_POST['PostID'];    

        /** What's left is our taxonomies */
        unset( $_POST['action'] );
        unset( $_POST['PostID'] );
        
        $taxonomies = $_POST;

        // add check to see if terms are new
        foreach( $taxonomies as $taxonomy => $term ) {
            wp_set_post_terms( $post_id, $term, $taxonomy );
            //$new_terms[]['term'] = get_term_by( 'id', $term, &$taxonomy );
        }
                   
        die();
    } // entryUtilityUpdate

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
     * @todo baseAjaxUrl() consider moving to abstract
     */
    public function baseAjaxUrl() {
        // @todo use localize for this
        // http://www.garyc40.com/2010/03/5-tips-for-using-ajax-in-wordpress/#js-global
        print '<script type="text/javascript"> var ajaxurl = "'. admin_url("admin-ajax.php") .'"; var _pluginurl="'. plugin_dir_url( __FILE__ ) .'";</script>';    
    } // End 'baseAjaxUrl'
    
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

} // End 'CustomPostTypeBase'