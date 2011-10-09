<div class="zm-tt-navigation-container">
    <nav class="tt-navigation-container">
        <ul>
            <li class="most-recent-task"><a href="<?php bloginfo('url'); ?>/task" title="List Recent Activity for: <?php bloginfo('name'); ?>">View Recent Task</a></li>
            <li class="create-ticket">
                <?php if ( is_user_logged_in() ) : ?>
                    <div class="zm-tt-form-container">
                        <a href="javascript://" id="create_ticket" data-template="theme/custom/task-create.php" class="button">Create a task</a>
                    </div>                
                <?php endif ?>
            </li>
        </ul>
    </nav>
</div>
