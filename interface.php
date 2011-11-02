<?php
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
 * - Deactivation script (to come)
 * - enqueue a base stylesheet (to come)
 */
 
 // @todo namespace
interface ICustomPostType {

    public function registerPostType( $param=array() );
    public function registerTaxonomy( $param=array() );        
    // @todo http://codex.wordpress.org/Function_Reference/locate_template
    public function templateRedirect();
    public function registerActivation();
    // public function regsiterDeactivation();
    // public function baseStyleSheet( $param=array() );

} // End 'ICustomPostType'