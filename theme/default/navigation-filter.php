<div class="zm-tt-form-container">
    <form action="javascript://" id="filter_task_form">
        <input type="hidden" name="security" value="<?php print wp_create_nonce( 'tt-ajax-forms' );?>">
        <div class="form-wrapper">
            <input type="hidden" value="task" name="post_type" />
            <?php

            $post_type = $_POST['post_type'];
            $my_cpt = get_post_types( array( 'name' => $post_type ), 'objects' );                    

            foreach( $my_cpt[ $post_type ]->taxonomies as $taxonomy ) {
                zm_base_build_options( array( 'taxonomy' => $taxonomy, 'prepend' => $taxonomy.'-' ) );
            }
            ?>
        </div>
    </form>
</div>
