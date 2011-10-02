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
?>
<ul class="inline">
    <li>
    	<?php 
    	/*
    	 @todo tt_task_age does not work when this file is loaded via ajax
    	 This is just a quick hack until we add support in functions.php for tt_task_age()
    	 to except a post_id as a param and get the age of a CPT
    	 */
    	?>
		<?php if ( empty( $_POST['post_id'] ) ) : ?>    	
    	<div class="age-container">
    		<small>Age</small><br />
    		<span class="age-icon"><?php tt_task_age(); ?></span>
    	</div>
    	<?php endif; ?>
    </li>                              
    
    <li>
	    <div class="status-container"><small>Status</small><br /> 
	    	<?php print zm_base_get_the_term_list( array( 'post_id' => $id , 'taxonomy' => 'status') ); ?>
	    </div>
    </li>

    <li>
    	<div class="priority-container"><small>Priority</small><br />
    		<?php print zm_base_get_the_term_list( array( 'post_id' => $id, 'taxonomy' => 'priority') ); ?>
    	</div>
    </li>
    <li>
    	<div class="project-container"><small>Project</small><br />
    		<?php print zm_base_get_the_term_list( array( 'post_id' => $id, 'taxonomy' => 'project' ) ); ?>
    	</div>
    </li>
    <li>
    	<div class="milestone-container"><small>Milestone</small><br />
    		<?php print zm_base_get_the_term_list( array( 'post_id' => $id, 'taxonomy' => 'milestone' ) ); ?>
    	</div>
    </li>
    <li>
    	<div class="type-container"><small>Type</small><br />
    		<?php print zm_base_get_the_term_list(array( 'post_id' => $id, 'taxonomy' => 'type' ) ); ?>
    	</div>
    </li>
    <li>
    	<span class="ui-icon ui-icon-arrow"><a href="#top">Return to Top</a></span>
    </li>
</ul>
