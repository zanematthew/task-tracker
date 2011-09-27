<div class="zm-tt-navigation-container">
    <nav class="tt-navigation-container">
        <ul>
            <li class="most-recent-<?php print get_query_var( 'post_type' ); ?>"><a href="<?php bloginfo('url'); ?>/<?php print get_query_var( 'post_type' ); ?>" title="List Recent Activity for: <?php bloginfo('name'); ?>">Recent</a></li>
            <li class="create-ticket">
                <?php if ( is_user_logged_in() ) : ?>
                    <div class="zm-tt-form-container">
                        <a href="javascript://" id="create_ticket" tt_template="theme/custom/<?php print get_query_var( 'post_type' ); ?>-create.php" class="button">Create a <?php print get_query_var( 'post_type' ); ?></a>
                    </div>
                <?php else : ?>
                    <div class="zm-tt-form-container">
                        <a href="javascript://" id="ltfo_handle" class="login" tt_template="theme/default/login.php" title="Click to login and create a <?php print get_query_var( 'post_type' ); ?>">Login to create a <?php print get_query_var( 'post_type' ); ?></a>
                    </div>
                <?php endif ?>
            </li>
        </ul>
    </nav>
</div>

<?php if ( is_user_logged_in() ) : ?>
<a href="<?php echo wp_logout_url( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); ?>" title="Logout the fuck out">LTFO</a>
<?php endif; ?>

