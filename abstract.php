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

        wp_register_style( 'taxonomy-default-style', plugin_dir_url( __FILE__ ) . 'theme/css/taxonomy.css', $this->dependencies['style'] , 'all' );   

        foreach( $this->post_type as $wtf ) {
            
            $my_cpt = get_post_types( array( 'name' => $wtf['type']), 'objects' );                    
            
            if ( is_tax( $wtf['taxonomies'] ) ) {                                                
                if ( in_array( $wp_query->query_vars['taxonomy'], $wtf['taxonomies'] ) ) {                                        
                    if ( file_exists( plugin_dir_path( __FILE__ ) . 'theme/custom/' . $wtf['type'] . '-taxonomy.php' ) ) {                                                
                        wp_enqueue_style( 'taxonomy-default-style' );                        
                        load_template( plugin_dir_path( __FILE__ ) . 'theme/custom/' . $wtf['type'] . '-taxonomy.php' );                                        
                    } elseif ( file_exists( plugin_dir_path( __FILE__ ) . 'theme/default/taxonomy.php' ) ) {                                                                        
                        wp_enqueue_style( 'taxonomy-default-style' );
                        load_template( plugin_dir_path( __FILE__ ) . 'theme/default/taxonomy.php' );
                    } elseif ( file_exists( STYLESHEETPATH . '/archive.php' ) ) {                    
                        load_template( STYLESHEETPATH . '/archive.php' );                                                            
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
    
    // Did you make a custom one?    
    // Did I make a custom one?    
    // Use MY default
    public function archiveRedirect( $current_post_type=null ) {

        wp_register_style( 'tt-archive-style', plugin_dir_url( __FILE__ ) . 'theme/css/archive.css', $this->dependencies['style'] , 'all' );   
        wp_register_style( 'tt-archive-default-style', plugin_dir_url( __FILE__ ) . 'theme/css/archive-default.css', $this->dependencies['style'] , 'all' );   

        if ( is_null( $current_post_type ) )
            wp_die( 'I need a CPT');

        // @todo this needs a loop for cpt's
        if ( is_post_type_archive( $current_post_type ) ) {            
            if ( file_exists( STYLESHEETPATH . '/archive-' . $current_post_type . '.php' ) ) {                                
                load_template( STYLESHEETPATH . '/archive-' . $current_post_type . '.php' );                    
            } elseif ( file_exists( plugin_dir_path( __FILE__ ) . 'theme/archive-' . $current_post_type . '.php' ) ) {
                wp_enqueue_style( 'tt-archive-style' );
                load_template( plugin_dir_path( __FILE__ ) . 'theme/archive-' . $current_post_type . '.php' );                                
            } elseif ( file_exists( plugin_dir_path( __FILE__ ) . 'theme/default/archive-default.php' ) ) {                            
                wp_enqueue_style( 'tt-archive-default-style' );
                load_template( plugin_dir_path( __FILE__ ) . 'theme/default/archive-default.php' );                
            }
            exit;   
        }
    } // End 'archiveRedirect'

    /**
     * Load the single template and needed css/js based on the following hierarchy:
     *
     * wp-content/theme/[users theme]/single-[custom_post_type].php
     * wp-content/plugins/[plugin name]]/theme/single-[$]custom_post_type].php
     * wp-content/plugins/[plugin name]/default/single.php
     *
     * @param current_post_type
     */
    public function singleRedirect( $current_post_type=null ) {
        
        wp_register_style( 'tt-single-style', plugin_dir_url( __FILE__ ) . 'theme/css/single.css', $this->dependencies['style'] , 'all' );   

        if ( is_null( $current_post_type ) )
            wp_die( 'I need a CPT');
                
        if ( is_single() ) {
                        
            if ( file_exists( STYLESHEETPATH . 'theme/single-' . $current_post_type . '.php'  ) ) {                
                load_template( STYLESHEETPATH . 'theme/single-' . $current_post_type . '.php' );                                
            } elseif ( file_exists( plugin_dir_path( __FILE__ ) . 'theme/single-' . $current_post_type . '.php' ) ) {                                                
                
} // End 'CustomPostTypeBase'