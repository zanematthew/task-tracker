<?php 

get_header();
get_template_part( 'header-container', 'single' ); 

?>
<div class="zm-tt-container zm-tt-archive-container">
<div class="tt-wrapper">
    <div class="tt-glue">
        <div class="main-container">
            <?php load_template( MY_PLUGIN_DIR . 'theme/default/navigation.php' ); ?>   
            <div class="tt-filter-container">
                <ul class="inline">
                    <li class="icon-container"><a href="javascript://" id="filter_handle" class="icon-find" tt_template="default/navigation-filter.php" data-post_type="<?php print  get_query_var( 'post_type' );?>">Advanced Filter</a></li>
                </ul>
            </div>
            <div id="tt_filter_target"></div>                
            <div id="tt_main_target">
                <div class="tt_loading"></div>
                <div class="sample" tt_template="theme/custom/archive-table.php" data-post_type="<?php print get_query_var( 'post_type' ); ?>"></div> 
                <div id="no_results"></div>                        
            </div>
        </div>
        <div class="zm-tt-sidebar-container">
            <ul class="text">
                <li class="zm-base-title">Info</li>
                <li>
                    <?php if ( !is_user_logged_in() ) : ?>
                        <div class="zm-tt-form-container">
                            <a href="javascript://" id="ltfo_handle" class="login" tt_template="theme/default/login.php" title="Click to login and create a task">Login to create a task</a>
                        </div>
                    <?php endif; ?>

                    <?php if ( is_user_logged_in() ) : ?>
                        <p><?php global $user_login; get_currentuserinfo(); ?>Welcome back <em><?php echo $user_login; ?></em>!</p>
                        <p class="utility-container">
                            <?php if ( current_user_can( 'administrator' ) ) : ?>
                                <span class="mini-button-container">
                                    <a href="<?php bloginfo('wpurl');?>/wp-admin" class="default" title="Click to go to WordPress admin">WordPress Admin</a>
                                </span>
                            <?php endif; ?>
                            <span class="mini-button-container">
                                <a href="<?php echo wp_logout_url( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); ?>" title="Click here to Log the fuck out" class="high">Logout</a>
                            </span>
                        </p>
                    <?php endif; ?>
                </li>
            </ul>        
            <?php zm_base_list_terms( array('taxonomy' => 'type', 'link' => 'anchor', 'post_id' => $post->ID ) ); ?>
            <?php zm_base_list_terms( array('taxonomy' => 'status', 'link' => 'anchor', 'post_id' => $post->ID ) ); ?>
            <?php zm_base_list_terms( array('taxonomy' => 'priority', 'link' => 'anchor', 'post_id' => $post->ID ) ); ?>
            <?php zm_base_list_terms( array('taxonomy' => 'project','link' => 'anchor', 'post_id' => $post->ID ) ); ?>
            <?php zm_base_list_terms( array('taxonomy' => 'phase','link' => 'anchor', 'post_id' => $post->ID ) ); ?>
            <?php zm_base_list_terms( array('taxonomy' => 'assigned','link' => 'anchor', 'post_id' => $post->ID ) ); ?>
        </div>
    </div>
</div>
</div>
<?php 

$cpt_obj = get_post_types( array( 'name' => get_query_var( 'post_type' ) ), 'objects' );
tt_json_feed( get_query_var( 'post_type' ),  $cpt_obj[get_query_var( 'post_type' )]->taxonomies );
get_footer(); 

?>
