<?php

require_once dirname(__FILE__) . '/helpers.php';
require_once dirname(__FILE__) . '/font-manager/font-manager.php';

if (!function_exists('thrive_page_template_add_menu_page')) {
    /* show the Thrive Content Builder main menu link in the admin panel */
    function thrive_page_template_add_menu_page()
    {
        $icon_url = plugins_url('thrive-visual-editor/editor/css/images/tcb-logo.png', 'thrive-visual-editor');
        add_menu_page("Font Manager", "Font Manager", "edit_theme_options", "thrive_font_manager", "thrive_font_manager", $icon_url);
    }
}

/**
 * load all css and js needed for the font manager page
 *
 * this is called only on font manager page
 */
function tcb_enqueue_admin()
{
    wp_enqueue_style('thickbox');

    wp_enqueue_script('thickbox');

    wp_enqueue_style('thrive-theme-options', plugins_url('thrive-visual-editor/admin/css/theme-options.css'));

    wp_enqueue_style('thrive-admin-colors', plugins_url('thrive-visual-editor/admin/css/thrive_admin_colours.css'));
    wp_enqueue_style('thrive-base-css', plugins_url('thrive-visual-editor/admin/css/pure-base-min.css'));
    wp_enqueue_style('thrive-pure-css', plugins_url('thrive-visual-editor/admin/css/pure-min.css'));

    wp_enqueue_script('wp-color-picker');
    wp_enqueue_style('wp-color-picker');
}
