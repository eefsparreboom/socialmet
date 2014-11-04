<?php $is_landing_page_related = empty($_POST['landing_page_lightbox']) && empty($_POST['landing_page']) ?>
<div class="tve_cpanel_sec">
    <?php if (true || $is_landing_page_related) : /* each landing page has it's own associated style family */ ?>
        <div class="tve_option_separator tve_dropdown_submenu tve_drop_style tve_left">
            <div class="tve_ed_btn tve_btn_text">
                <div class="tve_icon tve_icon-31 tve_left tve_sub_btn" id="sub_02"></div>
                <span class="tve_left tve_expanded"><?php echo empty($_POST['loaded_style']) ? 'Style Family' : $_POST['loaded_style'] ?></span>

                <div class="tve_clear"></div>
            </div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <?php foreach ($_POST['style_families'] as $style_family_name => $style_family_location): ?>
                            <li id="tve_style_family_<?php echo strtolower($style_family_name); ?>"
                                class="tve_btn_style_family tve_click" data-skip-undo="1" data-ctrl="controls.click.style_family"><?php echo $style_family_name; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif ?>
    <div class="tve_option_separator tve_dropdown_submenu tve_drop_flip tve_left"<?php echo ($is_landing_page_related) ? '' : ' style="margin-left: 0"' ?>>
        <div class="tve_ed_btn tve_btn_icon">
            <div class="tve_icon tve_icon-53"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <li id="tve_flipEditor" class="tve_click" data-skip-undo="1" data-ctrl="controls.click.flip_editor">Switch Editor Side</li>
                        <li id="tve_flipColor" class="tve_click" data-skip-undo="1" data-ctrl="controls.click.flip_editor_color">Change Editor Color</li>
                    </ul>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="tve_clear"></div>
</div>
<div class="tve_cpanel_sec tve_btn_collapse">
    <div class="tve_cpanel_head tve_clearfix tve_click" id="tve_collapse_editor_btn" data-skip-undo="1" data-ctrl="controls.click.collapse_editor">
        <div class="tve_icon tve_icon-32 tve_left tve_expanded "></div>
        <div class="tve_icon tve_icon-33 tve_left tve_collapsed"></div>
        <span class="tve_left tve_expanded">Collapse Editor</span>
    </div>
    <a href="" class="tve_logo">
        <span class="tve_cpanel_logo"></span>
    </a>

    <div class="tve_clear"></div>
</div>