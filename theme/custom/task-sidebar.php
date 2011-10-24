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
                        <a href="<?php echo wp_logout_url( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); ?>" title="Click here to Log out" class="high">Logout</a>
                    </span>
                </p>
            <?php endif; ?>
        </li>
    </ul>                    
    <?php zm_base_list_terms( array('taxonomy' => 'type', 'link' => 'anchor', 'post_id' => $post->ID, 'post_type' => $post->post_type ) ); ?>
    <?php zm_base_list_terms( array('taxonomy' => 'status', 'link' => 'anchor', 'post_id' => $post->ID ) ); ?>
    <?php zm_base_list_terms( array('taxonomy' => 'priority', 'link' => 'anchor', 'post_id' => $post->ID ) ); ?>
    <?php zm_base_list_terms( array('taxonomy' => 'project','link' => 'anchor', 'post_id' => $post->ID ) ); ?>
    <?php zm_base_list_terms( array('taxonomy' => 'phase','link' => 'anchor', 'post_id' => $post->ID ) ); ?>
    <?php zm_base_list_terms( array('taxonomy' => 'assigned','link' => 'anchor', 'post_id' => $post->ID ) ); ?>
</div>
