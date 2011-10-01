<?php get_header(); ?>
<?php get_template_part('header-container','index'); ?>
<div class="zm-tt-container zm-tt-single-container">
    <div class="tt-wrapper">
    <div class="tt-glue">
        <div class="main-container">
            <?php load_template( MY_PLUGIN_DIR . '/theme/default/navigation.php' ); ?>
            <div id="tt_main_target">
                <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
                    <div <?php post_class('result')?>>
                        <div class="content">
                            <h1 class="title"><?php the_title(); ?></h1>
                            <?php the_content(); ?>
                        </div>
                        <div class="entry-utility" id="utility">
                            <ul class="inline">
                                <li><div class="age-container"><small>Age</small><br /><span class="age-icon"><?php tt_task_age(); ?></span></li>
                                <li><div class="ticket-container"><?php print '<small>Ticket </small><br />#' . '<a href="'. get_permalink() .'">' . $post->ID . '</a>'; ?></div></li>                            
                                <li><div class="status-container"><small>Status</small><br /> <?php print zm_base_get_the_term_list($post->ID, 'status'); ?></div></li>
                                <li><div class="priority-container"><small>Priority</small><br /><?php print zm_base_get_the_term_list( $post->ID, 'priority'); ?></div></li>
                                <li><div class="project-container"><small>Project</small><br /><?php print zm_base_get_the_term_list($post->ID, 'project'); ?></div></li>
                                <li><div class="milestone-container"><small>Milestone</small><br /><?php print zm_base_get_the_term_list($post->ID, 'milestone'); ?></div></li>
                                <li><div class="type-container"><small>Type</small><br /><?php print zm_base_get_the_term_list($post->ID, 'type'); ?></div></li>                                
                                <li><span class="ui-icon ui-icon-arrow"><a href="#top">Return to Top</a></span></li>
                            </ul>
                        </div>
                    </div>
                    <?php load_template( MY_PLUGIN_DIR . '/theme/custom/task-update.php' ); ?>
                <?php endwhile; ?>
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
                                <span class="mini-button"><a href="<?php bloginfo('wpurl');?>/wp-admin" title="Click to go to WordPress admin">WordPress Admin</a></span>
                            <?php endif; ?>
                            <span class="mini-button"><a href="<?php echo wp_logout_url( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); ?>" title="Click here to Log the fuck out">Logout</a></span>
                        </p>
                    <?php endif; ?>
                </li>
            </ul>
            <?php zm_base_list_terms( array('taxonomy' => 'status' ) ); ?>
            <?php zm_base_list_terms( array('taxonomy' => 'priority', 'link' => false ) ); ?>
            <?php zm_base_list_terms( array('taxonomy' => 'project', 'link' => false ) ); ?>
            <?php zm_base_list_terms( array('taxonomy' => 'phase', 'link' => false ) ); ?>
            <?php zm_base_list_terms( array('taxonomy' => 'assigned', 'link' => false ) ); ?>
        </div>
    </div>
</div>
</div>
<?php get_footer(); ?>

