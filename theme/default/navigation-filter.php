<div class="zm-tt-form-container">
    <form action="javascript://" id="filter_task_form">
        <input type="hidden" name="security" value="<?php print wp_create_nonce( 'tt-ajax-forms' );?>">
        <div class="form-wrapper">
            <input type="hidden" value="task" name="post_type" />
            <?php
            /*
            $post_types = get_post_types( array( '_builtin' => false ), 'objects' );
            foreach ( $post_types['task']->taxonomies as $tax ) {
                zm_base_build_options( array( 'taxonomy' => strtolower( $tax ), 'prepend' => $tax.'-' ) );
            }
            */
            ?>
            <?php zm_base_build_options( array( 'taxonomy' => 'status', 'prepend' => 'status-' ) ); ?>
            <?php zm_base_build_options( array( 'taxonomy' => 'priority', 'prepend' => 'priority-' ) ); ?>
            <?php zm_base_build_options( array( 'taxonomy' => 'project', 'prepend' => 'project-' ) ); ?>            
        </div>
    </form>
</div>
