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
define( 'MY_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) );
define( 'MY_PLUGIN_URL', WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) );

require_once 'tt_functions.php';

/** @todo make OO Procedural code to make generating forms via CPTs and CTTs easier */
require_once 'wordpress-helper-functions.php';

add_action( 'init', 'tt_init' );

register_activation_hook( __FILE__ , 'tt_activation' );

// Registers: CPT, CTT, JS, CSS and adds the needed actions
function tt_init() {
// tt_activation();
// $boo = current_user_can( 'publish_posts' );
// var_dump( $boo );
// die();
    register_cpt_task();
    register_taxonomy_assigned();
    register_taxonomy_priority();
    register_taxonomy_phase();
    register_taxonomy_project();
    register_taxonomy_status();     
    register_taxonomy_ETA();     
// @todo type tax
    
    $dependencies_js = array(
        'jquery',
        'jquery-ui-core',
        'jquery-ui-dialog'
    );

    $dependencies_css = array( 
        'wp-jquery-ui-dialog'
     );

    wp_register_style(  'tt-styles', MY_PLUGIN_URL . 'theme/css/style.css', $dependencies_css, 'all' );
    wp_register_style(  'qtip-nightly-style', MY_PLUGIN_URL . 'library/js/qtip-nightly/jquery.qtip.min.css', '', 'all' );

    wp_register_script( 'tt-script', MY_PLUGIN_URL .'theme/js/script.js', $dependencies_js, '1.0' );
    wp_register_script( 'jquery-ui-effects', MY_PLUGIN_URL . 'theme/js/jquery-ui-1.8.13.effects.min.js', $dependencies_js, '1.8.13' );
    wp_register_script( 'qtip-nightly', MY_PLUGIN_URL . 'library/js/qtip-nightly/jquery.qtip.min.js', $dependencies_js, '0.0.1' );

    add_action( 'wp_head', 'zm_base_ajaxurl' ); 
    add_action( 'wp_footer', 'tt_create_task_div' );
    add_action( 'wp_ajax_tt_load_template', 'tt_load_template' ); // Load our create task form
    add_action( 'wp_ajax_nopriv_tt_load_template', 'tt_load_template' ); // For users that are not logged in.
    add_action( 'wp_ajax_project_submit_task', 'project_submit_task' );
    add_action( 'wp_ajax_project_wp_update_post', 'project_wp_update_post' );    
    add_action( 'admin_notices', 'tt_warning' );
    add_action('template_redirect', 'tt_template', 5);
	
	add_filter( 'post_class', 'tt_post_class_add' );
}

/**
 * Add additional classes to post_class()
 */
/** @todo tt_post_class_add() $taxonomies needs to come from Obj->taxonomies */ 
function tt_post_class_add( $classes ) {
    global $post;
    $taxonomies = array( 'status', 'project', 'priority' );

    foreach( $taxonomies as $taxonomy ) {
        $terms = get_the_terms( $post->ID, $taxonomy );
        if ( $terms ) {
            foreach( $terms as $term )
                $classes[] = $taxonomy . '-' . $term->term_id;
        }
    }
    return $classes;
}

function tt_create_task_div() {
    $html = '<div id="create_ticket_dialog" class="dialog-container">';
    $html .= '<div id="create_ticket_target" style="display: none;">hi</div>';
    $html .= '</div>';
    print $html;
}

function tt_debug( $message=null ) {
    $debug = debug_backtrace();
    $html = null;
    print '<pre>Message: ' . $message . '<br />File: ' . $debug[0]["file"]. '<br />line: ' . $debug[0]["line"] . '</pre>';
    print '<pre>';
    debug_print_backtrace();
    print '</pre>';
    die();
}   
             
/** 
 * load our template 
 * uh, why not make it ajaxy? :D
 */
function tt_load_template() {
    $template = $_POST['template'];

    if ( $template == null ) 
        tt_debug( 'Yo, you need a fucking template!');

    load_template( MY_PLUGIN_DIR . $template );
    die();
}

/**
 * zm_base_ajaxurl() Print our ajax url in the footer 
 */
function zm_base_ajaxurl() {
    print '<script type="text/javascript"> var ajaxurl = "'. admin_url("admin-ajax.php") .'"; var _pluginurl="'. MY_PLUGIN_URL.'";</script>';
}
                  
