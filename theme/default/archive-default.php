<?php get_header(); ?>
<?php get_template_part('header-container','index'); ?>
<?php 

global $wp_query; 
$my_cpt = $wp_query->query_vars['post_type'];

?>
    <div class="zm-tt-container zm-tt-archive-container">
        <div class="tt-glue">
            <div class="main-container">
                <div id="tt_filter_target"></div>                
                <div id="tt_main_target">
                    <div class="tt_loading"></div>
                    <div class="sample" tt_template="theme/default/archive-table.php" data-post_type="<?php print $my_cpt; ?>"></div> 
                    <div id="no_results">nothing here move on..</div>                        
                </div>
            </div>
            <div class="zm-tt-sidebar-container">
                <?php                        
                $current_post_type = get_query_var( 'post_type' );                
                $my_cpt = get_post_types( array( 'name' => $current_post_type), 'objects' );
                ?>
                <?php foreach ( $my_cpt[ $current_post_type ]->taxonomies as $tax ) : ?>
                    <?php zm_base_list_terms( array('taxonomy' => $tax ) ); ?>
                <?php endforeach; ?>        
            </div>
        </div>
    </div>
<?php tt_json_feed( $current_post_type,  $my_cpt[ $current_post_type ]->taxonomies ); ?>
<?php get_footer(); ?>
