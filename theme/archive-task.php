<?php get_header(); ?>
<?php get_template_part( 'header-container', 'single' ); ?>
    <div class="zm-tt-container zm-tt-archive-container">
        <div class="tt-glue">
            <div class="main-container">
                    <?php load_template( MY_PLUGIN_DIR . 'theme/navigation.php' ); ?>   
                    <div id="tt_main_target">
                    <div class="sample" tt_template="theme/archive-table.php"></div>
                    <div id="no_results">nothing here move on..</div>                        
                </div>
            </div>
            <div class="zm-tt-sidebar-container">
                <?php zm_base_list_terms( array('taxonomy' => 'status', 'link' => false ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'priority', 'link' => false ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'project', 'link' => false ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'phase', 'link' => false ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'assigned', 'link' => false ) ); ?>
            </div>
        </div>
    </div>
<?php tt_json_feed(); ?>
<?php get_footer(); ?>
