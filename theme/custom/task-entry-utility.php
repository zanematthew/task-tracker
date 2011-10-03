<?php
/**
 *
 * @requiremen this file MUST work as an ajax request as well as in "the loop"
 *
 * @note if this is an ajax request our functions are blind to post
 * and therefore need a post_id and maybe even alittle more
 *
 */

if ( !empty( $_POST['post_id'] ) )
	$id = (int)$_POST['post_id'];
else 
	$id = $post->ID;

if ( !empty( $_POST['post_type'] ) )
    $post_type = $_POST['post_type'];
else 
    $post_type = null;

?>
<ul class="inline">
    <?php 
    /*
     @todo tt_task_age does not work when this file is loaded via ajax
     This is just a quick hack until we add support in functions.php for tt_task_age()
     to except a post_id as a param and get the age of a CPT
     */
    ?>
    <?php if ( empty( $_POST['post_id'] ) ) : ?>        
    <li>	
    	<div class="age-container">
    		<small>Age</small><br />
    		<span class="age-icon"><?php tt_task_age(); ?></span>
    	</div>
    </li>                              
    <?php endif; ?>    

    <li>
	    <div class="status-container"><small>Status</small><br /> 
	    	<?php print zm_base_get_the_term_list( array( 'post_id' => $id , 'post_type' => $post_type, 'taxonomy' => 'status', 'link' => 'anchor') ); ?>
	    </div>
    </li>

    <li>
    	<div class="priority-container"><small>Priority</small><br />
    		<?php print zm_base_get_the_term_list( array( 'post_id' => $id, 'taxonomy' => 'priority', 'post_type' => $post_type, 'link' => 'anchor' ) ); ?>
    	</div>
    </li>
    <li>
    	<div class="project-container">
            <small>Project</small><br />
    		<?php print zm_base_get_the_term_list( array( 'post_id' => $id, 'taxonomy' => 'project', 'post_type' => $post_type, 'link' => 'anchor'  ) ); ?>
    	</div>
    </li>
    <li>
    	<div class="milestone-container">
            <small>Milestone</small><br />
    		<?php print zm_base_get_the_term_list( array( 'post_id' => $id, 'taxonomy' => 'milestone', 'post_type' => $post_type, 'link' => 'anchor'  ) ); ?>
    	</div>
    </li>
    <li>
     <small>Type</small><br />
    	<div class="type-container">           
    		<?php print zm_base_get_the_term_list(array( 'post_id' => $id, 'taxonomy' => 'type' ) ); ?>
    	</div>
    </li>
    <li>
        <small>Comment</small><br />
        <div class="icon-container">            
            <span class="comment-count" id="task_comment_handle" data-post_id="<?php echo $id; ?>" data-template="theme/custom/comment.php"><a href="javascript://"></a></span>
        </div>
    </li>
    <li class="right">
        <span class="mini-button-container">
            <a href="javascript://" class="default" id="utiliy_update_handle">Click to Edit</a>
        </span>
    </li>
</ul>
