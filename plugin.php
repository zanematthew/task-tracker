<?php
if ( is_admin() ) {
    ini_set('display_errors', 'on');
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
require_once 'interface.php';
require_once 'abstract.php';
require_once 'class.php';

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
    ),
    array(
        'name' => 'Collectible',
        'type' => 'collectible',
        'supports' => array(
            'title',
            'editor',            
            'comments'
        ),
        // @todo automate mother fuckergrrrrr
        'taxonomies' => array(
            'magazine', 
            'bmx', 
            'sneaker'
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
        ),
    array( 
        'name' => 'magazine', 
        'post_type' => 'collectible'
         ),            
    array( 
        'name' => 'bmx', 
        'post_type' => 'collectible'
        ),            
    array( 
        'name' => 'sneaker', 
        'post_type' => 'collectible'
        )        
);