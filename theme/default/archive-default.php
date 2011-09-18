<?php get_header(); ?>
<?php get_template_part('header-container','index'); ?>
<?php 

global $wp_query; 
$cpt = $wp_query->query_vars['post_type'];
$cpt_obj = get_post_types( array( 'name' => $cpt), 'objects' );

?>
<div class="zm-tt-container zm-tt-archive-container">
    <div class="tt-glue">
        <div class="main-container">        
            <div class="tt-filter-container">
                <ul class="inline">
                    <li><a href="javascript://" id="filter_handle" tt_template="default/navigation-filter.php" data-post_type="<?php print  $cpt;?>">Advanced Filter</a></li>
                </ul>
            </div>
            <div id="tt_filter_target"></div>                
            <div id="tt_main_target">
                <div class="tt_loading"></div>
                <div class="sample" tt_template="theme/default/archive-table.php" data-post_type="<?php print $cpt; ?>"></div> 
                <div id="no_results">nothing here move on..</div>                        
            </div>
        </div>
        <div class="zm-tt-sidebar-container">
            <?php foreach ( $cpt_obj[ $cpt ]->taxonomies as $tax ) : ?>
                <?php zm_base_list_terms( array('taxonomy' => $tax ) ); ?>
            <?php endforeach; ?>        
        </div>
    </div>
</div>
<?php tt_json_feed( $cpt,  $cpt_obj[ $cpt ]->taxonomies ); ?>
<?php get_footer(); ?>
