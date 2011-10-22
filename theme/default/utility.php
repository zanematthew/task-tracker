<?php
/**
 * Derive our post_id, either from the post or if this file is loaded via ajax
 */
if ( !empty( $_POST['post_id'] ) )
	$id = (int)$_POST['post_id'];
else 
	$id = $post->ID;


/**
 * Derive our post_type, either from the post or if this file is loaded via ajax
 */
if ( !empty( $_POST['post_type'] ) )
    $post_type = $_POST['post_type'];
else 
    $post_type = null;


/**
 * Once we have our post_type, we get the object to have access to our taxonomies
 */
$cpt_obj = get_post_types( array( 'name' => $post_type), 'objects' );

?>
<ul class="inline">
    <?php if ( empty( $_POST['post_id'] ) ) : ?>        
    <li>	
    	<div class="age-container">
    		<small>Age</small><br />
    		<span class="age-icon"><?php tt_task_age(); ?></span>
    	</div>
    </li>                              
    <?php endif; ?>    

    <li>
        <div class="status-container"><small>ID</small><br /> 
            <?php print $id; ?>
        </div>
    </li>

    <?php foreach ( $cpt_obj[$post_type]->taxonomies as $tax ) : ?>
    <li>
        <div class="<?php print $tax; ?>-container"><small><?php print $tax; ?></small><br /> 
            <?php print zm_base_get_the_term_list( array( 'post_id' => $id , 'post_type' => $post_type, 'taxonomy' => $tax, 'link' => 'anchor') ); ?>
        </div>
    </li>
    <?php endforeach; ?>


    <li class="right">
        <?php if ( is_user_logged_in() ) : ?>
        <span class="mini-button-container">
            <a href="javascript://" class="default" id="utiliy_update_handle">Click to Edit</a>        
        </span>
        <?php endif; ?>
    </li>
</ul>
