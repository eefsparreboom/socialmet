<?php
/**
 * The theme footer
 * 
 * @package bootstrap-basic
 */
?>

			</div><!--.site-content-->
			<div id="push"></div>
        </div><!--end #wrap div-->
        </div><!--end #wrap div-->
			
			<footer id="site-footer" role="contentinfo">
                <div class="container">
				<div id="footer-row" class="row site-footer">
					<div class="col-md-3 footer-left">
						<?php 
						if (!dynamic_sidebar('footer-left')) {
							printf(__('Powered by %s', 'bootstrap-basic'), 'WordPress');
							echo ' | ';
							printf(__('Theme: %s', 'bootstrap-basic'), '<a href="http://okvee.net">Bootstrap Basic</a>');
						} 
						?> 
					</div>
					<div class="col-md-3 footer-middle">
						<?php 
						if (!dynamic_sidebar('footer-middle')) {
							printf(__('Powered by %s', 'bootstrap-basic'), 'WordPress');
							echo ' | ';
							printf(__('Theme: %s', 'bootstrap-basic'), '<a href="http://okvee.net">Bootstrap Basic</a>');
						} 
						?> 
					</div>
					<div class="col-md-3 footer-right">
						<?php 
						if (!dynamic_sidebar('footer-right')) {
							printf(__('Powered by %s', 'bootstrap-basic'), 'WordPress');
							echo ' | ';
							printf(__('Theme: %s', 'bootstrap-basic'), '<a href="http://okvee.net">Bootstrap Basic</a>');
						} 
						?> 
					</div>
					
				</div>
				</div>
			</footer>
		
		
		
        
        <!--wordpress footer-->
		<?php wp_footer(); ?> 
        
	</body>
</html>