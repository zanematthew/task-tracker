<?php
/**
 * if you want to customize any of the template parts, just make 
 * a loop-single.php, header-container-single.php, etc. in your child theme
 */
?><?php get_header(); ?>
<?php get_template_part( 'header-container', 'single' ); ?>
<div class="container_12">
    <div class="grid_12">
        <div class="main-container">
            <article class="grid_8 alpha">
                <?php load_template( MY_PLUGIN_DIR . '/theme/navigation.php' ); ?>   
                <?php load_template( MY_PLUGIN_DIR . '/theme/loop-single.php' ); ?>
            </article>
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>
<?php get_template_part( 'footer-container', 'single' ); ?>
<?php get_footer(); ?>
