<?php $has_templates = isset($_POST['user_templates']) && is_array($_POST['user_templates']) ?>
<div class="tve_option_separator">
    <div class="tve_icon tve_icon-94 tve_left"></div>
    <span class="tve_expanded tve_left">Content Templates</span>
    <span class="tve_caret_top tve_right tve_sub_btn tve_sub_btn_caret tve_expanded" id="sub_02"></span>

    <div class="tve_clear"></div>
    <div class="tve_sub_btn">
        <div class="tve_sub">
            <ul id="user_template_list">
                <?php if ($has_templates): ?>
                    <?php foreach ($_POST['user_templates'] as $user_template_id => $template_name): ?>
                        <li id="tve_user_template" class="cp_draggable user_template_item tve_clearfix">
                            <span class="tve_left"><?php echo urldecode(stripslashes($template_name)); ?></span>
                            <span class="tve_icon tve_icon-51 tve_right tve_click" data-ctrl="controls.click.remove_user_template"></span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <li class="hide-on-tpl-save"<?php echo $has_templates ? ' style="display: none"' : '' ?>>No Content Templates yet</li>
            </ul>
        </div>
    </div>
</div>