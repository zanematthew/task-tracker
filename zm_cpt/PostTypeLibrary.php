<?php
/**
 * This class is designed to be an interface for the WordPress function register_post_type(). 
 *
 * It allows the development of multiple custom post types to be done with a multi-dimensional array. 
 * Any 'hooks', 'filters' or anything of that nature belongs in the 'controller/' dir. Again this 
 * is only designed to be a simple interface.
 *
 * Codex: http://codex.wordpress.org/Function_Reference/register_post_type
 *      
 */
abstract class PostTypeLibrary {
        
    public function postType($args=NULL) {

        foreach ( $this->post_type as $post_type ) {
        
            $post_type['type'] = strtolower( $post_type['type'] );
            
            /** */
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

            /** Full list @ http://codex.wordpress.org/Function_Reference/register_post_type */            
            /** @todo make these optional */
            $supports = array(
                'title',
                'editor',
                'author',
                'thumbnail',
                'excerpt',
                'comments',
                'custom-fields',
                'trackbacks'
                );

            $capabilities = array(
                'edit_article'
                );
            
            $args = array(
                'labels' => $labels,
                'public' => true,
                // 'capability_type' => 'post',
                // 'capabilities' => $capabilities,
                'supports' => $supports,
                'rewrite' => array( 'slug' => $post_type['slug'] )
                );
            
            register_post_type( $post_type['type'], $args);

            if ( !empty( $post_type['categories'] ) )
                $this->regsiterTaxonomyType( 'category', $post_type['type'] );

            if ( !empty( $post_type['tags'] ) )
                $this->regsiterTaxonomyType( 'post_tag', $post_type['type'] );
            
            if ( !empty( $post_type['widget_support'] ) ) {
                // print 'widget support, huh';
                // add_action( 'widgets_init', create_function('', 'register_widget( "WidgetController" );' ) );                    
            }
        }    
    }
    
    /**
     * Regsiters a taxonomy for a given post type 
     *
     * @param string $taxonomy
     * @param string $post_type
     */
    public function regsiterTaxonomyType( $taxonomy, $post_type ) {
        register_taxonomy_for_object_type( $taxonomy, $post_type );    
    }    

    /**
     * Regsiter's X number of custom taxonomies for a given post type.
     * See codex: http://codex.wordpress.org/Function_Reference/register_taxonomy
     */       
    public function registerTaxonomy($args=NULL) {                        
        foreach ( $this->taxonomy as $taxonomy ) {
        
            $taxonomy['name'] = strtolower( $taxonomy['name'] );
            
            if ( empty( $taxonomy['slug'] ) || empty( $taxonomy['singular_name'] ) )
                $taxonomy['slug'] =  $taxonomy['singular_name'] = $taxonomy['name'];
                
            /** Add an 's' to make things plural */
            if ( empty( $taxonomy['plural_name'] ) )
                $taxonomy['plural_name'] = $taxonomy['name'] . 's'; 
            
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
                'new_item_name'     => __( 'New ' . $taxonomy['singular_name'] . ' Name' ),
                ); 	
    
            $args = array(
                'labels'  => $labels,
                'rewrite' => array('slug' => $taxonomy['slug']),
                'hierarchical' => $taxonomy['hierarchical']
                );   
                
            register_taxonomy( $taxonomy['name'], $taxonomy['post_type'], $args );
        } // End 'if'
    } // End 'foreach'
} // End 'PostType'
