<?php 
global $post;
?>
<div class="zm-default-navigation-container">
    <nav>
        <ul>
            <li class="most-recent-<?php print $post->post_type; ?>"><a href="<?php bloginfo('url'); ?>/<?php print $post->post_type; ?>" title="List Recent Activity for: <?php bloginfo('name'); ?>">View Recent <span class="post-type"><?php print $post->post_type; ?></span></a></li>
            <li class="create-ticket">
                <?php if ( is_user_logged_in() ) : ?>
                    <div class="zm-default-form-container">
                        <a href="javascript://" id="create_ticket" data-template="theme/custom/<?php print $post->post_type; ?>-create.php" class="button">Create a <?php print $post->post_type; ?></a>
                    </div>                
                <?php endif ?>
            </li>
        </ul>
    </nav>
</div>
