<?php if ( is_user_logged_in() ) : ?>
    <div class="zm-tt-form-container default-update-container" id="tt_update_container">
        <a name="update"></a>
        <h3>Update Task <em><?php the_title(); ?></em></h3>
        <form action="javascript://" method="POST" id="default_utility_udpate_form">
            <input type="hidden" name="PostID" id="post_id" value="<?php echo $post->ID; ?>" />
            <?php zm_base_build_options('status'); ?>
            <?php zm_base_build_options('priority'); ?>
            <?php zm_base_build_options('type'); ?>            
            <?php zm_base_build_options('project'); ?>
            <?php zm_base_build_options('phase'); ?>
            <?php zm_base_build_options('assigned'); ?>
            <div class="button-container">
                <div id="publishing-action">
                    <input id="pt_publish" class="button" type="submit" value="Update" accesskey="p" name="save" />
                    <a href="javascript://" id="exit" data-template="theme/default/entry-utility.php" class="button" data-post_type="task">Cancel</a>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>