/** @todo tt_template() param $my_taxonomies(array) Obj->taxonomies */
function tt_template() {
    $post_type = get_query_var( 'post_type' ); // $current_post_type
    $my_post_type = 'task'; // consider making a define
    $my_taxonomies = array( 'status', 'priority', 'project', 'phase', 'assigned' ); // same as above

    // Quick and harsh error checking
    if ( !isset( $my_post_type ) ) die( 'Need a CPT!' );
    if ( !isset( $my_taxonomies ) ) die( 'Need a CTT!' );

    wp_enqueue_style( 'qtip-nightly-style' );
    wp_enqueue_style( 'wp-jquery-ui-dialog' );
    wp_enqueue_style( 'tt-styles' );

    wp_enqueue_script( 'tt-script' );
    wp_enqueue_script( 'qtip-nightly' );
    wp_enqueue_script( 'jquery-ui-effects' );

    switch( isset( $post_type ) ) {
        // Are we viewing a taxonomy page?
        case ( is_tax( $my_taxonomies ) ):
            global $wp_query;

            if ( in_array( $wp_query->query_vars['taxonomy'], $my_taxonomies ) )
                load_template( MY_PLUGIN_DIR . 'theme/archive-' . $my_post_type . '.php' );
            exit;
            break;

        // Is this a single page
        case ( is_single() ):
            // If your not my post type GTFO
            if ( get_post_type() != $my_post_type ) return;                
            if ( file_exists( STYLESHEETPATH . '/single-' . $my_post_type . '.php' ) ) return;

            load_template( MY_PLUGIN_DIR . '/theme/single-' . $my_post_type . '.php' );
            exit;
            break;

        // Is this a post type archive page
        case ( is_post_type_archive( $my_post_type ) ):
            if ( file_exists( STYLESHEETPATH . '/archive-' . $my_post_type . '.php' ) ) return;
            load_template( MY_PLUGIN_DIR . '/theme/archive-' . $my_post_type . '.php' );
            exit;
            break;
        default:
            return;
    } // End 'switch'
}

function tt_activation(){

    // our terms need our taxes registered so we can insert them 
    register_taxonomy_priority();
    register_taxonomy_phase();
    register_taxonomy_status();

    // insert some base terms 
    // priority 
    wp_insert_term( 'High',   'priority', array( 'description' => '', 'slug' => 'high' ) );
    wp_insert_term( 'Low',    'priority', array( 'description' => '', 'slug' => 'low' ) );
    wp_insert_term( 'Medium', 'priority', array( 'description' => '', 'slug' => 'medium' ) );

    // status
    wp_insert_term( 'Aborted',  'status', array( 'description' => 'A Task that will NOT be completed.', 'slug' => 'aborted' ) );
    wp_insert_term( 'Closed',   'status', array( 'description' => 'A Task that has been resolved and reviewed is completed.', 'slug' => 'closed' ) );
    wp_insert_term( 'New',      'status', array( 'description' => 'A New Task is a Task that is waiting to be worked on.', 'slug' => 'new' ) );
    wp_insert_term( 'Open',     'status', array( 'description' => 'A Task that is currently being worked on.', 'slug' => 'open' ) );
    wp_insert_term( 'Resolved', 'status', array( 'description' => 'The Task has been finished, but needs to be approved before it is closed.', 'slug' => 'resolved' ) );

    // priority  
    wp_insert_term( '0.1', 'phase', array( 'description' => '', 'slug' => '0-1' ) );
    wp_insert_term( '1.0', 'phase', array( 'description' => '', 'slug' => '1-0' ) );
    wp_insert_term( '2.0', 'phase', array( 'description' => '', 'slug' => '2-0' ) );
    wp_insert_term( '3.0', 'phase', array( 'description' => '', 'slug' => '3-0' ) );

    // insert sample task  
    $author_ID = get_current_user_id();
    $post = array(
        'post_title' => 'Your first Task!',
        'post_content' => 'This is a sample Task',
        'post_author' => $author_ID,
        'post_type' => 'task',
        'post_status' => 'pending'
    );
    $post_id = wp_insert_post( $post, true );

    // assign a term for our sample Task  
    if ( isset( $post_id ) ) {
        $term_id = term_exists( 'medium', 'priority' );
        wp_set_post_terms( $post_id, $term_id, 'priority' );

        $term_id = term_exists( 'new', 'status' );
        wp_set_post_terms( $post_id, $term_id, 'status' );

        $term_id = term_exists( '1.0', 'phase' );
        wp_set_post_terms( $post_id, $term_id, 'phase' );
    }
}

