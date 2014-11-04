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
						<?php 
                        $aFaqCategories = get_terms('faq_categories');
                        foreach($aFaqCategories AS $oCategory){
                            $args = array(
                                'post_type'=>'faq',
                                'faq_categories' => $oCategory->slug
                            );
                            $query = new WP_Query($args);
                            // The Loop
                            if ( $query->have_posts() ) {
                                ?>
                                <h1 class="entry-title"><?php echo $oCategory->name; ?></h1>
                                <div class="cDivFaqBlock">
                        <?php
                                
                                while ( $query->have_posts() ) {
                                    $query->the_post();
                                    echo '<div class="cDivFaqItem">';
                                    echo '<div class="cDivFaqTitle">' . get_the_title() . '</div>';
                                    echo '<div class="cDivFaqContent">' . get_the_content() . '</div>';
                                    echo '</div>';
                                }
                                ?>
                                </div>
                                <?php
                            } 
                            /* Restore original Post Data */
                            wp_reset_postdata();
                        }
                        ?>
					</main>
				</div>
<?php get_sidebar('right'); ?> 
<?php get_footer(); ?> 