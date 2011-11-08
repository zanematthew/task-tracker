<div class="zm-default-sidebar-container">
    <ul class="text">
        <li class="zm-base-title">Info</li>
        <li>
            <?php if ( !is_user_logged_in() ) : ?>
                <div class="zm-default-form-container">
                    <a href="javascript://" id="ltfo_handle" class="login" data-template="theme/default/login.php" title="Click to login and create a task">Login to create a task</a>
                </div>
            <?php endif; ?>

            <?php if ( is_user_logged_in() ) : ?>
                <p><?php global $user_login; get_currentuserinfo(); ?>Welcome back <em><?php echo $user_login; ?></em>!</p>
                <p class="utility-container">
                    <?php if ( current_user_can( 'administrator' ) ) : ?>
                        <span class="mini-button-container">
                            <a href="<?php bloginfo('wpurl');?>/wp-admin" class="default" title="Click to go to WordPress admin">WordPress Admin</a>
                        </span>
                    <?php endif; ?>
                    <span class="mini-button-container">
                        <a href="<?php echo wp_logout_url( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); ?>" title="Click here to Log out" class="high">Logout</a>
                    </span>
                </p>
            <?php endif; ?>
        </li>
    </ul>        
    <?php 

	global $wp_query; 

	// Derive the Current Post Type
	// Are we viewing a Custom Post Type Archive?
    if ( is_post_type_archive() || is_single() ) {
		$cpt = $wp_query->query_vars['post_type'];	
	} 
        
    elseif ( is_tax() ) {
        // Are we viewing a Custom Taxonomy Archive?
		$cpt = $wp_query->posts[0]->post_type;
	} else {
       // Else die
		wp_die( 'need a CPT' );
	}

	$cpt_obj = get_post_types( array( 'name' => $cpt), 'objects' );
	?>
   	<?php foreach ( $cpt_obj[ $cpt ]->taxonomies as $tax ) : ?>
        <?php zm_base_list_terms( array('taxonomy' => $tax ) ); ?>
    <?php endforeach; ?>        
</div>
