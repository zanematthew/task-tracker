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

require_once 'wordpress-helper-functions.php';
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
            'content',
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
        )
);