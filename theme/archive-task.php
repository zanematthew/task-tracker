<?php get_header(); ?>
<?php get_template_part( 'header-container', 'single' ); ?>
    <div class="zm-tt-container zm-tt-archive-container">
        <div class="tt-glue">
            <div class="main-container">
                <?php load_template( MY_PLUGIN_DIR . 'theme/navigation.php' ); ?>   
                <div class="tt-filter-container">
                    <ul class="inline">
                        <li><a href="#filter-task" id="filter_handle" tt_template="navigation-filter.php">Filter</a></li>                
                    </ul>
                </div>
                <div id="tt_filter_target"></div>                
                <div id="tt_main_target">
                    <div class="tt_loading"></div>
-                    <div class="sample" tt_template="theme/archive-table.php"></div> 
                    <div id="no_results">nothing here move on..</div>                        
                </div>
            </div>
            <div class="zm-tt-sidebar-container">
                <?php zm_base_list_terms( array('taxonomy' => 'status', 'link' => 'javascript://' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'priority', 'link' => 'anchor' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'project' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'phase' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'assigned' ) ); ?>
            </div>
        </div>
    </div>
<?php tt_json_feed( 'task',  array( 'status', 'priority', 'project', 'phase', 'assigned' ) ); ?>
<?php get_footer(); ?>
