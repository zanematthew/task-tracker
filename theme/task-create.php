<div class="form-container" id="task_create_form">
    <form action="javascript://" id="create_task_form">
        <input type="hidden" value="task" name="post_type" />
        <p>
            <label>Title</label>
            <input type="text" name="post_title" id="post_title" />
        </p>
        <p>
            <label>Description</label>
            <textarea name="content"></textarea>
        </p>
        <?php zm_base_build_radio('status'); ?>
        <?php zm_base_build_radio('priority'); ?>
        <?php zm_base_build_options('project'); ?>
        <?php zm_base_build_options('phase'); ?>
        <?php //zm_base_build_options('category'); ?>
        <?php zm_base_build_options('assigned'); ?>
        <div class="button-container">
            <div id="publishing-action">
                <input id="pt_publish" class="button" type="submit" value="Save" accesskey="p" name="save" />
                <button id="clear">Clear</button>
                <button id="exit">Exit</button>
            </div>
        </div>
    </form>
</div>
