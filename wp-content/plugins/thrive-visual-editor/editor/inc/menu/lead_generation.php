<span class="tve_options_headline">Lead Generation options</span>
<ul class="tve_menu">
    <?php $has_custom_colors = true; include dirname(__FILE__) . '/_custom_colors.php' ?>
    <?php include dirname(__FILE__) . '/_margin.php' ?>
    <li>
        <div id="lb_lead_generation_code" class="tve_ed_btn tve_btn tve_btn_text tve_click" data-ctrl="controls.lb_open">Autoresponder Code</div>
    </li>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left">Vertical</span><span id="sub_02" class="tve_caret tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu" style="display: block;">
                    <ul>
                        <li class="lead_generation_style tve_click" id="thrv_lead_generation_vertical" data-ctrl="controls.click.lead_generation_style">
                            <div class="lead_generation_image" id="lead_generation_vertical_image"></div>
                            <div>Vertical</div>
                        </li>
                        <li class="lead_generation_style tve_click" id="thrv_lead_generation_horizontal" data-ctrl="controls.click.lead_generation_style">
                            <div class="lead_generation_image" id="lead_generation_horizontal_image"></div>
                            <div>Horizontal</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li>
        <label>
            Button text <input id="lead_generation_button_text" class="tve_text tve_change" type="text" value=""/>
        </label>
    </li>
    <li class="tve_btn_text">
        <label class="tve_text">&nbsp;Align:&nbsp; </label>
    </li>
    <li id="tve_leftBtn" class="btn_alignment tve_alignment_left">
        Left
    </li>
    <li id="tve_centerBtn" class="btn_alignment tve_alignment_center">
        Center
    </li>
    <li id="tve_rightBtn" class="btn_alignment tve_alignment_right">
        Right
    </li>
    <li>
        <div id="tve_fullwidthBtn" class="tve_ed_btn tve_btn_text tve_center btn_alignment tve_left">Full Width</div>
    </li>
    <li>
        <label>
            <input id="lead_generation_form_target" class="tve_text tve_change" type="checkbox" value="_blank"/> Open in new window
        </label>
    </li>
    <li class="tve_text tve_slider_config" data-value="300" data-min-value="200" data-property="max-width" data-max-value="available"
        data-input-selector="#lead_generation_width_input" data-callback="lead_generation">
        <label for="lead_generation_width_input" class="tve_left">&nbsp;Form size</label>

        <div class="tve_slider tve_left">
            <div class="tve_slider_element" id="tve_lead_generation_size_slider"></div>
        </div>
        <input class="tve_left" type="text" id="lead_generation_width_input" value="50px">

        <div class="clear"></div>
    </li>
</ul>