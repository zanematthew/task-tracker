<div class="zm-tt-form-container" id="task_create_form">
    <form action="javascript://" id="create_task_form">
        <input type="hidden" name="security" value="<?php print wp_create_nonce( 'tt-ajax-forms' );?>">
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
        <?php zm_base_build_radio( 'type', array( 'label' => 'Category', 'default' => 'Personal' ) ); ?>

        <?php zm_base_build_radio('status', array( 'default' => 'New' )); ?>
        <?php zm_base_build_input( array( 'taxonomy'=> 'priority', 'type'=> 'radio', 'default' => 'Medium' ) ); ?>
        <?php zm_base_build_options('project'); ?>
        <?php zm_base_build_options('phase'); ?>
        <?php zm_base_build_options( array( 'taxonomy' => 'assigned', 'label' => 'Assigned To' ) ); ?>
        </div>
        <div class="button-container">
            <div id="publishing-action">
                <div class="left">                    
                    <input id="save_exit" class="button" type="button" value="Save &amp; Close" accesskey="p" name="save_exit" />
                    <ul class="entry-utility-container">
                        <li><a id="save_add" href="javascript://">Add Another</a>|</li>
                        <li><a href="javascript://" id="clear">Clear</a>|</li>
                        <li><a href="javascript://" id="exit" data-template="theme/custom/archive-table.php" data-post_type="task">Exit</a></li>
                    </ul>
                </div>
                <div class="right">
                </div>                
            </div>
        </div>
    </form>
</div>
