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
    public function getName();
    public function regsiterCpt();
    public function regsiterCt();    
}

/**
 * Declare our methods signature
 */
abstract class CustomPostTypeBase implements ICustomPostType {
    
    public function regsiterCpt() {
        print 'not my cpt..';
    }
    
    public function regsiterCt() {
        print 'not my ct..';    
    }
}

class CustomPostType extends CustomPostTypeBase { 
    
    static $instance;
    
    /**
     * Everything to be ran when our class is instantioned adds hooks and sh!t 
     */
    public function __construct() {
        self::$instance = $this;
        
        add_action( 'init', array( &$this, 'regsiterCpt' ) );
        add_action( 'init', array( &$this, 'regsiterCt' ) ); 
    }
    
    public function regsiterCpt() {
        print 'my cpt...';
    }
}

$task = new CustomPostType('Task Tracker');
