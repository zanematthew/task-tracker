<div class="zm-tt-form-container">
    <form action="javascript://" id="filter_task_form">
        <input type="hidden" name="security" value="<?php print wp_create_nonce( 'tt-ajax-forms' );?>">
        <div class="form-wrapper">
            <input type="hidden" value="task" name="post_type" />
            <?php
            $post_types = get_post_types( array( '_builtin' => false ), 'objects' );
            foreach ( $post_types['task']->taxonomies as $tax ) {
                zm_base_build_options( array( 'taxonomy' => $tax, 'prepend' => $tax.'-' ) );
            }
            ?>
        </div>
    </form>
</div>
