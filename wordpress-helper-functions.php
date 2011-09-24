<?php

/**
 * Helper Funciton Definition -- We define our helper function by the following: 
 * "Any funciton that maybe of use when building a WordPress theme." They should have the following 
 * characteristics:
 * 1. Are NOT used in hooks, filters or actions.
 * 2. Should NOT be dependent on each other i.e. can be pulled out and dropped into a functions.php
 * 
 * @version 0.0.1
 * @authoer Zane M. Kolnik
 */

/** 
 * Retrives the following: 'Posted xxx days ago', no not like that..
 *
 * @package helper 
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
 * Prints Posted by with author avatar and link to author archive page 
 *
 * @package helper
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
 * Prints ONLY the author image with link to author archive
 *
 * @package helper 
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
 * Prints Posted in Category and Tags 
 *
 * @package helper
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
 * Retrive user's information based on a param 
 *
 * @package helper
 * @param int $user_id 
 * @param string $param
 */
function zm_base_userdata($user_id=1, $param=null){

    if ( $param == null )
        exit('need param');

    /** @todo check list: http://codex.wordpress.org/Function_Reference/get_userdata */
	$user_info = get_userdata( $user_id );	
	echo $user_info->$param ;
}

/** 
 * Print the src to an image given a size. Must be used in the_loop!
 *
 * @package helper 
 * @param $size
 */
function zm_base_image_src( $size=null ) {
    /** @todo check for post->ID */
    /* @todo check against global image sizes */
	if ( $size == null )
		$size = 'large';

	$src = wp_get_attachment_image_src( get_post_thumbnail_id(), $size );
	print $src[0];
}

/** 
 * Prints semantically structured term list for a given POST.
 *
 * @package helper
 * @param int $id=0, 
 * @param string $taxonomy, $before, $sep, $after
 */
function zm_base_get_the_term_list( $id = 0, $taxonomy=null, $before = '', $sep = ', ', $after = '' ) {

    if ( is_array( $id ) )
        extract( $id );

// @todo wtf
$id = 0;

// @todo mo wtf
//    $terms = get_the_terms( $id, $taxonomy, $before, $sep, $after);

    $terms = get_the_terms( $id, $taxonomy );
    $my_link = null;

    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        print '&mdash;';
        return;
    } else {
        foreach ( $terms as $term ) {
            
            if ( isset( $link ) && $link == 'javascript://' )
                $my_link = 'javascript://';
            elseif ( isset( $link ) && $link == 'anchor' )
                $my_link = '#' . $term->taxonomy . '-'. $term->slug;            
            else            
                $my_link = get_term_link( $term, $taxonomy );
                
            if ( is_wp_error( $my_link ) )
                return $my_link;

            $title = sprintf( '%1$s <br /><em>%2$s</em>', sprintf( __("View all %s"), $term->name), $term->description );

            $term_links[] = '<a href="' . $my_link . '" title="'.$title.'" rel="'.$term->taxonomy . '_' . $term->slug.'" class="zm-base-'. $taxonomy.'-'.$term->slug .'">' . $term->name . '</a>';
        }
        $term_links = apply_filters( "term_links-$taxonomy", $term_links );
        return $before . join( $sep, $term_links ) . $after;
    }
}

/** 
 * This funtction will return a 'well' structured list of links for a given taxonomy 
 *
 * @package helper
 * @param string $taxonomy
 */
