<div class="zm-tt-form-container" id="task_create_form">
    <form action="javascript://" id="create_task_form">
        <div class="form-wrapper">
        <input type="hidden" value="task" name="post_type" />
        <p>
            <label>Title</label>
            <input type="text" name="post_title" id="post_title" />
        </p>
        <p>
            <label>Description</label>
            <textarea name="content"></textarea>
        </p>
        <?php zm_base_build_radio('status', array( 'default' => 'New' )); ?>
        <?php zm_base_build_radio('priority', array( 'default' => 'Medium' ) ); ?>
        <?php zm_base_build_options('project'); ?>
        <?php zm_base_build_options('phase'); ?>
        <?php zm_base_build_options('assigned'); ?>
        </div>
        <div class="button-container">
            <div id="publishing-action">
                <input id="pt_publish" class="button" type="submit" value="Save" accesskey="p" name="save" />
                <a href="javascript://" id="clear" class="button">Clear</a>
                <a href="javascript://" id="exit" class="button">Exit</a>
            </div>
        </div>
    </form>
</div>
