<?php if ( is_user_logged_in() ) : ?>
    <div class="zm-tt-form-container default-update-container" id="tt_update_container">
        <a name="update"></a>
        <h3>Update Task <em><?php the_title(); ?></em></h3>
        <form action="javascript://" method="POST" id="default_update">
            <input type="hidden" name="PostID" id="post_id" value="<?php echo $post->ID; ?>" />
            <?php zm_base_build_radio('status'); ?>
            <?php zm_base_build_radio('priority'); ?>
            <?php zm_base_build_options('type'); ?>            
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

<div class="comments-container" id="comments_target">
<h2>Comments &amp; Notes</h2>
<ul>
    <?php
    $comments = get_comments( array(
      'post_id' => $post->ID,
      'number'    => 10,
      'status'    => 'approve'
    ) );
    foreach($comments as $comment) : ?>
        <li>
            <div class="avatar-container"><?php print get_avatar( $comment, 32 ); ?></div>
            <time><?php print $comment->comment_date; ?></time>            
            <div class="content"><?php print $comment->comment_content; ?></div>
            <div class="author"><?php print $comment->comment_author; ?></div>            
        </li>
    <?php endforeach; ?>
</ul>
</div>
