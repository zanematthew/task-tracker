<?php

global $wp_query;

// yes we are trusting that it is always the first index of our array std class crap
$current_post_type = $wp_query->posts[0]->post_type;

if ( is_null( $current_post_type ) ) {
    $url = get_bloginfo('url');
    header("Location: {$url}");
}
?>
<?php get_header(); ?>
<?php get_template_part('header-container','index'); ?>
<?php
$my_cpt = get_post_types( array( 'name' => $current_post_type), 'objects' );
$my_cpt_taxes = $my_cpt[ $current_post_type ]->taxonomies;
?>
<div class="zm-tt-container zm-tt-archive-container">
    <div class="tt-glue">
        <div class="main-container">
            
            <div class="tt-filter-container">
                <ul class="inline">
                    <li><a href="javascript://" id="filter_handle" tt_template="default/navigation-filter.php" data-post_type="<?php print  $current_post_type; ?>">Advanced Filter</a></li>
                </ul>
            </div>

            <div id="tt_filter_target"></div>                            

            <div id="tt_main_target">
                <div class="zm-tt-archive-container" >
                   <table id="archive_table">
                        <thead>
                            <tr>
                                <th id="title"><span>Title</span></th>
                                <?php foreach ( $my_cpt_taxes as $tax ) : ?>
                                    <th><span><?php print str_replace( "-", " ", $tax ); ?></span></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <?php $x = 0; ?>
                        <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
                            <tr <?php post_class('result')?>>
                                <td><?php $x++; ?>
                                     <strong class="title"><a href="<?php the_permalink(); ?>" title="Link to project: <?php the_title(); ?>"><?php the_title(); ?></a></strong>
                                     <span class="comment-count"><?php comments_number(' '); ?></span>
                                     <div class="utility-container zm-base-hidden">
                                         <?php edit_post_link('Admin Edit', '' , ' |'); ?>
                                         by <?php the_author(); ?> on <?php the_time(get_option('date_format')); ?> |
                                         <a href="#delete" class="default_delete" data-post_id="<?php print $post->ID; ?>" data-security="<?php print wp_create_nonce( 'tt-ajax-forms' );?>">Delete</a>
                                     </div>
                                </td>
                                <?php foreach( $my_cpt_taxes as $tax ) : ?>
                                    <td>
                                        <div class="milestone-container zm-base-item">
                                            <?php print zm_base_get_the_term_list( array( 'post_id' => $post->ID, 'taxonomy' => $tax )); ?>
                                        </div>
                                    </td>
                                <?php endforeach; ?>                                
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="zm-tt-sidebar-container">
            <?php foreach ( $my_cpt_taxes as $tax ) : ?>
                <?php zm_base_list_terms( array('taxonomy' => $tax ) ); ?>
            <?php endforeach; ?>        
        </div>
    </div>
</div>
<?php get_footer(); ?>
