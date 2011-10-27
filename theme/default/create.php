<?php

$cpt = $_POST['post_type'];
$cpt_obj = get_post_types( array( 'name' => $cpt), 'objects' );

?>

<div class="zm-default-form-container" id="default_create_form">
    <form action="javascript://" id="create_default_form">
        <div id="default_message_target"></div>
        <input type="hidden" name="security" value="<?php print wp_create_nonce( 'tt-ajax-forms' );?>">
        <div class="form-wrapper">
        <input type="hidden" value="<?php print $cpt; ?>" name="post_type" />
        <p>
            <label>Title</label>
            <input type="text" name="post_title" id="post_title" />
        </p>
        <p>
            <label>Description</label>
            <textarea name="content"></textarea>
        </p>        
        <?php foreach ( $cpt_obj[$cpt]->taxonomies as $tax ) : ?>
            <?php zm_base_build_options( $tax ); ?>
        <?php endforeach; ?>
        </div>
        <div class="button-container">
            <div id="publishing-action">
                <div class="left">                    
                    <input id="save_exit" class="button" type="submit" value="Save &amp; Close" accesskey="p" name="save_exit" data-template="theme/default/archive-table.php" data-post_type="<?php print $cpt; ?>"/>
                    <ul class="entry-utility-container">                        
                        <li><a href="javascript://" id="save_add" data-template="theme/default/archive-table.php" data-post_type="<?php print $cpt; ?>">Save &amp; add another</a></li>                        
                        <li><a href="javascript://" id="clear">Clear</a>|</li>
                        <li><a href="javascript://" id="exit" data-template="theme/default/archive-table.php" data-post_type="<?php print $cpt; ?>">Exit</a></li>
                    </ul>
                </div>
                <div class="right">
                </div>                
            </div>
        </div>
    </form>
</div>
