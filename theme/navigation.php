<div class="zm-tt-navigation-container">
<nav class="tt-navigation-container">
<ul>
<li class="currnet-item"><a href="<?php bloginfo('url'); ?>/task" title="List Recent Activity for: <?php bloginfo('name'); ?>">Recent</a></li>
<li class="create-ticket">
<?php if ( is_user_logged_in() ) : ?>
    <div class="zm-tt-form-container">
        <a href="javascript://" id="create_ticket" class="button">Create a Task</a>
    </div>
<?php else : ?>
    <div class="zm-tt-form-container">
    <a href="<?php echo wp_login_url(); ?>" title="Click to login and create a Task">Login</a> to create a Task
    </div>
<?php endif ?>
</li>
</ul>
</nav>
</div>
