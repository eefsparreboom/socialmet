<?php
/**
 * Template for displaying single posts
 * 
 * @package bootstrap-basic
 */

get_header();

/**
 * determine main column size from actived sidebar
 */
$main_column_size = bootstrapBasicGetMainColumnSize();
?> 
<?php get_sidebar('left'); ?> 
				<div class="col-md-<?php echo $main_column_size; ?> content-area" id="main-column">
					<main id="main" class="site-main" role="main">
						<?php 
						while (have_posts()) {
							the_post();
							get_template_part('content', 'single');
                            $iExcludeFromArchive = get_the_ID();
							echo "\n\n";
							
							// If comments are open or we have at least one comment, load up the comment template
							if (comments_open() || '0' != get_comments_number()) {
								comments_template();
							}

							echo "\n\n";

						} //endwhile;
						?> 
					</main>
				</div>
<?php get_sidebar('right'); ?> 
<div class="col-md-12 archive-area" id="archive-column">
	<?php 
                $query = new WP_Query(array('post_type'=>'testimonial','post__not_in'=>array($iExcludeFromArchive)));
                if($query->have_posts()):
                    ?>
                        <div class="row">
                            
            <?php
                    while($query->have_posts()):$query->the_post();
                    $sImage = catch_that_image();
                   
                ?>
                                    <div class="col-md-3">
                                        <div class="cDivImage">
                                            <img src="<?php echo get_template_directory_uri().'/inc/thumb.php?zc=1&w=230&h=150&src='.$sImage; ?>" class="img-responsive" />
                                        </div>
                                        <div class="cDivContent">
                                            <h3><?php the_title(); ?></h3>
                                            <?php the_excerpt(); ?>
                                            &nbsp;<a href="<?php the_permalink(); ?>">Lees meer.. </a>
                                        </div>
                                    </div>
            <?php 
                    endwhile; 
                    wp_reset_postdata();
            ?>
                                
                        </div>
            <?php
                endif;
            
             ?>				
</div>
<?php get_footer(); ?> 