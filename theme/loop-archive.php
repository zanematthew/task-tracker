    <table>
        <thead>
            <tr>
                <th id="title"><span>Title</span></th>
                <th id="milestone"><span>Milestone</span></th>
                <th id="age"><span>Age</span></th>                
<!--                <th id="category"><span>Category</span></th> -->
                <th id="status"><span>Status</span></th>
                <th id="priority"><span>Priority</span></th>
                <th id="project"><span>Project</span></th>
            </tr>
        </thead>
        <?php // if ( $custom->have_posts() ) while ( $custom->have_posts() ) : $custom->the_post(); ?>
        <?php $x = 0; ?>
        <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
            <tr <?php post_class('result')?>>
	            <td><?php $x++; ?>
	                 <strong class="title"><a href="<?php the_permalink(); ?>" title="Link to project: <?php the_title(); ?>"><?php the_title(); ?></a></strong>
              	     <span class="comment-count"><?php comments_number(' '); ?></span>
         	         <div class="utility-container zm-base-hidden">
	                     <?php edit_post_link('Admin Edit', '' , ' |'); ?>
                         by <?php the_author(); ?> on <?php the_time(get_option('date_format')); ?> |
           	         </div>
	            </td>
                <td>
                    <div class="milestone-container"><?php print zm_base_get_the_term_list($post->ID, 'milestone'); ?></div>
                </td>
                <td>
                <?php project_age(); ?>
                </td>
<!--                <td>
                    <div class="category-container"><?php print zm_base_get_the_term_list($post->ID, 'category'); ?></div>
                </td>
-->
                <td>
                    <div class="status-container"><?php print zm_base_get_the_term_list($post->ID, 'status'); ?></div>
                </td>
                <td>    	            
                    <div class="priority-container"><?php print zm_base_get_the_term_list( $post->ID, 'priority'); ?></div>
                </td>                     
                <td>
                    <div class="project-container"><?php print zm_base_get_the_term_list($post->ID, 'project'); ?></div>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
