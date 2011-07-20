<?php


/** 
 * @helper zm_base_get_the_term_list( $id=0, $taxonomy, $before, $sep, $after ) prints semantically structured term list.
 * tried using add_filter( 'get_the_term_list', 'new_get_the_term_list' ); but it wasn't working
 * ummm, I think these 2 functions do the same thing wtf! 
 */
function zm_base_get_the_term_list( $id = 0, $taxonomy=null, $before = '', $sep = ', ', $after = '' ) {

	$terms = get_the_terms( $id, $taxonomy, $before, $sep, $after);

    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        print '&mdash;';
        return;
    } else {
	foreach ( $terms as $term ) {
		$link = get_term_link( $term, $taxonomy );

		if ( is_wp_error( $link ) )
			return $link;

		$term_links[] = '<a href="' . $link . '" rel="tag" class="zm-base-'. $taxonomy.'-'.$term->slug .'">' . $term->name . '</a>';
	}

	$term_links = apply_filters( "term_links-$taxonomy", $term_links );

	return $before . join( $sep, $term_links ) . $after;
	}
}

/** 
 * @helper zm_base_list_terms( $taxonomy ) This funtction will return a 'well' structured list of links for a given taxonomy 
 */
function zm_base_list_terms( $taxonomy ) {

    $terms = get_terms( $taxonomy );
    $html = null;

    // @todo -- add support for rss link 
    foreach( $terms as $term ) {
        $html .= '<li class="zm-base-' . $term->slug . '">';
        $html .= '<a href="' . get_term_link( $term->slug, $term->taxonomy ) . '" title="' . sprintf(
        __( "View all %s"), $term->name ) . '" '. '>' . $term->name . '</a>';
        $html .= '<span class="zm-base-count">' . $term->count . '</span>';
        $html .= '</li>';
    }

    // @todo make sure term used as class name is 'clean', i.e. no spaces! all lower case.
    $first = '<li class="zm-base-terms ' . $terms[1]->taxonomy . '"><span class="zm-base-title">' . $terms[1]->taxonomy . '</span><ul>';    
    $last = '</ul></li>';
    print $first . $html . $last; 
}

/** 
 * @helper zm_base_current_term( $taxonomy ) Determine the current term, idk fucking no what I'm doing.
 * @todo this is no longer used.
 */
function zm_base_current_term( $taxonomy ) {
    global $post;
    $current_term = null;
    
    // @todo better way to combine conditional 
    if ( $post ) {
        $my_terms = get_the_terms( $post->ID, $taxonomy );
        if ( $my_terms ) {
            if ( is_wp_error( $my_terms ) ) {
                exit( "Opps..." . $my_terms->get_error_message() . "..dog, cmon, fix it!" );
            }
            foreach( $my_terms as $my_term ) {
                $current_term = $my_term->name;
            }
        }
    }
    return $current_term;
}

/** 
 * @helper zm_base_get_terms( $taxonomy ) This mimics get_terms, but has shows error messages if we have one.
 * @todo $args should be a params. or look into using add_filter
 */
function zm_base_get_terms( $taxonomy ) {

    // All Terms 
    $args = array(
        'orderby' => 'parent',
        'hide_empty' => 0
         );

    $terms = get_terms( $taxonomy, $args );

    if ( is_wp_error( $terms ) ) {
//        exit( "Opps..." . $terms->get_error_message() . "..dog, cmon, fix it!" );
    }

    return $terms;
}

/**
 * @helper zm_base_build_options( $taxonomy, $value='term_id' ) Build an option list of Terms based on a given Taxonomy.
 * @param value = the value to be use in the form, can be term_id or term_slug
 */
function zm_base_build_options( $taxonomy=null, $value='term_id' ) {
    global $post;

    $terms = zm_base_get_terms( $taxonomy );
    
    if ( $post ) {
        $tmp = get_the_terms( $post->ID, $taxonomy );
    }
        
    if ( isset( $tmp ) && is_array( $tmp ) ) {
        $tmp_index = array_keys( $tmp );
        $current_term = $tmp[ $tmp_index[0]]->name;
    } else {
        $current_term = null;
    }

    // @todo the below markup should be pulled out into a 'view' 
    ?>
	<?php if ( !empty( $terms ) ) : ?>
    <fieldset class="zm-base-<?php echo $taxonomy; ?>-container <?php echo $taxonomy; ?>-container">
    <legend class="title"><?php echo $taxonomy; ?></legend>	
	<select name="<?php echo $taxonomy; ?>">
        <option value="">-- Choose a <?php echo $taxonomy; ?> --</option>              
	    <?php foreach( $terms as $term ) : ?>
            <?php /** Some cryptic short hand true:false */ ?>
            <?php $current_term == $term->name ? $selected = 'selected=selected' : $selected = null; ?>
            <option value="<?php echo $term->$value; ?>" my_term_id=<?php echo $term->term_id; ?> <?php echo $selected; ?>><?php echo $term->name; ?></option>              
	    <?php endforeach; ?>
    </select>
    </fieldset>
    <?php endif; ?>
<?php }

/**
 * @helper zm_base_build_radio( $taxonomy=null, $value='term_id' ) Build radio buttons of Terms based on a given Taxonomy.
 * @param value = the value to be use in the form, can be term_id or term_slug
 */
function zm_base_build_radio( $taxonomy=null, $value='term_id' ) {

    $terms = zm_base_get_terms( $taxonomy );
    $current_term = zm_base_current_term( $taxonomy );

    // @todo the below markup should be pulled out into a 'view' 
    ?>    
    <fieldset class="zm-base-<?php echo $taxonomy; ?>-container"><legend class="title"><?php echo $taxonomy; ?></legend>
    <?php foreach( $terms as $term ) : ?>
        <?php /** Some cryptic short hand true:false */ ?>
        <?php $current_term == $term->name ? $selected = 'checked=checked' : $selected = null; ?>
        <label for="<?php echo $term->$value; ?>">        
        <input type="radio" value="<?php echo $term->$value; ?>" id="<?php echo $term->term_id; ?>" my_term_id="<?php echo $term->term_id; ?>" name="<?php echo $taxonomy; ?>" <?php echo $selected; ?> />
        <?php echo $term->name; ?></label>
    <?php endforeach; ?>
    </fieldset>
<?php }

/**
 * @helper zm_base_build_checkbox( $taxonomy=null, $value='term_id' ) Build radio buttons of Terms based on a given Taxonomy.
 * @param value = the value to be use in the form, can be term_id or term_slug
 */
function zm_base_build_checkbox( $taxonomy=null, $value='term_id' ) {
    $terms = zm_base_get_terms( $taxonomy );
    $current_term = zm_base_current_term( $taxonomy );

    // @todo the below markup should be pulled out into a 'view' 
    ?>    
    <fieldset class="zm-base-<?php echo $taxonomy; ?>-container"><legend class="title"><?php echo $taxonomy; ?></legend>
    <?php foreach( $terms as $term ) : ?>
        <?php /** Some cryptic short hand true:false */ ?>
        <?php $current_term == $term->name ? $selected = 'checked=checked' : $selected = null; ?>
        <label for="<?php echo $term->$value; ?>">        
        <input type="checkbox" value="<?php echo $term->$value; ?>" id="<?php echo $term->term_id; ?>" my_term_id="<?php echo $term->term_id; ?>" name="<?php echo $taxonomy; ?>[]" <?php echo $selected; ?> />
        <?php echo $term->name; ?></label>
    <?php endforeach; ?>
    </fieldset>
<?php }
