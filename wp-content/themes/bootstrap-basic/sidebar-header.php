<?php if (is_active_sidebar('sidebar-header')) { ?> 
				<div class="col-md-3" id="sidebar-header">
					<?php do_action('before_sidebar'); ?> 
					<?php dynamic_sidebar('sidebar-header'); ?> 
				</div>
<?php } ?> 