<?php get_header(); 

global $wp_query;

if ( $wp_query->query_vars['term'] == 'closed' || $wp_query->query_vars['term'] == 'aborted' )
    $terms = null;
else    
    $terms = array( 'closed', 'aborted' );

$args = array(
    'post_type' => 'task',
    'post_status' => 'publish',    
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => $wp_query->query_vars['taxonomy'],
            'field' => 'slug',
            'terms' => $wp_query->query_vars['term']            
            ),        
        array(
            'taxonomy' => 'status',
            'field' => 'slug',
            'terms' => $terms,
            'operator' => 'NOT IN'            
        )
    )
);

/*
$args = array( 
    'post_type' => 'task',
    'post_status' => 'publish',
    'status__not_in' => array( 
        43, 
        44 
    )    
);
*/

$my_query = new WP_Query( $args );

$comments_count = wp_count_comments( $post->ID);

if ( $comments_count->total_comments == 1 ) 
    $comment_class = 'comment-count';

elseif ( $comments_count->total_comments > 1 ) 
    $comment_class = 'comments-count';
else 
    $comment_class = '';

?>
<?php get_template_part('header-container','index'); ?>
    <div class="zm-tt-container zm-tt-archive-container">
        <div class="tt-glue">
            <div class="main-container">
                <?php load_template( MY_PLUGIN_DIR . 'theme/default/navigation.php' ); ?>
                <div class="tt-filter-container">
                    <ul class="inline">


<li class="icon-container"><a href="#filter-task" id="filter_handle" class="icon-find" tt_template="default/navigation-filter.php" data-post_type="<?php global $wp_query; print $wp_query->posts[0]->post_type; ?>">Filter</a></li>                        
                    </ul>
                </div>
                <div id="tt_filter_target"></div>                
                <div class="zm-tt-archive-container" >
                   <table id="archive_table">
                        <thead>
                            <tr>
                                <th id="title"><span>Title</span></th>                
                                <th id="age"><span>Age</span></th>
                                <th id="status"><span>Status</span></th>
                                <th id="priority"><span>Priority</span></th>
                                <th id="project"><span>Project</span></th>                
                                <th id="project"><span>Type</span></th>                                            
                            </tr>
                        </thead>
                        <?php $x = 0; ?>
                        <?php // if ( $my_query->have_posts() ) while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
                        
                        <?php if ( $my_query->have_posts() ) while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
                            <?php $x++; ?>
                            <tr <?php post_class('result')?>>
                                <td>
                                     <strong class="title"><a href="<?php the_permalink(); ?>" title="Link to project: <?php the_title(); ?>">#<?php the_ID(); ?> <?php the_title(); ?></a></strong>
                                     <span class="<?php print $comment_class; ?>"><a href="<?php the_permalink(); ?>#comments_target" title="<?php comments_number(); ?>"><?php comments_number(' '); ?></a></span>

                                     <div class="utility-container zm-base-hidden">
                                         <?php edit_post_link('Admin Edit', '' , ' |'); ?>
                                         by <?php the_author(); ?> on <?php the_time(get_option('date_format')); ?> |
                                         <?php if ( is_user_logged_in() ) : ?>
                                         <a href="#delete" class="default_delete" data-post_id="<?php print $post->ID; ?>" data-security="<?php print wp_create_nonce( 'tt-ajax-forms' );?>">Delete</a>
                                         <?php endif; ?>
                                     </div>
                                </td>
                                <td>
                                    <?php tt_task_age(); ?>
                                </td>
                                <td>
                                    <div class="status-container">
                                        <?php print zm_base_get_the_term_list( array( 'link' => true, 'post_id' => $post->ID, 'taxonomy' => 'status' ) ); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="priority-container zm-base-item">
                                        <?php print zm_base_get_the_term_list( array( 'link' => true, 'post_id' => $post->ID, 'taxonomy' => 'priority' ) ); ?>
                                    </div>
                                <td>
                                    <div class="project-container zm-base-item">
                                        <?php print zm_base_get_the_term_list( array( 'link' => true, 'post_id' => $post->ID, 'taxonomy' => 'project' ) ); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="type-container">
                                        <?php print zm_base_get_the_term_list( array( 'link' => true, 'post_id' => $post->ID, 'taxonomy' => 'type' ) ); ?>
                                    </div>
                                </td>                            
                            </tr>
                        <?php endwhile; ?>
                    </table>
                    <?php tt_json_feed( 'task',  array( 'status', 'priority', 'project', 'phase', 'assigned' ) ); ?>
                </div>
            </div>
            <div class="zm-tt-sidebar-container">
                        <ul class="text">
                <li class="zm-base-title">Info</li>
                <li>
                    <?php if ( !is_user_logged_in() ) : ?>
                        <div class="zm-tt-form-container">
                            <a href="javascript://" id="ltfo_handle" class="login" tt_template="theme/default/login.php" title="Click to login and create a task">Login to create a task</a>
                        </div>
                    <?php endif; ?>

                    <?php if ( is_user_logged_in() ) : ?>
                        <p><?php global $user_login; get_currentuserinfo(); ?>Welcome back <em><?php echo $user_login; ?></em>!</p>
                        <p class="utility-container">
                            <?php if ( current_user_can( 'administrator' ) ) : ?>
                                <span class="mini-button"><a href="<?php bloginfo('wpurl');?>/wp-admin" title="Click to go to WordPress admin">WordPress Admin</a></span>
                            <?php endif; ?>
                            <span class="mini-button"><a href="<?php echo wp_logout_url( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); ?>" title="Click here to Log the fuck out">Logout</a></span>
                        </p>
                    <?php endif; ?>
                </li>
            </ul>        

                <?php zm_base_list_terms( array('taxonomy' => 'status', ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'priority' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'project' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'phase' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'assigned' ) ); ?>
            </div>
        </div>
    </div>
<?php tt_json_feed( 'task',  array( 'status', 'priority', 'project', 'phase', 'assigned' ) ); ?>
<?php get_footer(); ?>
