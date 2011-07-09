<?php

include MY_PLUGIN_DIR . "zm_cpt/PostTypeLibrary.php";

class PostTypeController extends PostTypeLibrary {
    /** an array of custom post types */
    /** @todo need DEFAULTS! */
    public $post_type;

    /** an array of taxonomies */    
    public $taxonomy;
        
    public function __construct() {    
        add_action('init', array( &$this, 'init') );  
    }
    
    public function  __destruct() {}
    
    /** Methods to be called on initantiate */
    public function init() {

       /** 
        * If we have an taxonomy and it is an array. We call the parent method 'regsiterTaxonomy'
        * Which registers an array of taxonomies for a given CPT.
        */
        if ( !empty( $this->taxonomy ) && is_array( $this->taxonomy ) ) {
            parent::registerTaxonomy( $this->taxonomy );
        }

        /** We don't need to check this, cause isn't this the whole fucking point of the code? */
        parent::postType( $this->post_type );
                
        /** Allow CPT to show in category, tag and author archive */
        // add_filter('request', array( &$this, 'post_type_tags_fix') );        
        /** @todo wtf was this here? it breaks attachment.php */
        // add_filter('pre_get_posts', array( &$this, 'query_post_type') );
    }
    
    /**
     * Allows CPT to display in the_loop when a query is made for 
     * a category or tag.
     */
    public function query_post_type($query, $type=null) {

        global $wp_query;
        
        /** may add parameter for this later */
        $tmp = array('post');
        
        foreach ( $this->post_type as $post_type ) {
            if ( !empty( $post_type['categories'] ) || !empty( $post_type['tags'] ) ) {
                $tmp[] = $post_type['type']; 
            }
        }

        $wp_query->set( 'post_type', $tmp);        
    }
    
    /**
     * Allow usage of CPT in author archive pages
     * As of now this is DEFAULT!
     */    
    public function post_type_tags_fix( $request ) {
        
        if ( isset( $request['author_name'] ) )
            $request['post_type'] = 'any';
                
        return $request;
    }    
} // End 'PostType'

