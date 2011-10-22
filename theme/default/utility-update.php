<?php
global $post; 
$cpt = $post->post_type;
$cpt_obj = get_post_types( array( 'name' => $cpt), 'objects' );
?>

<?php if ( is_user_logged_in() ) : ?>
    <div class="zm-tt-form-container default-update-container" id="default_utility_update_container" style="display: none;">
        <a name="update"></a>
        <form action="javascript://" method="POST" id="default_utility_udpate_form">
            <input type="hidden" name="PostID" id="post_id" value="<?php echo $post->ID; ?>" />
                        
            <?php foreach ( $cpt_obj[$cpt]->taxonomies as $tax ) : ?>
                <?php zm_base_build_options( $tax ); ?>
            <?php endforeach; ?>

            <div class="button-container">
                <div id="publishing-action">
                    <div class="mini-button-container">
                        <input class="update" type="submit" value="Update" accesskey="p" name="save" />                    
                        <a href="javascript://" id="default_utility_update_exit" class="high">Exit</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>