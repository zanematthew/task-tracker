<div class="comments-container" id="comments_target">
<h2>Comments</h2>
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
    
    <div class="zm-tt-form-container default-update-container" id="tt_update_container">        
        <form action="javascript://" method="POST" id="default_utility_udpate_form">
			<p><label class="zm-base-title"></label><br /><textarea tabindex="4" rows="10" cols="58" id="comment" name="comment"></textarea></p>    
            <div class="button-container">
                <div id="publishing-action">
                    <input id="pt_publish" class="button" type="submit" value="Update" accesskey="p" name="save" />
                    <a href="javascript://" id="exit" data-template="theme/default/entry-utility.php" class="button" data-post_type="task">Cancel</a>
                </div>
            </div>
        </form>
    </div>

</div>
