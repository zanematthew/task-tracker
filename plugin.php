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
/** @todo make OO Procedural code to make generating forms via CPTs and CTTs easier */
require_once 'wordpress-helper-functions.php';
require_once 'functions.php';

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
                'capability_type' => 'post',                
                'supports' => $supports,
                'rewrite' => array( 'slug' => $post_type['slug'] ),
                'hierarchical' => true,
                'description' => 'Photo galleries',
        //        'taxonomies' => array( 'assigned', 'phase', 'priority', 'project', 'status', 'type', 'ETA' ),
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
                'new_item_name'     => __( 'New ' . $taxonomy['singular_name'] . ' Name' )
                );

            $args = array(
                'labels'  => $labels,
                'rewrite' => array('slug' => $taxonomy['slug']),
                'hierarchical' => $taxonomy['hierarchical'],
                'query_var' => true
                );
            register_taxonomy( $taxonomy['name'], $taxonomy['post_type'], $args );
        } // End 'foreach'
       
    return $this->taxonomy;
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
        
        define( 'MY_PLUGIN_DIR', $this->plugin_dir );
        define( 'MY_PLUGIN_URL', $this->plugin_url );

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
        add_action( 'template_redirect', array( &$this, 'templateRedirect' ) );        
        add_action( 'wp_head', array( &$this, 'baseAjaxUrl' ) );        
        add_action( 'wp_footer', array( &$this, 'createPostTypeDiv' ) );        
        add_action( 'wp_ajax_loadTemplate', array( &$this, 'loadTemplate' ) ); // Load our create task form
        add_action( 'wp_ajax_nopriv_loadTemplate', array( &$this, 'loadTemplate' ) ); // For users that are not logged in.        
        add_action( 'wp_ajax_postTypeSubmit', array( &$this, 'postTypeSubmit' ) );        
        add_action( 'wp_ajax_postTypeUpdate', array( &$this, 'postTypeUpdate' ) );

//        add_action( 'admin_notices', 'tt_warning' );
        add_filter( 'post_class', array( &$this, 'addPostClass' ) );
        
        wp_register_style(  'tt-styles', $this->plugin_url . 'theme/css/style.css', $this->dependencies['style'], 'all' );
        wp_register_style(  'qtip-nightly-style', $this->plugin_url . 'library/js/qtip-nightly/jquery.qtip.min.css', '', 'all' );
        wp_register_script( 'tt-script', $this->plugin_url . 'theme/js/script.js', $this->dependencies['script'], '1.0' );        
        wp_register_script( 'jquery-ui-effects', $this->plugin_url . 'theme/js/jquery-ui-1.8.13.effects.min.js', $this->dependencies['script'], '1.8.13' );
        wp_register_script( 'qtip-nightly', $this->plugin_url . 'library/js/qtip-nightly/jquery.qtip.min.js', $this->dependencies['script'], '0.0.1' );            
    }
    
    /**
     * Add additional classes to post_class()
     */
    public function addPostClass( $classes ) {
        global $post;
        $args = array(
          'public'   => true,
          '_builtin' => false  
        ); 
        $output = 'objects'; // or objects
        $tax_names = array();        
        $taxonomies = get_taxonomies( $args, $output ); 

        if ( $taxonomies ) {
          foreach ($taxonomies  as $taxonomy ) {
            $tax_names[] .= $taxonomy->labels->name;
          }
        }
        // $taxonomies = array( 'status', 'project', 'priority' );

        foreach( $tax_names as $name ) {
            $terms = get_the_terms( $post->ID, $name );
            if ( $terms ) {
                foreach( $terms as $term )
                    $classes[] = $name . '-' . $term->term_id;
            }
        }
        print_r( $classes );
die('array of classes does NOT need to be indexed!' );        
        return $classes;
    }

    public function postTypeSubmit() {
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
    
        $author_ID = get_current_user_id();
    
        $post = array(
            'post_title' => $title,
            'post_content' => $content,
            'post_author' => $author_ID,
            'post_type' => $type,
            'post_status' => $status
        );
        /** insert our post */
        $post_id = wp_insert_post( $post, true );
    
        if ( is_wp_error( $post_id ) )
            return;
    
        if ( !empty( $post_id ) ) {
            $taxonomies = $_POST;
            foreach( $taxonomies as $taxonomy => $term ) {
                if ( isset( $term ) )
                    wp_set_post_terms( $post_id, $term, &$taxonomy );
            }
        }
        die();
    }
    
    public function postTypeUpdate( $post ) {
    
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
    }    
    
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
    }

    public function createPostTypeDiv(){
        print '<div id="create_ticket_dialog" class="dialog-container"><div id="create_ticket_target" style="display: none;">hi</div></div>';
    }
    
    /**
     * zm_base_ajaxurl() Print our ajax url in the footer 
     */
    public function baseAjaxUrl() {
        print '<script type="text/javascript"> var ajaxurl = "'. admin_url("admin-ajax.php") .'"; var _pluginurl="'. MY_PLUGIN_URL.'";</script>';    
    }
    
    public function templateRedirect() {

        $current_post_type = get_query_var( 'post_type' ); // $current_post_type

        $my_taxonomies = array( 'status', 'priority', 'project', 'phase', 'assigned' ); // same as above

        // Quick and harsh error checking
        if ( !isset( $this->post_type ) ) die( 'Need a CPT!' );
        if ( !isset( $my_taxonomies ) ) die( 'Need a CTT!' );
    
        wp_enqueue_style( 'qtip-nightly-style' );
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
        wp_enqueue_style( 'tt-styles' );
    
        wp_enqueue_script( 'tt-script' );
        wp_enqueue_script( 'qtip-nightly' );
        wp_enqueue_script( 'jquery-ui-effects' );

        foreach ( $this->post_type as $my_post_type => $k ) {    	
        switch( $k['type'] ) {
            // Are we viewing a taxonomy page?
            case ( is_tax( $my_taxonomies ) ):
die('tax');
                global $wp_query;
    
                if ( in_array( $wp_query->query_vars['taxonomy'], $my_taxonomies ) )
                    load_template( MY_PLUGIN_DIR . 'theme/archive-' . $this->post_type . '.php' );
                exit;
                break;
    
            // Is this a single page
            case ( is_single() ):
                // If your not my post type GTFO
die('single');
                if ( get_post_type() != $this->post_type ) return;
                if ( file_exists( STYLESHEETPATH . '/single-' . $my_post_type . '.php' ) ) return;
    
                load_template( MY_PLUGIN_DIR . '/theme/single-' . $my_post_type . '.php' );
                exit;
                break;
  
            // Is this a post type archive page
            case ( is_post_type_archive( 'task' ) ):
                if ( file_exists( STYLESHEETPATH . '/archive-' . $k['type'] . '.php' ) ) return;
                load_template( $this->plugin_dir . '/theme/archive-' . $k['type'] . '.php' );
                exit;
                break;
            default:
                return;
        } // End 'switch'
    } // End 'foreach'
    } // End 'function templateRedirect'
    
} // End 'CustomPostType'

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