function tt_warning() {
    $copy = '<p><strong>Thanks for installing Task Tracker!</strong> We\'ve added a sample Task, along with a few most commonly used statuses, priorities and phases. Your first Task is Pending and assigned the following: <br /><br /> Status = New<br /> Priority = Medium<br /> Phase = 1</p>';
    $one = "post-new.php?post_type=task";
    $two = "options-permalink.php";
    print '<div class="updated fade">';
    print sprintf( __( $copy . 'All you need to do know is start <a href="%1$s">Adding Tasks</a> and <a href="%2$s">update your permalinks</a>!' ),  $one, $two );
    print '</div>';
}

function project_submit_task() {
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

function project_wp_update_post( $post ) {
        
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

///////////////////////////////
///////////////////////////////
// @todo use CPT class for this
///////////////////////////////
///////////////////////////////
function register_cpt_task() {
    $labels = array( 
        'name' => _x( 'Tasks', 'task' ),
        'singular_name' => _x( 'Task', 'task' ),
        'add_new' => _x( 'Add New', 'task' ),
        'add_new_item' => _x( 'Add New Task', 'task' ),
        'edit_item' => _x( 'Edit Task', 'task' ),
        'new_item' => _x( 'New Task', 'task' ),
        'view_item' => _x( 'View Task', 'task' ),
        'search_items' => _x( 'Search Tasks', 'task' ),
        'not_found' => _x( 'No galleries found', 'task' ),
        'not_found_in_trash' => _x( 'No galleries found in Trash', 'task' ),
        'parent_item_colon' => _x( 'Parent Task:', 'task' ),
        'menu_name' => _x( 'Tasks', 'task' )
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Photo galleries',
        'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'revisions' ),
        'taxonomies' => array( 'assigned', 'phase', 'priority', 'project', 'status', 'type', 'ETA' ),
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

    register_post_type( 'task', $args );
}

function register_taxonomy_assigned() {

    $labels = array( 
        'name' => _x( 'Assigned', 'assigned' ),
        'singular_name' => _x( 'Assigned', 'assigned' ),
        'search_items' => _x( 'Search Assigned', 'assigned' ),
        'popular_items' => _x( 'Popular Assigned', 'assigned' ),
        'all_items' => _x( 'All Assigned', 'assigned' ),
        'parent_item' => _x( 'Parent Assigned', 'assigned' ),
        'parent_item_colon' => _x( 'Parent Assigned:', 'assigned' ),
        'edit_item' => _x( 'Edit Assigned', 'assigned' ),
        'update_item' => _x( 'Update Assigned', 'assigned' ),
        'add_new_item' => _x( 'Add New Assigned', 'assigned' ),
        'new_item_name' => _x( 'New Assigned Name', 'assigned' ),
        'separate_items_with_commas' => _x( 'Separate assigned with commas', 'assigned' ),
        'add_or_remove_items' => _x( 'Add or remove assigned', 'assigned' ),
        'choose_from_most_used' => _x( 'Choose from the most used assigned', 'assigned' ),
        'menu_name' => _x( 'Assigned', 'assigned' )
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'hierarchical' => false,
        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'assigned', array('task'), $args );
}

function register_taxonomy_priority() {

    $labels = array( 
        'name' => _x( 'Prioritys', 'priority' ),
        'singular_name' => _x( 'Priority', 'priority' ),
        'search_items' => _x( 'Search Prioritys', 'priority' ),
        'popular_items' => _x( 'Popular Prioritys', 'priority' ),
        'all_items' => _x( 'All Prioritys', 'priority' ),
        'parent_item' => _x( 'Parent Priority', 'priority' ),
        'parent_item_colon' => _x( 'Parent Priority:', 'priority' ),
        'edit_item' => _x( 'Edit Priority', 'priority' ),
        'update_item' => _x( 'Update Priority', 'priority' ),
        'add_new_item' => _x( 'Add New Priority', 'priority' ),
        'new_item_name' => _x( 'New Priority Name', 'priority' ),
        'separate_items_with_commas' => _x( 'Separate prioritys with commas', 'priority' ),
        'add_or_remove_items' => _x( 'Add or remove prioritys', 'priority' ),
        'choose_from_most_used' => _x( 'Choose from the most used prioritys', 'priority' ),
        'menu_name' => _x( 'Prioritys', 'priority' ),
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'hierarchical' => true,
        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'priority', array('task'), $args );
}

function register_taxonomy_phase() {

    $labels = array( 
        'name' => _x( 'Phase', 'phase' ),
        'singular_name' => _x( 'Phase', 'phase' ),
        'search_items' => _x( 'Search Phase', 'phase' ),
        'popular_items' => _x( 'Popular Phase', 'phase' ),
        'all_items' => _x( 'All Phase', 'phase' ),
        'parent_item' => _x( 'Parent Phase', 'phase' ),
        'parent_item_colon' => _x( 'Parent Phase:', 'phase' ),
        'edit_item' => _x( 'Edit Phase', 'phase' ),
        'update_item' => _x( 'Update Phase', 'phase' ),
        'add_new_item' => _x( 'Add New Phase', 'phase' ),
        'new_item_name' => _x( 'New Phase Name', 'phase' ),
        'separate_items_with_commas' => _x( 'Separate phase with commas', 'phase' ),
        'add_or_remove_items' => _x( 'Add or remove phase', 'phase' ),
        'choose_from_most_used' => _x( 'Choose from the most used phase', 'phase' ),
        'menu_name' => _x( 'Phase', 'phase' ),
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'hierarchical' => true,
        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'phase', array('task'), $args );
}

function register_taxonomy_project() {

    $labels = array( 
        'name' => _x( 'Projects', 'project' ),
        'singular_name' => _x( 'Project', 'project' ),
        'search_items' => _x( 'Search Projects', 'project' ),
        'popular_items' => _x( 'Popular Projects', 'project' ),
        'all_items' => _x( 'All Projects', 'project' ),
        'parent_item' => _x( 'Parent Project', 'project' ),
        'parent_item_colon' => _x( 'Parent Project:', 'project' ),
        'edit_item' => _x( 'Edit Project', 'project' ),
        'update_item' => _x( 'Update Project', 'project' ),
        'add_new_item' => _x( 'Add New Project', 'project' ),
        'new_item_name' => _x( 'New Project Name', 'project' ),
        'separate_items_with_commas' => _x( 'Separate projects with commas', 'project' ),
        'add_or_remove_items' => _x( 'Add or remove projects', 'project' ),
        'choose_from_most_used' => _x( 'Choose from the most used projects', 'project' ),
        'menu_name' => _x( 'Projects', 'project' )
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'hierarchical' => true,
        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'project', array('task'), $args );
}

function register_taxonomy_status() {
    $labels = array( 
        'name' => _x( 'Status', 'status' ),
        'singular_name' => _x( 'Status', 'status' ),
        'search_items' => _x( 'Search Status', 'status' ),
        'popular_items' => _x( 'Popular Status', 'status' ),
        'all_items' => _x( 'All Status', 'status' ),
        'parent_item' => _x( 'Parent Status', 'status' ),
        'parent_item_colon' => _x( 'Parent Status:', 'status' ),
        'edit_item' => _x( 'Edit Status', 'status' ),
        'update_item' => _x( 'Update Status', 'status' ),
        'add_new_item' => _x( 'Add New Status', 'status' ),
        'new_item_name' => _x( 'New Status Name', 'status' ),
        'separate_items_with_commas' => _x( 'Separate status with commas', 'status' ),
        'add_or_remove_items' => _x( 'Add or remove status', 'status' ),
        'choose_from_most_used' => _x( 'Choose from the most used status', 'status' ),
        'menu_name' => _x( 'Status', 'status' )
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'hierarchical' => true,
        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'status', array('task'), $args );
}

function register_taxonomy_ETA() {
    $labels = array(
        'name' => _x( 'ETA', 'ETA' ),
        'singular_name' => _x( 'ETA', 'ETA' ),
        'search_items' => _x( 'Search ETA', 'ETA' ),
        'popular_items' => _x( 'Popular ETA', 'ETA' ),
        'all_items' => _x( 'All ETA', 'ETA' ),
        'parent_item' => _x( 'Parent ETA', 'ETA' ),
        'parent_item_colon' => _x( 'Parent ETA:', 'ETA' ),
        'edit_item' => _x( 'Edit ETA', 'ETA' ),
        'update_item' => _x( 'Update ETA', 'ETA' ),
        'add_new_item' => _x( 'Add New ETA', 'ETA' ),
        'new_item_name' => _x( 'New ETA Name', 'ETA' ),
        'separate_items_with_commas' => _x( 'Separate ETA with commas', 'ETA' ),
        'add_or_remove_items' => _x( 'Add or remove ETA', 'ETA' ),
        'choose_from_most_used' => _x( 'Choose from the most used ETA', 'ETA' ),
        'menu_name' => _x( 'ETA', 'ETA' )
    );
        
    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'hierarchical' => true,
        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'ETA', array('task'), $args );
}
