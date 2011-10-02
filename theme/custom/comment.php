<div class="comments-container" id="comments_target">
<?php 
if ( !empty( $_POST['post_id']) ) {
    $id = (int)$_POST['post_id'];
} else {
    $id = $post->ID;
}
?>
    <h2>Comments</h2>
    <ul>
        <?php
        $comments = get_comments( array(
          'post_id' => $id,
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
    <div class="zm-tt-form-container">        
        <form action="javascript://" method="POST" id="default_add_comment_form">
			<p>
                <textarea tabindex="4" rows="10" cols="58" id="comment" name="comment"></textarea>
            </p>    
            <div class="button-container">
                <div id="publishing-action">
                    <input id="comment_add" class="button" type="submit" value="Update" accesskey="p" name="save" />
                </div>
            </div>
        </form>
    </div>
</div>
