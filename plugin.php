<?php
if ( is_admin() ) {
    ini_set('display_errors', 0);
    error_reporting( E_ALL );
}

/**
 * Registers custom post type "Task", ...
 */

/**
 * Plugin Name: Task Tracker
 * Plugin URI: --
 * Description: Turns WP into a Task Tracking system.
 * Version: 0.0.1
 * Author: Zane M. Kolnik
 * Author URI: http://zanematthew.com/
 * License: GP
 */

/**
 * A predetermined list of methods our class MUST implement
 */
interface ICustomPostType {
    public function registerPostType();
    public function registerTaxonomy();    
}

/**
 * Declare our methods signature
 */
abstract class CustomPostTypeBase implements ICustomPostType {
    public $plugin_url = WP_PLUGIN_URL;
    public $plugin_dir = WP_PLUGIN_DIR;
      
    public function registerPostType( $args=NULL ) {
    
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
        } // End 'foreach'             
    } // End 'function'

    public function registerTaxonomy( $args=NULL ) {
        foreach ( $this->taxonomy as $taxonomy ) {

            $taxonomy['name'] = strtolower( $taxonomy['name'] );

            if ( empty( $taxonomy['slug'] ) || empty( $taxonomy['singular_name'] ) )
                $taxonomy['slug'] =  $taxonomy['singular_name'] = $taxonomy['name'];

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
                'hierarchical' => $taxonomy['hierarchical'],
                'query_var' => true
                );

            register_taxonomy( $taxonomy['name'], $taxonomy['post_type'], $args );
        } // End 'foreach'
    } // End 'function'   

}

class CustomPostType extends CustomPostTypeBase { 
    
    static $instance;
    public $plugin_dir = WP_PLUGIN_DIR;
    public $plugin_url = WP_PLUGIN_URL;    
    public $dependencies = array();
        
    /**
     * Everything to be ran when our class is instantioned adds hooks and sh!t 
     */
    public function __construct() {
        self::$instance = $this;       
        $this->plugin_dir = $this->plugin_dir . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) );
        $this->plugin_url = $this->plugin_url . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) );
        $this->dependencies['script'] = array(
            'jquery',
            'jquery-ui-core',
            'jquery-ui-dialog'
        );

        $this->dependencies['style'] = array(
            'wp-jquery-ui-dialog'
        );
        
        add_action( 'init', array( &$this, 'registerPostType' ) );
        add_action( 'init', array( &$this, 'registerTaxonomy' ) ); 
        
        wp_register_style(  'tt-styles', $this->plugin_url . 'theme/css/style.css', $this->dependencies['style'], 'all' );
        wp_register_style(  'qtip-nightly-style', $this->plugin_url . 'library/js/qtip-nightly/jquery.qtip.min.css', '', 'all' );
        wp_register_script( 'tt-script', $this->plugin_url . 'theme/js/script.js', $this->dependencies['script'], '1.0' );        
        wp_register_script( 'jquery-ui-effects', $this->plugin_url . 'theme/js/jquery-ui-1.8.13.effects.min.js', $this->dependencies['script'], '1.8.13' );
        wp_register_script( 'qtip-nightly', $this->plugin_url . 'library/js/qtip-nightly/jquery.qtip.min.js', $this->dependencies['script'], '0.0.1' );            
    }    
}

$task = new CustomPostType();
$task->post_type = array(
    array(
        'name' => 'Task',
        'type' => 'task'
    )
);

$task->taxonomy = array(
    array(
        'name' => 'assigned', 
        'post_type' => 'task'
        ),        
    array( 
        'name' => 'phase', 
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
        ),            
    array( 
        'name' => 'ETA',
        'post_type' => 'task'
        )
    );