function zm_base_list_terms( $taxonomy ) {

    if ( is_array( $taxonomy ) )
        extract( $taxonomy );
    
    $terms = get_terms( $taxonomy );
    
    if ( !$terms )
        return;
        
    $i = 0;
    $len = count( $terms );
    $html = $first = $last = $my_link = null;

    /** @todo -- add support for rss link */
    // very fucking usefull http://php.net/manual/en/types.comparisons.php    
    if ( is_wp_error( $terms ) )
        return;
            
    foreach( $terms as $term ) {
        
        if ( isset( $link ) && $link == 'javascript://' )
            $my_link = 'javascript://';
        elseif ( isset( $link ) && $link == 'anchor' )
            $my_link = '#' . $term->taxonomy . '-'. $term->slug;            
        else            
            $my_link = get_term_link( $term->slug, $term->taxonomy );                                        

        // First
        if ( $i == 0 )
            $html .= '<li class="zm-base-title ' . $term->taxonomy . '">' . $term->taxonomy .'</li>';    
                        
        $title = sprintf( '%1$s <br /><em>%2$s</em>', sprintf( __("View all %s"), $term->name), $term->description );

        $html .= '<li class="zm-base-item zm-base-' . $term->slug . '">';
        $html .= '<a href="' . $my_link . '" title="'.$title.'" rel="' . $term->taxonomy . '_' . $term->slug . '">' . $term->name . '</a>';
        $html .= '<span class="zm-base-count">' . $term->count . '</span>';
        $html .= '</li>';
        $i++;
    }
    
    /** @todo make sure term used as class name is 'clean', i.e. no spaces! all lower case. */
    print '<ul>'.$html.'</ul>'; 
}

/** 
 * Determine the current term, idk fucking no what I'm doing.
 *
 * @package helper 
 * @param string $taxonomy
 */
