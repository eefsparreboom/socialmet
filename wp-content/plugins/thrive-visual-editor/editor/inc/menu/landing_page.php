<span class="tve_options_headline">Landing Page Settings</span>
<span class="tve_options_headline" style="font-size: 13px;">Background</span>
<ul class="tve_menu">
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <i class="tve_icon tve_icon-15 tve_left"></i><span
                class="tve_caret tve_left" id="sub_01"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu color_selector">
                    <div class="tve_color_picker tve_left">
                        <span class="tve_options_headline tve_color_title">Custom Colors</span>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </li>
    <?php if (!empty($_POST['page_section_patterns'])) : ?>
        <li class="tve_clear"></li>
        <li class="tve_firstOnRow tve_ed_btn tve_btn_text">
            <div class="tve_option_separator">
                <span class="tve_ind tve_left">Background pattern</span>
                <span class="tve_caret tve_left" id="sub_02"></span>

                <div class="tve_clear"></div>
                <div class="tve_sub_btn" style="width: 700px;left:auto;right:-9px">
                    <div class="tve_sub active_sub_menu" style="">
                        <ul class="tve_clearfix">
                            <li style="display: none"></li>
                            <?php foreach ($_POST['page_section_patterns'] as $i => $_image) : ?>
                                <?php $_uri = $template_uri . '/images/patterns/' . $_image . '.png' ?>
                                <li class="tve_ed_btn tve_btn_text tve_left tve_section_color_change clearfix tve_click" data-ctrl="controls.click.change_pattern" data-plugin="tve_lp" data-pattern="1">
                                    <span class="tve_section_colour tve_left" style="background:url('<?php echo $_uri ?>')"></span>
                                    <span class="tve_left tve-pat-fixed"><?php echo 'pattern' . ($i + 1); ?></span>
                                    <input type="hidden" data-image="<?php echo $_uri; ?>"/>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
            </div>
        </li>
    <?php endif ?>
    <li class="tve_clear"></li>
    <li class="tve_firstOnRow tve_ed_btn tve_btn_text tve_center tve_click" id="tve_lp_bg_image">Background
        image...
    </li>
    <li class="tve_clear"></li>
    <li class="tve_text clearfix tve_firstOnRow">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="tve_lp_bg_fixed"
               value="1"><label
            for="tve_lp_bg_fixed" class="tve_left"> Static image</label> &nbsp;
    </li>
    <li class="tve_clear"></li>
    <li class="tve_firstOnRow tve_ed_btn tve_btn_text tve_center tve_click"
        id="tve_lp_clear_bg_color">Clear background color
    </li>
    <?php if (!empty($_POST['page_section_patterns'])) : ?>
        <li class="tve_clear"></li>
        <li class="tve_firstOnRow tve_ed_btn tve_btn_text tve_center tve_click"
            id="tve_lp_clear_bg_pattern">Clear background pattern
        </li>
    <?php endif ?>
    <li class="tve_clear"></li>
    <li class="tve_firstOnRow tve_ed_btn tve_btn_text tve_center tve_click"
        id="tve_lp_clear_bg_image">Clear background image
    </li>
    <li class="tve_clear"></li>
</ul>
<div class="tve_clear" style="height: 10px;"></div>
<span class="tve_options_headline" style="font-size: 13px;">Custom scripts</span>
<ul class="tve_menu">
    <li class="tve_click tve_ed_btn tve_btn_text" data-ctrl="controls.lb_open" id="lb_global_scripts" data-load="1">Setup custom scripts...</li>
    <li class="tve_clear"></li>
</ul>
<div class="tve_clear" style="height: 10px;"></div>
<span class="tve_options_headline" style="font-size: 13px;">Other settings</span>
<ul class="tve_menu">
    <li class="tve_firstOnRow tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left">Advanced options</span>
            <span class="tve_caret tve_left" id="sub_02"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn" style="width: 400px;left:auto;right:-9px">
                <div class="tve_sub active_sub_menu" style="">
                    <ul class="tve_clearfix">
                        <li class="tve_no_click">
                            <label for="tve_strip_css">
                                <input type="checkbox" id="tve_strip_css" class="tve_change" data-ctrl="controls.set_global_flag" data-flag="do_not_strip_css" value="1" />
                                Do not strip out Custom CSS from the &lt;head&gt; section
                            </label><br>
                            <div style="font-weight:normal;color:#111; line-height: 1;">
                                The Content Builder will strip out any Custom CSS from the &lt;head&gt; section from all Landing Pages built with it.
                                Usually, this is extra CSS that is not needed throughout the Lading Page.
                                <br>By ticking the checkbox above, you will disable this functionality, and all Custom CSS will be included.
                                <br>Please keep in mind that including this Custom CSS might prevent some of the above controls
                                to function properly, such as: background color, background image etc.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
</ul>
<div class="tve_clearfix"></div>