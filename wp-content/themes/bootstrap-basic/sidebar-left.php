<?php if (is_active_sidebar('sidebar-left')) { ?> 
				<div class="col-md-4" id="sidebar-left">
					<?php do_action('before_sidebar'); ?> 
					<?php dynamic_sidebar('sidebar-left'); ?> 
				</div>
<?php } ?> 