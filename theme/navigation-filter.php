<div class="zm-tt-form-container">
    <form action="javascript://" id="filter_task_form">
        <input type="hidden" name="security" value="<?php print wp_create_nonce( 'tt-ajax-forms' );?>">
        <div class="form-wrapper">
            <input type="hidden" value="task" name="post_type" />
            <?php zm_base_build_options( 'status' ); ?>
            <?php zm_base_build_options( 'priority' ); ?>
            <?php zm_base_build_options( 'project' ); ?>
        </div>
    </form>
</div>