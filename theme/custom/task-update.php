<?php if ( is_user_logged_in() ) : ?>
    <div class="zm-tt-form-container default-update-container" id="task_update_container" style="display: none;">
        <a name="update"></a>
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
                    <div class="mini-button-container">
                        <input class="update" type="submit" value="Update" accesskey="p" name="save" />                    
                        <a href="javascript://" id="task_entry_utility_update_exit" class="high">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>