function zm_base_current_term( $taxonomy ) {
    global $post;
    $current_term = null;
    
    /** @todo better way to combine conditional */
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
 * This mimics get_terms, but has shows error messages if we have one.
 *
 * @package helper
 * @param string $taxonomy
 * @todo $args should be a params, or look into using add_filter
 */
function zm_base_get_terms( $taxonomy ) {

    /** All Terms */
    $args = array(
        'orderby' => 'name',
        'hide_empty' => false
         );

    $terms = get_terms( $taxonomy, $args );

    if ( is_wp_error( $terms ) ) {
//        exit( "Opps..." . $terms->get_error_message() . "..dog, cmon, fix it!" );
    }

    return $terms;
}

/**
 * Build an option list of Terms based on a given Taxonomy.
 *
 * @package helper 
 * @uses zm_base_get_terms to return the terms with error checking
 * @param string $taxonomy
 * @param mixed $value, the value to be used in the form field, can be term_id or term_slug
 */
function zm_base_build_options( $taxonomy=null, $value='term_id' ) {
    
    if ( is_array( $taxonomy ) )
        extract( $taxonomy );

    // white list
    if ( empty( $prepend ) )
        $prepend = null; 
    
    if ( !isset( $label ) )     
        $label = $taxonomy;        

// var_dump( $label );
// wp_die('dead');
   
    /** All Terms */
    $args = array(
        'orderby' => 'name',
        'hide_empty' => false
         );

    $terms = get_terms( $taxonomy, $args );

    if ( is_wp_error( $terms ) ) {
//        exit( "Opps..." . $terms->get_error_message() . "..dog, cmon, fix it!" );
        $terms = false;
    }

    $current_term = zm_base_current_term( $taxonomy );

    /** @todo the below markup should be pulled out into a 'view' */ 
    ?>
	<?php if ( $terms ) : ?>
    <fieldset class="zm-base-<?php echo $taxonomy; ?>-container <?php echo $taxonomy; ?>-container">
    <legend class="zm-base-title"><?php echo $label; ?></legend>	
	<select name="<?php echo $taxonomy; ?>" id="select_<?php echo $taxonomy; ?>">
        <option value="">-- Choose a <?php echo $taxonomy; ?> --</option>              
	    <?php foreach( $terms as $term ) : ?>
            <?php /** Some cryptic short hand true:false */ ?>
            <?php $current_term == $term->name ? $selected = 'selected=selected' : $selected = null; ?>
            <option value="<?php echo $prepend; ?><?php echo $term->$value; ?>" data-value="<?php echo $term->slug; ?>" class="taxonomy-<?php echo $taxonomy; ?> term-<?php echo $term->slug; ?> <?php echo $taxonomy; ?>-<?php echo $term->term_id; ?>" <?php echo $selected; ?>><?php echo $term->name; ?></option>
	    <?php endforeach; ?>
    </select>
    </fieldset>
    <?php endif; ?>
<?php }

/**
 * Build radio buttons of Terms based on a given Taxonomy. Also will default current term.
 *
 * @package helper 
 * @uses zm_base_get_terms to return the terms with error checking 
 * @uses zm_base_current_term() to get the current term for post type currently being viewed
 * @param string $taxonomy
 * @param string $value, The value to be used in the 'name' field of the form
 */
function zm_base_build_radio( $taxonomy=null, $options=array() ) {

    // @todo need an array of "choices" like value => array( 'term_id', 'term_name' )
    $defaults = array(
        'default' => null,
        'value' => 'term_id'
    );
    extract( $defaults );
    extract( $options );

    $terms = zm_base_get_terms( $taxonomy );
    
    if ( !empty( $default ) )
        $current_term = $default;
    else 
        $current_term = zm_base_current_term( $taxonomy );
        
    /** @todo the below markup should be pulled out into a 'view' */ 
    ?>    
    <?php if ( $terms ) : ?>
    <fieldset class="zm-base-<?php echo $taxonomy; ?>-container"><legend class="zm-base-title"><?php echo $taxonomy; ?></legend>
    <?php foreach( $terms as $term ) : ?>
        <?php /** Some cryptic short hand true:false */ ?>
        <?php $current_term == $term->name ? $selected = 'checked=checked' : $selected = null; ?>
        <label for="<?php echo $term->$value; ?>">        
        <input type="radio" value="<?php echo $term->$value; ?>" id="<?php echo $term->term_id; ?>" my_term_id="<?php echo $term->term_id; ?>" name="<?php echo $taxonomy; ?>"
        <?php echo $selected; ?> />
        <?php echo $term->name; ?></label>
    <?php endforeach; ?>
    </fieldset>
    <?php endif; ?>
<?php }


function zm_base_build_input( $taxonomy=null ) {

    if ( is_array( $taxonomy ) )
        extract( $taxonomy );

    // @todo need to merge 
    $defaults = array(
        'value' => 'term_id'
    );
    
    // white list
    if ( empty( $prepend ) )
        $prepend = null;    
        
    extract( $defaults );

    /** All Terms */
    $args = array(
        'orderby' => 'name',
        'hide_empty' => false
         );

    $terms = get_terms( $taxonomy, $args );

    if ( is_wp_error( $terms ) ) {
//        exit( "Opps..." . $terms->get_error_message() . "..dog, cmon, fix it!" );
        $terms = false;
    }    

    if ( !empty( $default ) )
        $current_term = $default;
    else 
        $current_term = zm_base_current_term( $taxonomy );
        
    /** @todo the below markup should be pulled out into a 'view' */ 
    ?>    
    <?php if ( $terms ) : ?>    
    <fieldset class="zm-base-<?php echo $taxonomy; ?>-container"><legend class="zm-base-title"><?php echo $taxonomy; ?></legend>
    <?php foreach( $terms as $term ) : ?>
        <?php /** Some cryptic short hand true:false */ ?>
        <?php $current_term == $term->name ? $selected = 'checked=checked' : $selected = null; ?>
        <label for="<?php echo $term->$value; ?>">        
        <input type="<?php echo $type; ?>" value="<?php echo $prepend; ?><?php echo $term->$value; ?>" class="taxonomy-<?php echo $taxonomy; ?> term-<?php echo $term->slug; ?> <?php echo $taxonomy; ?>-<?php echo $term->term_id; ?>" id="<?php echo $term->term_id; ?>" name="<?php echo $taxonomy; ?>"
        <?php echo $selected; ?> />
        <?php echo $term->name; ?></label>
    <?php endforeach; ?>
    </fieldset>
    <?php endif; ?>
<?php }

/**
 * Build radio buttons of Terms based on a given Taxonomy.
 *
 * @package helper 
 * @uses zm_base_get_terms to return the terms with error checking 
 * @uses zm_base_current_term() to get the current term for post type currently being viewed
 * @param string $taxonomy
 * @param string $value, The value to be used in the 'name' field of the form
 */
function zm_base_build_checkbox( $taxonomy=null, $value='term_id' ) {

    /** All Terms */
    $args = array(
        'orderby' => 'name',
        'hide_empty' => false
         );

    $terms = get_terms( $taxonomy, $args );

    if ( is_wp_error( $terms ) ) {
//        exit( "Opps..." . $terms->get_error_message() . "..dog, cmon, fix it!" );
        $terms = false;
    }

    $current_term = zm_base_current_term( $taxonomy );

    /** @todo the below markup should be pulled out into a 'view' */ 
    ?>    
    <fieldset class="zm-base-<?php echo $taxonomy; ?>-container"><legend class="zm-base-title"><?php echo $taxonomy; ?></legend>
    <?php foreach( $terms as $term ) : ?>
        <?php /** Some cryptic short hand true:false */ ?>
        <?php $current_term == $term->name ? $selected = 'checked=checked' : $selected = null; ?>
        <label for="<?php echo $term->$value; ?>">        
        <input type="checkbox" value="<?php echo $term->$value; ?>" id="<?php echo $term->term_id; ?>" my_term_id="<?php echo $term->term_id; ?>" name="<?php echo $taxonomy; ?>[]" <?php echo $selected; ?> />
        <?php echo $term->name; ?></label>
    <?php endforeach; ?>
    </fieldset>
<?php }

/**
 * Retrive the next post type modified from TwentyTen
 *
 * @package helper
 */
function zm_next_post() {
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

/**
 * Retrive the previous post type modified from TwentyTen
 *
 * @package helper
 */
function zm_previous_post() {
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
if ( ! function_exists( 'zm_comment' ) ) :
function zm_comment( $comment, $args, $depth ) {
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

/**
 * Provide a list of links to the image sizes.
 *
 * @package helper
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
 * @package helper
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
 * Creats a 'Back to Post' link
 *
 * @package helper 
 */
function zm_back_to_post_link() {
    global $post; ?>
    <a href="<?php echo get_permalink( $post->post_parent ); ?>" title="<?php esc_attr( printf( __( 'Return to %s', 'zm_base' ), get_the_title( $post->post_parent ) ) ); ?>" rel="gallery">
    <?php printf( __( '<span class="meta-nav">&larr; </span> Return to: %s', 'collection' ), get_the_title( $post->post_parent ) ); ?></a>
<?php }

/**
 * Truncate a content.
 *
 * @package helper 
 * @param string $content, The content we want to truncate
 * @param int $size, The size we want to truncate to
 */
function zm_base_truncate( $content, $size=35 ) {
    global $post;
    $content = get_post(get_post_thumbnail_id())->post_content;
    $length = strlen( $content );
    echo substr( $content, 0, $size);
    if ( $length > $size ) echo "...";
}

/**
 * Returns a linked list of terms for a given taxonomy
 *
 * @package helper 
 * @prama string $zm_term
 */
function zm_term_links( $zm_term=null) {

    // Set our global, we'll use this to check the "current" state
    global $wp_query;

    // Check if we have a term
    if ( !isset( $zm_term ) )
       die('no term, gtfo');

    // Does our term exists
    if ( !taxonomy_exists( $zm_term ) )
        die('taxo no exsito, gtfo');

    // Our object of terms
    $terms = get_terms( $zm_term );
    $x = 1;
    $count = count( $terms );
    $html = null;
    $class = '';    

    foreach ($terms as $term) {	    

        // First
        if ( $x == 1 ) {
            $html .= '<li class="zm-title">'. $zm_term . '</li>';
            $bar = '<span class="zm-bar">|</span>';
        // "Middle"
        } elseif ( $x == $count ) {
            $bar = '';
        // Last
        } else {
            $bar = '<span class="zm-bar">|</span>';
        }

        // Determine if the user is currently viewing our term
        if ( $wp_query->query_vars['term'] == $term->slug ) {
            // Set a class for styling
            $class = 'zm-current';
            $term_html = $term->name;
        } else {
            // If this is NOT the current term the we wrap the term in an "a" tag
            $term_html = '<a href="'.get_term_link( $term->slug , $zm_term ).'">'.$term->name.'</a>';
        }    

        $html .= '<li class="'.$class. ' zm-'. $term->slug.'">' . $term_html . $bar . '</li>';
        $x++;
    }

    print '<ul>' . $html . '</ul>';
}
