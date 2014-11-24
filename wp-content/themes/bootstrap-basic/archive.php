<?php 
/**
 * Displaying archive page (category, tag, archives post, author's post)
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
						<?php if (have_posts()) { ?> 
						<div class="row">
						
						<?php 
                                                $i=0;
						/* Start the Loop */
						while (have_posts()) {
							the_post();

							/* Include the Post-Format-specific template for the content.
							 * If you want to override this in a child theme, then include a file
							 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
							 */
                                                        ?>
                                                    <div class="col-md-4">
                                                        <?php
							get_template_part('content', get_post_format());
                                                        ?>
                                                    </div>
                                                    <?php
                                                        $i++;
                                                        if($i===3){
                                                        ?>
                                                </div><div class="row">
                                                        <?php
                                                            $i=0;
                                                        }
						} //endwhile; 
						?> 
                                                </div>
						<?php bootstrapBasicPagination(); ?> 

						<?php } else { ?> 

						<?php get_template_part('no-results', 'archive'); ?> 

						<?php } //endif; ?> 
					</main>
				</div>
<?php get_sidebar('right'); ?> 
<?php get_footer(); ?> 