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

                        <div class="entry-utility" title="Click to Edit" id="task_entry_utility_handle" data-post_id="<?php echo $post->ID;?>" data-template="theme/custom/task-entry-utility.php">                
                            <div id="task_entry_utility_target" style="text-align: center;">
                                <div class="tt_loading"></div>
                                Loading entry utility...
                            </div>
                        </div>

                        <div id="task_update_target"></div>

<?php // load_template( MY_PLUGIN_DIR . '/theme/custom/task-update.php' ); ?>

                    </div>
<div class="comments-container" id="comments_target">
<!-- <h2>Comments &amp; Notes</h2> -->
<?php //load_template( MY_PLUGIN_DIR . '/theme/custom/comment.php' ); ?>
<ul>
    <?php
    $comments = get_comments( array(
      'post_id' => $post->ID,
      'number'    => 10,
      'status'    => 'approve'
    ) );
    foreach($comments as $comment) : ?>
        <li>
            <div class="avatar-container"><?php print get_avatar( $comment, 32 ); ?></div>
            <time><?php print $comment->comment_date; ?></time>            
            <div class="content"><?php print $comment->comment_content; ?></div>
            <div class="author"><?php print $comment->comment_author; ?></div>            
        </li>
    <?php endforeach; ?>
</ul>
</div>

                <?php endwhile; ?>
            </div>
        </div>
        <div class="zm-tt-sidebar-container">
            <ul class="text">
                <li class="zm-base-title">Info</li>
                <li>
                    <?php if ( !is_user_logged_in() ) : ?>
                        <div class="zm-tt-form-container">
                            <a href="javascript://" id="ltfo_handle" class="login" data-template="theme/default/login.php" title="Click to login and create a task">Login to create a task</a>
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

