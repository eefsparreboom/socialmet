<?php
/**
 * The theme header
 * 
 * @package bootstrap-basic
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]>  <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>     <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>     <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?php wp_title('|', true, 'right'); ?></title>
		<meta name="viewport" content="width=device-width">

		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
		
		<!--wordpress head-->
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<!--[if lt IE 8]>
			<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
		<![endif]-->
		
		<div id="wrap">
                    <div class="container-fluid cDivHeaderContainer">
                            <div class="container cDivHeaderTop">
				<div class="row site-branding">
                                    <div class="col-xs-10 col-md-9 site-title">
                                            <h1 class="site-title-heading">
                                                    <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home"><img src="<?php header_image(); ?>" class="img-responsive" alt="" /></a>
                                            </h1>

                                    </div>
                                    
                                    <div class="visible-xs col-xs-2 cDivToggleMenu">
                                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-primary-collapse">
                                            <span class="sr-only"><?php _e('Toggle navigation', 'bootstrap-basic'); ?></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                        </button>
                                    </div>
                                        <?php get_sidebar('header'); ?> 
                                </div>
                            </div>
                    </div>
                    <div class="container-fluid cDivHeaderNavigation">
                            <div class="container">
                                <div class="row">
					<div class="col-md-12 page-header-top-right">
						<nav class="navbar navbar-default" role="navigation">
							<div class="navbar-header">

							</div>
							
							<div class="collapse navbar-collapse navbar-primary-collapse">
								<?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'menu_class' => 'nav navbar-nav', 'walker' => new BootstrapBasicMyWalkerNavMenu())); ?> 
								
							</div><!--.navbar-collapse-->
						</nav>
					</div>
                                </div>
                            </div><!--.site-branding-->
                    </div><!--.site-branding-->
				
				
			
		
		
		<div class="container page-container">
			<?php do_action('before'); ?> 
			
			<?php if(is_page('home')){ 
                $query = new WP_Query(array('post_type'=>'header'));
                if($query->have_posts()):
                    ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="cDivHeaderSlider">
            <?php
                    while($query->have_posts()):$query->the_post();
                    $oImage = get_field('image');
                    $sQuote = get_field('quote_text');
                    $sLink = get_field('button_link');
                    $sButtonText = get_field('button_text');
                ?>
                                    <div class="cDivSlide">
                                        <div class="cDivImage">
                                            <img src="<?php echo $oImage['url']; ?>" class="img-responsive" />
                                        </div>
                                        <div class="cDivContent col-md-6">
                                            <div class="cDivQuote">
                                                &OpenCurlyDoubleQuote; <?php echo $sQuote; ?> &CloseCurlyDoubleQuote;
                                            </div>
                                            <a class="btn btn-primary" href="<?php echo $sLink;?>">
                                                <?php echo $sButtonText;?>
                                            </a>
                                        </div>
                                    </div>
            <?php 
                    endwhile; 
                    wp_reset_postdata();
            ?>
                                </div>
                            </div>
                        </div>
            
            
            <?php
                endif;
            
            } ?>
			<!-- Movie Modal -->
            <div class="modal fade" id="movieModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">&nbsp;</h4>
                  </div>
                  <div class="modal-body">
                      <div class="embed-responsive embed-responsive-16by9">
                    <?php echo do_shortcode('[video width="1280" height="720" mp4="http://voedingleeft.kominski.net/wp-content/uploads/2014/10/Keer-Diabetes-Om-Trailer.mp4"][/video]');?>
                      </div>
                  </div>
                  
                </div>
              </div>
            </div>
			<div id="content" class="row row-with-vspace site-content">