<?php
$menu_path = dirname(__FILE__) . '/inc/menu/';
$side_menu_path = dirname(__FILE__) . '/inc/side-menu/';
$is_thrive_theme = !empty($_POST['is_thrive_theme']);
$template_uri = $_POST['template_uri'];
$editor_dir = $_POST['editor_dir'];
$landing_page_dir = dirname(dirname($editor_dir)) . '/landing-page/templates';
$_cPanelPosition = ($_POST['display_options']['position'] == 'left') ? "tve_cpanelFlip" : "";
$_dColor = ($_POST['display_options']['color'] == 'dark') ? "tve_is_dark" : "";

$thrive_optins = $thrive_optin_colors = $posts_categories = $custom_menus = array();
$current_theme_name = $_POST['current_theme_name'];
$banned_themes_names[] = 'Performag';

if ($is_thrive_theme) {
    $thrive_optins = !empty($_POST['thrive_optins']) ? $_POST['thrive_optins'] : array();
    $thrive_optin_colors = !empty($_POST['thrive_optin_colors']) ? $_POST['thrive_optin_colors'] : array();
    $posts_categories = !empty($_POST['posts_categories']) ? $_POST['posts_categories'] : array();
    $custom_menus = !empty($_POST['custom_menus']) ? $_POST['custom_menus'] : array();
}
$landing_page_template = empty($_POST['landing_page']) ? false : $_POST['landing_page'];
$fonts = empty($_POST['custom_fonts']) ? array() : $_POST['custom_fonts'];
$post_type = $_POST['post_type'];
?>
<div class="tve_wrapper <?php echo $_cPanelPosition . ' ' . $_dColor ?>" id="tve_cpanel">
    <div class="tve_editor">
        <div class="tve_cpanel_sec tve_control_btns">
            <div class="tve_btn_success tve_left" title="Save">
                <div class="tve_update" title="Save" id="tve_update_content">
                    <span class="tve_expanded">Save Changes</span>
                    <span class="tve_collapsed tve_icon tve_icon-52"></span>
                </div>
            </div>
            <div class="tve_btn_default tve_expanded tve_left" title="Publish">
                <a class="tve_preview" title="Publish" id="tve_preview_content" target="_blank"
                   href="<?php echo $_POST['preview_url']; ?>">
                    <span class="">Preview</span>
                </a>
            </div>
            <div class="tve_clear"></div>
        </div>

        <?php if ($post_type != 'tcb_lightbox' && $post_type == 'page') : ?>
            <?php include $side_menu_path . 'landing_pages.php' ?>
        <?php endif ?>

        <?php include $side_menu_path . 'page_actions.php' ?>

        <div class="tve_cpanel_options">

            <?php include $side_menu_path . 'simple_content_elements.php' ?>

            <?php include $side_menu_path . 'multi_style_elements.php' ?>

            <?php include $side_menu_path . 'advanced_elements.php' ?>

            <?php if ($is_thrive_theme || $landing_page_template): ?>
                <?php include $side_menu_path . 'thrive_theme_elements.php' ?>
            <?php endif; ?>

        </div>
        <?php include $side_menu_path . 'editor_settings.php' ?>
    </div>
</div>

