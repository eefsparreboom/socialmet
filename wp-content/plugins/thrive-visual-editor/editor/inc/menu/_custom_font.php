<li class="tve_ed_btn tve_btn_text">
	<div class="tve_option_separator">
		<span class="tve_ind tve_left" data-default="Custom Font">Custom Font</span><span class="tve_caret tve_left" id="sub_02"></span>

		<div class="tve_clear"></div>
		<div class="tve_sub_btn">
			<div class="tve_sub active_sub_menu tve_medium" style="min-width: 220px">
				<ul>
					<?php foreach ($fonts as $font): ?>
						<li style="font-size:15px;line-height:28px" class="tve_click tve_font_selector <?php echo $font['font_class'] ?>"
						    data-cls="<?php echo $font['font_class'] ?>"><?php echo $font['font_name'] . ' ' . $font['font_size'] ?></li>
					<?php endforeach; ?>
					<li><a class="tve_link" href="<?php echo $_POST['font_settings_url'] ?>" target="_blank">Add new Custom Font</a></li>
				</ul>
				<div class="tve_clear"></div>
			</div>
		</div>
	</div>
</li>
<li class="tve_ed_btn tve_btn_text tve_click" id="tve_clear_custom_font">Clear custom font</li>