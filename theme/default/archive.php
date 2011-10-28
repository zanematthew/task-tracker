<?php get_header(); ?>
<?php get_template_part('header-container','index'); ?>
<?php 

global $wp_query; 
$cpt = $wp_query->query_vars['post_type'];
$cpt_obj = get_post_types( array( 'name' => $cpt), 'objects' );

?>

<div class="zm-default-container zm-default-archive-container">
    <div class="tt-glue">
        <div class="main-container">        

            <?php load_template( plugin_dir_path( __FILE__ ) . 'navigation.php' ); ?>   

            <div class="tt-filter-container">
                <ul class="inline">
                    <li><a href="javascript://" id="filter_handle" data-template="default/navigation-filter.php" data-post_type="<?php print  $cpt;?>">Advanced Filter</a></li>
                </ul>
            </div>
            <div id="tt_filter_target"></div>                
            <div id="tt_main_target">
                <div class="tt_loading"></div>
                <div class="sample" data-template="theme/default/archive-table.php" data-post_type="<?php print $cpt; ?>"></div> 
                <div id="no_results"></div>                        
            </div>
        </div>

        <?php load_template( plugin_dir_path( __FILE__ ) . 'sidebar.php' ); ?>   

    </div>
</div>

<?php tt_json_feed( $cpt,  $cpt_obj[ $cpt ]->taxonomies ); ?>

<?php get_footer(); ?>
