<?php get_header(); ?>
<?php get_template_part('header-container','index'); ?>
<div class="zm-tt-container zm-tt-single-container">
    <div class="tt-wrapper">
    <div class="tt-glue">
        <div class="main-container">
            <?php load_template( plugin_dir_path( __FILE__ ) . 'default/navigation.php' ); ?>
            <div id="tt_main_target">
                <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
                    <script type="text/javascript">
                        _post_id = "<?php print $post->ID; ?>";
                    </script>                
                    <div <?php post_class('result')?>>

                        <div class="content">
                            <h1 class="title"><?php the_title(); ?></h1>
                            <?php the_content(); ?>
                        </div>

                        <div class="entry-utility" id="task_entry_utility_handle" data-post_id="<?php echo $post->ID;?>" data-post_type="<?php echo $post->post_type; ?>" data-template="theme/custom/task-entry-utility.php">
                            <div id="task_entry_utility_target">
                                <div style="text-align: center;">
                                <div class="tt_loading"></div>
                                Loading entry utility...
                                </div>
                            </div>
                            <?php load_template( plugin_dir_path( __FILE__ ) . 'custom/task-update.php' ); ?>                            
                        </div>

                    </div>
                                        
                    <div id="task_comment_target">
                        <div class="tt_loading" style="display: none;"></div>
                    </div>

                <?php endwhile; ?>
            </div>
        </div>
        <?php load_template( plugin_dir_path( __FILE__ ) . 'custom/task-sidebar.php' ); ?>    
    </div>
</div>
</div>
<?php get_footer(); ?>