<div class="tve_cpanel_onpage <?php echo $_dColor ?>" style="display: none" id="tve_cpanel_onpage">
    <div class="tve_secondLayer">
        <div id="text_menu">
            <?php include $menu_path . 'text.php' ?>
        </div>

        <div id="text_inline_only_menu">
            <?php include $menu_path . 'text_inline_only.php' ?>
        </div>

        <div id="img_menu">
            <?php include $menu_path . 'img.php' ?>
        </div>

        <div id="button_menu">
            <?php include $menu_path . 'button.php' ?>
        </div>

        <div id="contentbox_menu">
            <?php include $menu_path . 'contentbox.php' ?>
        </div>

        <div id="guarantee_menu">
            <?php include $menu_path . 'guarantee.php' ?>
        </div>

        <div id="calltoaction_menu">
            <?php include $menu_path . 'calltoaction.php' ?>
        </div>

        <div id="testimonial_menu">
            <?php include $menu_path . 'testimonial.php' ?>
        </div>

        <div id="bullets_menu">
            <?php include $menu_path . 'bullets.php' ?>
        </div>

        <div id="tabs_menu">
            <?php include $menu_path . 'tabs.php' ?>
        </div>

        <div id="toggle_menu">
            <?php include $menu_path . 'toggle.php' ?>
        </div>

        <div id="custom_html_menu">
            <?php include $menu_path . 'custom_html.php' ?>
        </div>

        <div id="feature_grid_menu">
            <?php include $menu_path . 'feature_grid.php' ?>
        </div>

        <div id="cc_icons_menu">
            <?php include $menu_path . 'cc_icons.php' ?>
        </div>

        <div id="pricing_table_menu">
            <?php include $menu_path . 'pricing_table.php' ?>
        </div>

        <div id="content_container_menu">
            <?php include $menu_path . 'content_container.php' ?>
        </div>

        <?php if ($is_thrive_theme || $landing_page_template): ?>
            <div id="page_section_menu">
                <?php include $menu_path . 'page_section.php' ?>
            </div>
        <?php endif; ?>

        <div id="table_menu">
            <?php include $menu_path . 'table.php' ?>
        </div>

        <div id="table_cell_menu">
            <?php include $menu_path . 'table_cell.php' ?>
        </div>

        <div id="thrive_optin_menu">
            <?php include $menu_path . 'thrive_optin.php' ?>
        </div>

        <div id="content_reveal_menu">
            <?php include $menu_path . 'content_reveal.php' ?>
        </div>

        <div id="tw_qs_menu">
            <?php include $menu_path . 'tw_qs.php' ?>
        </div>

        <div id="lead_generation_menu">
            <?php include $menu_path . 'lead_generation.php' ?>
        </div>

        <div id="post_grid_menu">
            <?php include $menu_path . 'post_grid.php' ?>
        </div>

        <div id="contents_table_menu">
            <?php include $menu_path . 'contents_table.php' ?>
        </div>

        <div id="responsive_video_menu">
            <?php include $menu_path . 'responsive_video.php' ?>
        </div>

        <div id="countdown_timer_evergreen_menu">
            <?php include $menu_path . 'countdown_timer_evergreen.php' ?>
        </div>

        <div id="countdown_timer_menu">
            <?php include $menu_path . 'countdown_timer.php' ?>
        </div>

        <div id="thrive_posts_list_menu">
            <?php include $menu_path . 'thrive_posts_list.php' ?>
        </div>

        <div id="thrive_custom_phone_menu">
            <?php include $menu_path . 'thrive_custom_phone.php' ?>
        </div>

        <div id="thrive_custommenu_menu">
            <?php include $menu_path . 'thrive_custommenu.php' ?>
        </div>

        <div id="rating_menu">
            <?php include $menu_path . 'rating.php' ?>
        </div>

        <div id="shortcode_menu">
            <?php include $menu_path . 'shortcode.php' ?>
        </div>

        <div id="lists_menu">
            <?php include $menu_path . 'lists.php' ?>
        </div>

        <div id="default_element_menu">
            <?php /* this will be shown as a default menu for everything that does not have a menu, and should contain general options */ ?>
            <?php include $menu_path . 'default_element.php' ?>
        </div>

        <?php if ($post_type == 'tcb_lightbox') : ?>
            <div id="lightbox_menu">
                <?php include $menu_path . 'lightbox.php' ?>
            </div>
        <?php endif ?>

        <?php if ($landing_page_template) : ?>
            <div id="landing_page_menu">
                <?php include $menu_path . 'landing_page.php' ?>
            </div>
            <div id="landing_page_content_menu">
                <?php include $menu_path . 'landing_page_content.php' ?>
            </div>
        <?php endif ?>

        <div id="icon_menu">
            <?php include $menu_path . 'icon.php' ?>
        </div>

        <div id="cb_text_menu">
            <?php $is_cb_text = true; include $menu_path . 'icon.php' ?>
        </div>

        <div class="tve_clear"></div>
    </div>
    <a href="javascript:void(0)" id="tve_submenu_close" title="Close"></a>
    <div class="tve_menu">
        <a href="javascript:void(0)" id="tve_submenu_save" class="tve_click tve_icon tve_icon-93 tve_lb_small tve_btn tve_no_hide" data-ctrl="controls.lb_open" data-lb="lb_save_user_template" title="Save this element as a Content Template">
            <input type="hidden" name="element" value="1" />
        </a>
    </div>
    <div id="tve_iris_holder" style="display: none">
        <span class="tve_cp_text tve_cp_title" id="tve_cp_title">Text Color</span>

        <div class="tve_cp_row"></div>

        <div class="tve_cp_row tve_clearfix">
            <span class="tve_cp_text">Color</span>
            <input type="text" size="10" id="tve_cp_color" class="tve_right" style="width: 120px"/>
        </div>
        <div class="tve_cp_row tve_clearfix wp-picker-opacity" id="tve_opacity_ctrl">
            <span class="tve_cp_text tve_left" style="">Opacity</span>
            <input type="text" size="2" id="tve_cp_opacity" class="tve_right" style="width: 36px" />
            <div class="ui-slider-bg tve_right" style="width: 150px;">
                <div class="wp-opacity-slider" id="tve_cp_opacity_slider"></div>
            </div>
        </div>
        <div class="tve_cp_row tve_cp_actions">
            <div id="tve_cp_save_fav" class="tve_btn_default tve_left">
                <div class="tve_preview">Save as Favourite Color</div>
            </div>
            <div class="tve_btn_success tve_right" id="tve_cp_ok">
                <div class="tve_update">OK</div>
            </div>
        </div>
    </div>
</div>

<!--lightbox stuff-->
<div class="tve_lightbox_overlay" id="tve_lightbox_overlay"></div>
<div class="tve_lightbox_frame" id="tve_lightbox_frame">
    <a class="tve-lightbox-close" href="javascript:void(0)" title="Close"><span class="tve_lightbox_close tve_click" data-ctrl="controls.lb_close"></span></a>

    <div class="tve_lightbox_content" id="tve_lightbox_content"></div>
    <div class="tve_lightbox_buttons" id="tve_lightbox_buttons">
        <input type="button" class="tve_save_lightbox tve_mousedown tve_btn_green" value="Save" data-ctrl="controls.lb_save">
    </div>
</div>

<div style="display: none" id="tve_table_merge_actions" class="tve_table_merge_cells_actions">
    <?php include $menu_path . 'table_cell_manager.php' ?>
</div>