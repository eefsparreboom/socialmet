<?php

global $tve_icon_manager;
/**
 * add the Icon Manager admin menu
 */
require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Icon_Manager.php';
$tve_icon_manager = Thrive_Icon_Manager::instance();

$icon_url = plugins_url('thrive-visual-editor/editor/css/images/tcb-logo.png', 'thrive-visual-editor');
add_menu_page("Icon Manager", "Icon Manager", "edit_theme_options", "thrive_icon_manager", array($tve_icon_manager, 'mainPage'), $icon_url);

