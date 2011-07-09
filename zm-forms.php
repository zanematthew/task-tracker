<?php

/**
 * Helper Funciton Definition -- We define our helper function by the following: 
 * "Any funciton that maybe of use when building a WordPress theme." They should have the following 
 * characteristics:
 * 1. Are NOT used in hooks, filters or actions.
 * 2. Should NOT be dependent on each other i.e. can be pulled out and dropped into a functions.php
 */

/** 
 * @helper zm_base_posted_on() Retrives the following: 'Posted xxx days ago', no not like that..
 */
if ( ! function_exists( 'zm_base_posted_on' ) ) :
function zm_base_posted_on() {
    printf( __( ' Posted <span class="%1$s">%2$s</span> ago', 'zm_base' ),
        'zm-base-meta-prep-author',
        sprintf( '<span class="zm-base-date">%1$s</span>',
            esc_attr( human_time_diff( get_the_time('U'), current_time('timestamp') ) )
        )
    );
}
endif;

/** 
 * @helper zm_base_posted_by() Prints Posted by with author and link to author archive page 
 */
if ( ! function_exists( 'zm_base_posted_by' ) ) :
function zm_base_posted_by() {
    printf( __( '%1$s <span class="%2$s">%3$s</span> ', 'zm_base' ),
        sprintf( '<span class="zm-base-author-image zm-base-vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a> </span>',
            get_author_posts_url( get_the_author_meta( 'ID' ) ),
            sprintf( esc_attr__( 'View all posts by %s', 'zm_base' ), get_the_author() ),
            get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 40 ) )
        ),    
        'zm-meta-prep-author',
        sprintf( '<span class="zm-base-author-nickname zm-base-vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a> </span>',
            get_author_posts_url( get_the_author_meta( 'ID' ) ),
            sprintf( esc_attr__( 'View all posts by %s', 'collection' ), get_the_author() ),
            get_the_author_meta('nickname')
        )
    );
}
endif;

/**
 * @helper: zm_base_author_avatar() Prints ONLY the author image w/link to archive
 */
if ( ! function_exists( 'zm_base_author_avatar' ) ) :
function zm_base_author_avatar() {

    $content = sprintf( '<span class="zm-base-author-image zm-base-vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a> </span>',
            get_author_posts_url( get_the_author_meta( 'ID' ) ),
            sprintf( esc_attr__( 'View all posts by %s', 'zm_base' ), get_the_author() ),
            get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 40 ) )
        );
    $css = 'zm-meta-prep-author';
    
    printf( __( '%1$s <span class="%2$s"></span> ', 'zm_base' ), $content, $css );
}
endif;

/** 
 * @helper: zm_base_posted_in() Prints Posted in Category and Tags 
 */
if ( ! function_exists( 'zm_base_posted_in' ) ) :
function zm_base_posted_in() {

    // Retrieves tag list of current post, separated by commas.
    $tag_list = get_the_tag_list( '', ', ' );

    if ($tag_list) {
        $posted_in = __('<span class="zm-base-posted-in"> &nbsp;Posted in %1$s </span> Tags %2$s', 'collection');
    } elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
        $posted_in = __('<span class="zm-base-posted-in"> Posted in %1$s </span> ', 'collection');
    } else {
        $posted_in = null;
    }

    // Prints the string, replacing the placeholders.
    printf(
        $posted_in,
        get_the_category_list( ', ' ), // 1
        $tag_list,
        get_the_category_list( ', ' ) // 1
    );
}
endif;
		 
/** 
 * @helper zm_base_userdata( $user_id=CURRENT USER, $param ) Retrive user's information based on a param 
 */
function zm_base_userdata($user_id=1, $param=null){

    if ( $param == null )
        exit('need param');

/** 
 * @todo check agains list: http://codex.wordpress.org/Function_Reference/get_userdata	
 */
	$user_info = get_userdata( $user_id );	
	echo $user_info->$param ;
}

/** 
 * @helper zm_base_image_src( $size=null ) Print the src to an image given a size. Must be used in the_loop!
 */
