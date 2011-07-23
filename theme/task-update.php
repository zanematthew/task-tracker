<?php if ( is_user_logged_in() ) : ?>
<div class="zm-tt-form-container task-update-container">
        <a name="update"></a>
        <h3>Update Task <em><?php the_title(); ?></em></h3>
        <form action="javascript://" method="POST" id="update_task">
            <input type="hidden" name="PostID" id="post_id" value="<?php echo $post->ID; ?>" />
        <?php zm_base_build_radio('status'); ?>
        <?php zm_base_build_radio('priority'); ?>
        <?php zm_base_build_options('project'); ?>
        <?php zm_base_build_options('phase'); ?>
        <?php zm_base_build_options('assigned'); ?>
<p><label class="zm-base-title">Note</label><br /><textarea tabindex="4" rows="10" cols="58" id="comment" name="comment"></textarea></p>
        
            <div class="button-container">
                <div id="publishing-action">
                    <input id="pt_publish" class="button" type="submit" value="Update" accesskey="p" name="save" />
                </div>
            </div>
        </form>
</div>
<?php endif; ?>

<ul>
<?php
$comments = get_comments( array(
    'post_id' => $post->ID,
  'number'    => 10,
  'status'    => 'approve'
) );

foreach($comments as $comment) :
	echo "<li>{$comment->comment_content}<br /><small>{$comment->comment_author}</small></li>";
endforeach;

// print_r( $recent_comments );
?>
</ul>
