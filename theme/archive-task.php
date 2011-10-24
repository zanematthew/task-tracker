<?php 

get_header();
get_template_part( 'header-container', 'single' ); 

?>
<div class="zm-default-container zm-default-archive-container">
<div class="tt-wrapper">
    <div class="tt-glue">
        <div class="main-container">
            <?php load_template( plugin_dir_path( __FILE__ ) . 'default/navigation.php' ); ?>   
            <div class="tt-filter-container">
                <ul class="inline">
                    <li class="icon-container"><a href="javascript://" id="filter_handle" class="icon-find" data-template="default/navigation-filter.php" data-post_type="<?php print  get_query_var( 'post_type' );?>">Advanced Filter</a></li>
                </ul>
            </div>
            <div id="tt_filter_target"></div>                
            <div id="tt_main_target">
                <div class="tt_loading"></div>
                <div class="sample" data-template="theme/custom/archive-table.php" data-post_type="<?php print get_query_var( 'post_type' ); ?>"></div> 
                <div id="no_results"></div>                        
            </div>
        </div>
        <?php load_template( plugin_dir_path( __FILE__ ) . 'custom/task-sidebar.php' ); ?>    
    </div>
</div>
</div>
<?php 

$cpt_obj = get_post_types( array( 'name' => get_query_var( 'post_type' ) ), 'objects' );
tt_json_feed( get_query_var( 'post_type' ),  $cpt_obj[get_query_var( 'post_type' )]->taxonomies );

get_footer(); 

?>