function zm_base_image_src( $size=null ) {
/** 
 * @todo check for post->ID
 * @todo check against global image sizes 
 */
	if ( $size == null )
		$size = 'large';

	$src = wp_get_attachment_image_src( get_post_thumbnail_id(), $size );
	print $src[0];
}

/** 
 * @helper zm_base_get_the_term_list( $id=0, $taxonomy, $before, $sep, $after ) prints semantically structured term list.
 * tried using add_filter( 'get_the_term_list', 'new_get_the_term_list' ); but it wasn't working
 * ummm, I think these 2 functions do the same thing wtf! 
 */
function zm_base_get_the_term_list( $id = 0, $taxonomy, $before = '', $sep = ', ', $after = '' ) {

	$terms = get_the_terms( $id, $taxonomy, $before, $sep, $after);

    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        // print '<pre>';
        // var_dump( $terms );
        // print '</pre>';
        print 'error';
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

/** 
 * @todo -- add support for rss link 
 */
    foreach( $terms as $term ) {
        $html .= '<li class="zm-base-' . $term->slug . '">';
        $html .= '<a href="' . get_term_link( $term->slug, $term->taxonomy ) . '" title="' . sprintf(
        __( "View all %s"), $term->name ) . '" '. '>' . $term->name . '</a>';
        $html .= '<span class="zm-base-count">' . $term->count . '</span>';
        $html .= '</li>';
    }
    
/** 
 * @todo make sure term used as class name is 'clean', i.e. no spaces! all lower case.
 */
    $first = '<li class="zm-base-terms ' . $terms[0]->taxonomy . '"><span class="zm-base-title">' . $terms[0]->taxonomy . '</span><ul>';    
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
    
/** 
 * @todo better way to combine conditional 
 */
    if ( $post ) {
        $my_terms = get_the_terms( $post->ID, $taxonomy );
        if ( $my_terms ) {
            if ( is_wp_error( $my_terms ) ) {

print '<pre>'; 
print_r( $my_terms );
print '</pre>';
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

    /** All Terms */
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

/** 
 * @todo the below markup should be pulled out into a 'view' 
 */ ?>
	<?php if ( !empty( $terms ) ) : ?>
    <fieldset class="zm-base-<?php echo $taxonomy; ?>-container <?php echo $taxonomy; ?>-container">
    <legend class="zm-base-title"><?php echo $taxonomy; ?></legend>	
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

/** 
 * @todo the below markup should be pulled out into a 'view' 
 */ ?>    
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

/** 
 * @todo the below markup should be pulled out into a 'view' 
 */ ?>    
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

/** @todo port into zm */
/**
 * Next Post
 * modified from TwentyTen
 */
function collection_next_post() {
    global $post;

    // Retrieve next post link that is adjacent to current post.
    $nextPost = get_next_post( false );

    // Check to make sure we have a previous post
    if ( !empty( $nextPost ) ) {
        if ( function_exists( 'get_the_post_thumbnail' ) ) {
            $nextThumbnail = get_the_post_thumbnail( $nextPost->ID, 'thumbnail' );
        }
        
        /** @todo markup should be 'cleaner' */
        echo '<div class="image">';
        if ( isset( $nextThumbnail) && !empty( $nextThumbnail ) ) {
            next_post_link( '%link', "$nextThumbnail", false );
        }
        echo '</div>';

		print '<div class="content">';
		print '<span class="title">';
		next_post_link('%link', '%title');
		print '</span>';

        // Get our list of catgeories
        if (get_the_category($nextPost->ID)) {

            // Returns an array of objects
            $categories = get_the_category( $nextPost->ID );

            $catTotal = count($categories);
            $i = 0;

            print 'Category ';
            for ($i; $i < $catTotal; $i++) {
                print  '<a href="'.get_category_link($categories[$i]->cat_ID).'">'.$categories[$i]->cat_name.'</a> ';
            }
            print '<span class="posted-on">Posted on ';
            the_modified_time('m/d/ Y');
            print '</span>';
        } else {
            $category = 'no';
        }
        print '</div>';
    }
}

/** @todo port into zm */
function collection_previous_post() {
    global $post;

    // Retrieve next post link that is adjacent to current post.
    $prevPost = get_previous_post(false);

    // Check to make sure we have a previous post
    if (!empty($prevPost)) {
        if (function_exists('get_the_post_thumbnail')) {
            $prevthumbnail = get_the_post_thumbnail($prevPost->ID, 'thumbnail');
        }

        echo '<div class="image">';
        if (isset($prevthumbnail) && !empty($prevthumbnail)) {
           previous_post_link('%link',"$prevthumbnail", false);
        }
        echo '</div>';

        // Probally a better way to do this, but f-it, it works
        print '<div class="content">';
        print '<span class="title">';
        previous_post_link('%link', '%title');
        print '</span>';
        
        // Get our list of catgeories
        if (get_the_category($prevPost->ID)) {

            // Returns an array of objects
            $categories = get_the_category($prevPost->ID);

            $catTotal = count($categories);
            $i = 0;

            print 'Category ';

            for ($i; $i < $catTotal; $i++) {
                print  '<a href="'.get_category_link($categories[$i]->cat_ID).'">'.$categories[$i]->cat_name.'</a> ';
            }
            print '<span class="posted-on">Posted on ';

            the_modified_time('m/d/ Y');
            print '</span>';
        } else {
            $category = 'no';
        }
            print '</div>';
    }
}

/** @todo port into zm */
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own collection_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Ten 1.0
 */
if ( ! function_exists( 'collection_comment' ) ) :
function collection_comment( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    switch ( $comment->comment_type ) :
        case '' :
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <div id="comment-<?php comment_ID(); ?>">
        <div class="comment-author vcard">
            <?php echo get_avatar( $comment, 40 ); ?>
            <?php printf( __( '%s', 'collection' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
        </div> <!-- .comment-author .vcard -->
        <?php if ( $comment->comment_approved == '0' ) : ?>
            <em><?php _e( 'Your comment is awaiting moderation.', 'collection' ); ?></em>
            <br />
        <?php endif; ?>

        <div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
            <?php
                /* translators: 1: date, 2: time */
                printf( __( '%1$s at %2$s', 'collection' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'collection' ), ' ' );
            ?>
        </div><!-- .comment-meta .commentmetadata -->

        <div class="comment-body"><?php comment_text(); ?></div>

        <div class="reply">
            <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
        </div><!-- .reply -->
    </div><!-- #comment-##  -->

    <?php
            break;
        case 'pingback'  :
        case 'trackback' :
    ?>
    <li class="post pingback">
        <p><?php _e( 'Pingback:', 'collection' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'collection'), ' ' ); ?></p>
    <?php
            break;
    endswitch;
}
endif;

/** @todo move into init, Some short code that might be useful */
add_shortcode('zm_meta_list', 'zm_meta_list');
function zm_meta_list($atts, $content=NULL) {
    $booo =  "<div class='zm-meta-list column-two'>{$content}</div>";
    return $booo;
}

/**
 * Provide a list of links for images
 *
 * - need to set global post because we need the ID
 * - traverse the array meta_array for the index 'sizes'
 * - loop until we've built a list of links with appropiate html attributes
 */
function zm_image_download() {

    global $post;

    $meta_array = wp_get_attachment_metadata( $post->ID );
    $i = 0;
    $len = count( $meta_array['image_meta'] );

    foreach ( $meta_array['sizes'] as $key => $value ) {

        if ($i == 0) {
            print '<ul class="zm-meta">';
            $class = 'zm-item zm-item-first';
            the_title( '<li><h3 class="title">Download sizes for the <em>','</em> image.</h3></li>' );
        }  else {
            $class = 'zm-item';
        }

        $link = wp_get_attachment_image_src( get_post_thumbnail_id(), $key );

        /** @todo maybe printf this? */
        print "<li class='{$class}'><a href='{$link[0]}' target='_blank' title='Download the {$key} size'>{$key}</a> {$value['height']} x {$value['width']}</li>";

        if ( $i == $len - 1 ) {
            print '</ul>';
        }

        $i++;
    }
}

/**
 * Provide a list of exif information for images
 *
 * - traverse the array meta_array for the index 'image_meta'
 * - loop until we've built a list of exif information
 */
function zm_image_exif() {
    global $post;

    $meta_array = wp_get_attachment_metadata( $post->ID );
    $i = 0;
    $len = count( $meta_array['image_meta'] );

    foreach ( $meta_array['image_meta'] as $key => $value ) {

        if ($i == 0) {
            print '<ul class="zm-meta">';
            $class = 'zm-item zm-item-first';
            the_title('<li><h3 class="title">Exif data for the <em>','</em> image.</h3></li>');
        }  else {
            $class = 'item';
        }

        if ( $key == 'created_timestamp' ) {
            $key = 'created';
            $value = date('F, j, Y', $value);
        }

        if ( $key == 'focal_length' ) {
            $key = 'focal length';
        }

        if ( $key == 'shutter_speed' ) {
            $key = 'shutter speed';
        }

        print "<li class='{$class}'><span class='key'>{$key}</span> <span class='value'>{$value}</span></li>";

        if ( $i == $len - 1 ) {
            print '</ul>';
        }

        $i++;
    }
}

/**
 * @helper zm_back_to_post_link() Creats a 'Back to Post' link
 * @params --
 */
function zm_back_to_post_link() {
    global $post; ?>
    <a href="<?php echo get_permalink( $post->post_parent ); ?>" title="<?php esc_attr( printf( __( 'Return to %s', 'zm_base' ), get_the_title( $post->post_parent ) ) ); ?>" rel="gallery">
    <?php printf( __( '<span class="meta-nav">&larr; </span> Return to: %s', 'collection' ), get_the_title( $post->post_parent ) ); ?></a>
<?php }

/**
 * Thank you: http://dimox.net/wordpress-breadcrumbs-without-a-plugin/
 */
function dimox_breadcrumbs() {
 
  $delimiter = '&raquo;';
  $home = 'Home'; // text for the 'Home' link
  $before = '<span class="current">'; // tag before the current crumb
  $after = '</span>'; // tag after the current crumb
 
  if ( !is_home() && !is_front_page() || is_paged() ) {
    echo '<div class="breadcrumb-container">';
 
    global $post;
    $homeLink = get_bloginfo('url');
    echo '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
 
    if ( is_category() ) {
      global $wp_query;
      $cat_obj = $wp_query->get_queried_object();
      $thisCat = $cat_obj->term_id;
      $thisCat = get_category($thisCat);
      $parentCat = get_category($thisCat->parent);
      if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
      echo $before . single_cat_title('', false) . $after;
 
    } elseif ( is_day() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('d') . $after;
 
    } elseif ( is_month() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('F') . $after;
 
    } elseif ( is_year() ) {
      echo $before . get_the_time('Y') . $after;
 
    } elseif ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
        echo $before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
        echo $before . get_the_title() . $after;
      }
 
    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
      $post_type = get_post_type_object(get_post_type());
      echo $before . $post_type->labels->singular_name . $after;
 
    } elseif ( is_attachment() ) {
      $parent = get_post($post->post_parent);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
      echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
      echo $before . get_the_title() . $after;
 
    } elseif ( is_page() && !$post->post_parent ) {
      echo $before . get_the_title() . $after;
 
    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
      echo $before . get_the_title() . $after;
 
    } elseif ( is_search() ) {
      echo $before . 'Search results for "' . get_search_query() . '"' . $after;
 
    } elseif ( is_tag() ) {
      echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;
 
    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $before . 'Articles posted by ' . $userdata->display_name . $after;
 
    } elseif ( is_404() ) {
      echo $before . 'Error 404' . $after;
    }
 
    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
      echo __('Page') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }
 
    echo '</div>';
  }
} // end dimox_breadcrumbs()


function zm_base_truncate( $content, $size=35 ) {
    global $post;
    $content = get_post(get_post_thumbnail_id())->post_content;
    $length = strlen( $content );
    echo substr( $content, 0, $size);
    if ( $length > $size ) echo "...";
}
