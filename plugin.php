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
 * - Deactivation script
 * - enqueue a base stylesheet
 */
interface ICustomPostType {

    public function registerPostType( $param=array() );
    public function registerTaxonomy( $param=array() );        
    public function templateRedirect();
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
        foreach( $this->taxonomy as $tax )
            $taxonomies[] = $tax['name'];
    
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
//                'taxonomies' => array( 'assigned', 'phase', 'priority', 'project', 'status', 'type', 'ETA' ),
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

    /**
     * Determines archive template, single template, and template per taxonomy if any exisits
     * else falls back on theme tempates
     */
    public function templateRedirect() {

        $current_post_type = get_query_var( 'post_type' ); // $current_post_type

        $my_cpt = get_post_types( array( 'name' => 'task' ) ,'objects' );

        foreach( $my_cpt as $myt )
            $my_taxonomies = $myt->taxonomies;

        // Quick and harsh error checking
        if ( !isset( $this->post_type ) ) wp_die( 'Need a CPT!' );
        if ( !isset( $my_taxonomies ) ) wp_die( 'Need a CTT!' );

        wp_enqueue_style( 'qtip-nightly-style' );
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
        wp_enqueue_style( 'tt-styles' );
    
        wp_enqueue_script( 'tt-script' );
        wp_enqueue_script( 'qtip-nightly' );
        wp_enqueue_script( 'jquery-ui-effects' );

        foreach ( $this->post_type as $my_post_type => $k ) {    	
            switch( $k['type'] ) {
            case ( is_tax( $my_taxonomies ) ):
                global $wp_query;
                
                if ( in_array( $wp_query->query_vars['taxonomy'], $my_taxonomies ) ) {
                    load_template( MY_PLUGIN_DIR . 'theme/'.$k['type'].'-taxonomy.php' );
//                    foreach ( $my_taxonomies as $taxonomy ) {
//                        load_template( MY_PLUGIN_DIR . 'theme/archive-' . $k['type'] . '.php' );
//                        load_template( MY_PLUGIN_DIR . 'theme/taxonomy-' . $my_taxonomies. '.php' );
//                    }
                }
                exit;
                break;
    
            case ( is_single() ):
                // If your not my post type GTFO                
                foreach ( $this->post_type as $my_post_type )
                    if ( get_post_type() != $my_post_type['type'] ) return;
                
                // first checkout our plugin themes folder
                foreach ( $this->post_type as $my_post_type )
                    if ( file_exists( STYLESHEETPATH . '/single-' . $my_post_type['type'] . '.php' ) ) return;
    
                foreach ( $this->post_type as $my_post_type )
                    load_template( MY_PLUGIN_DIR . '/theme/single-' . $my_post_type['type'] . '.php' );
                exit;
                break;
  
            case ( is_post_type_archive( $k['type'] ) ):
                if ( file_exists( STYLESHEETPATH . '/archive-' . $k['type'] . '.php' ) ) return;
                load_template( $this->plugin_dir . '/theme/archive-' . $k['type'] . '.php' );
                exit;
                break;
            default:
                return;
            } // End 'switch'
        } // End 'foreach'
    } // End 'function templateRedirect'    
} // End 'CustomPostTypeBase'

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
        
        /** @todo consider, moving the following to the abstract */
        add_action( 'wp_head', array( &$this, 'baseAjaxUrl' ) );        
        add_action( 'wp_ajax_loadTemplate', array( &$this, 'loadTemplate' ) ); 
        add_action( 'wp_ajax_nopriv_loadTemplate', array( &$this, 'loadTemplate' ) ); 
        add_filter( 'post_class', array( &$this, 'addPostClass' ) );
                
        add_action( 'wp_footer', array( &$this, 'createPostTypeDiv' ) );            
        add_action( 'wp_ajax_postTypeSubmit', array( &$this, 'postTypeSubmit' ) );        
        add_action( 'wp_ajax_postTypeUpdate', array( &$this, 'postTypeUpdate' ) );

//        add_action( 'admin_notices', 'tt_warning' );
        
        wp_register_style(  'tt-styles', $this->plugin_url . 'theme/css/style.css', $this->dependencies['style'], 'all' );
        wp_register_style(  'qtip-nightly-style', $this->plugin_url . 'library/js/qtip-nightly/jquery.qtip.min.css', '', 'all' );
        wp_register_script( 'tt-script', $this->plugin_url . 'theme/js/script.js', $this->dependencies['script'], '1.0' );        
        wp_register_script( 'jquery-ui-effects', $this->plugin_url . 'theme/js/jquery-ui-1.8.13.effects.min.js', $this->dependencies['script'], '1.8.13' );
        wp_register_script( 'qtip-nightly', $this->plugin_url . 'library/js/qtip-nightly/jquery.qtip.min.js', $this->dependencies['script'], '0.0.1' );            
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
        $args = array(
          'public'   => true,
          '_builtin' => false  
        ); 
        $output = 'objects'; // or objects
        $tax_names = array();        
        $taxonomies = get_taxonomies( $args, $output ); 

        if ( $taxonomies ) {
          foreach ($taxonomies  as $taxonomy )
            $tax_names[] .= $taxonomy->labels->name;
        }

        foreach( $tax_names as $name ) {
            $terms = get_the_terms( $post->ID, $name );
            if ( $terms ) {
                foreach( $terms as $term )
                    $classes[] = $name . '-' . $term->term_id;
            }
        }
        return $classes;
    } // End 'addPostClass'

    /**
     * Basic post submission for use with an ajax request
     */
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
    } // End 'postTypeSubmit'
    
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
     * Print our ajax url in the footer 
     * @todo baseAjaxUrl() consider moving to abstract
     */
    public function baseAjaxUrl() {
        print '<script type="text/javascript"> var ajaxurl = "'. admin_url("admin-ajax.php") .'"; var _pluginurl="'. MY_PLUGIN_URL.'";</script>';    
    } // End 'baseAjaxUrl'
    
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
