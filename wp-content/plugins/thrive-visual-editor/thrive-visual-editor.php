<?php

/*
Plugin Name: Thrive Visual Editor
Plugin URI: http://www.thrivethemes.com
Version: 1.57
Author: <a href="http://www.thrivethemes.com">Thrive Themes</a>
Description: Live front end editor for your Wordpress content
*/

DEFINE("TVE_VERSION", 1.57);
DEFINE("TVE_DIR", plugins_url() . '/thrive-visual-editor');
DEFINE("TVE_EDITOR_JS", TVE_DIR . '/editor/js');
DEFINE("TVE_EDITOR_CSS", TVE_DIR . '/editor/css');
DEFINE("TVE_TEMPLATES_CSS", TVE_DIR . '/shortcodes/templates/css');
DEFINE("TVE_LANDING_PAGE_TEMPLATE", TVE_DIR . '/landing-page/templates');

/** plugin updates **/
require 'plugin-updates/plugin-update-checker.php';
$MyUpdateChecker = new PluginUpdateChecker(
    'http://members.thrivethemes.com/plugin_versions/content_builder/update.json',
    __FILE__,
    'thrive-visual-editor'
);

/* init the Event Manager */
require_once dirname(__FILE__) . '/event-manager/init.php';

/* include the admin menu settings page for the font manager */
if (is_admin()) {

    /* using the admin_menu hook to add links to the side admin menu */
    add_action('admin_menu', 'tcb_admin_init');

    /* on ajax calls, we should also load the functionality for the Font Manager */
    if (defined('DOING_AJAX') && DOING_AJAX) {
        add_action('admin_init', 'tcb_admin_init');
    }

    function tcb_admin_init()
    {
        if (!tve_check_if_thrive_theme()) {
            require_once plugin_dir_path(__FILE__) . '/admin/init.php';
            thrive_page_template_add_menu_page();
        }
        /* init the font manager admin stuff. TODO: this will be moved also to the themes */
        require_once plugin_dir_path(__FILE__) . '/admin/icon-manager/init.php';
    }
}

add_action('edit_form_after_title', 'tve_admin_button');

/* ajax calls through WP API */
add_action('wp_ajax_tve_save_post', 'tve_save_post');
add_action('wp_ajax_tve_editor_display_config', 'tve_editor_display_config');
add_action('wp_ajax_tve_change_style_family', 'tve_change_style_family');
add_action('wp_ajax_tve_save_user_template', 'tve_save_user_template');
add_action('wp_ajax_tve_load_user_template', 'tve_load_user_template');
add_action('wp_ajax_tve_remove_user_template', 'tve_remove_user_template');
add_action('wp_ajax_load_element_from_api', 'tve_load_element_from_api');
add_action("wp_ajax_tve_optin_render_fields", "tve_optin_render_fields");
add_action('wp_ajax_tve_landing_pages_load', 'tve_landing_pages_load');
add_action('wp_ajax_tve_widget_options', 'tve_widget_options');
add_action('wp_ajax_tve_display_widget', 'tve_display_widget');
add_action('wp_ajax_tve_widgets_list', 'tve_widgets_list');
add_action('wp_ajax_tve_do_post_grid_shortcode', 'tve_do_post_grid_shortcode');
/**
 * Post grid filters
 */
add_action('wp_ajax_tve_categories_list', 'tve_categories_list');
add_action('wp_ajax_tve_tags_list', 'tve_tags_list');
add_action('wp_ajax_tve_custom_taxonomies_list', 'tve_custom_taxonomies_list');
add_action('wp_ajax_tve_authors_list', 'tve_authors_list');
add_action('wp_ajax_tve_posts_list', 'tve_posts_list');

/* event manager - related ajax calls - they all have this one handler function, which will forward the call to a "front" controller */
add_action('wp_ajax_tve_event_manager', 'tve_event_manager_ajax');

/* ajax action to be executed in frontend area */
add_action('tcb_ajax_tve_render_shortcode', 'tve_render_shortcode');

add_action('wp_enqueue_scripts', 'tve_enqueue_editor_scripts');
add_action('wp_enqueue_scripts', 'tve_remove_conflicting_scripts', 100);
add_action('wp_footer', 'pre_save_filter_wrapper');
add_action('admin_menu', 'tve_add_settings_menu');

// add thrive edit link to admin bar
add_action('admin_bar_menu', 'thrive_editor_admin_bar', 100);
add_action('wp_enqueue_scripts', 'tve_frontend_enqueue_scripts');

// load script for edit page only
add_action('admin_enqueue_scripts', 'tve_edit_page_scripts');

// add TCB buttons to admin post/page listing screen
add_filter('page_row_actions', 'thrive_page_row_buttons', 10, 2);
add_filter('post_row_actions', 'thrive_page_row_buttons', 10, 2);

/* we need to always load this into the head section, because some themes styles will overwrite the font settings */
add_action('wp_head', 'tve_load_font_css');

// To fight against themes creating custom wpautop scripts and injecting rogue <br/> and <p> tags into content we have to apply shortcodes early, then add our content to the page
// at priority 101, hence the two separate "the_content" actions
if (is_editor_page_raw()) {
    add_filter('the_content', 'tve_editor_content', PHP_INT_MAX);
} else {
    add_filter('the_content', 'tve_editor_content');
}

// this is a fix for the "Pitch" theme that tries to use a backend function get_current_screen() in a media library filter that we run in the front end and therefore breaks the page.
add_action("after_setup_theme", "tve_turn_off_get_current_screen");


// add the same tve_editor_filter but on this case on Landing Page templates
add_filter('tve_landing_page_content', 'tve_editor_content');

// manipulate social sharing hooks so that they work with TCB
if (has_filter("dd_hook_wp_content")) {
    remove_filter('the_content', 'dd_hook_wp_content');
    add_filter('the_content', 'dd_hook_wp_content', 103);
}

// make sure WP editor page doesn't overwrite TCB content
add_filter('is_protected_meta', 'tve_hide_custom_fields', 10, 2);

// integration with YOAST SEO
add_filter('wpseo_pre_analysis_post_content', 'tve_yoast_seo_integration');

// add notification container
add_action('wp_footer', 'tve_add_notification_box');

// use settings API to store non post-level settings
add_action('init', 'tve_global_options_init');

/* hook to fix various conflicts that might appear. first one: YARPP */
add_action('init', 'tve_fix_plugin_conflicts', PHP_INT_MAX);

// add filter for including the TCB meta into the search functionality
add_filter('posts_clauses', 'tve_process_search_clauses', null, 2);

add_action('get_the_content_limit', 'tve_genesis_get_post_excerpt');

// hook for detecting if a post is setup as a landing page from the TCB
add_action('template_redirect', 'tcb_landing_page_content');

// add a custom body class if we are in editing mode
add_filter('body_class', 'tve_add_body_editor_class');

// automatically modify lightbox title if the title of the associated landing page is modified
add_action('save_post', 'tve_save_post_callback');

add_filter('widget_form_callback', "tve_widget_form_callback");

// global options
// all style sheet families listed below will be added to the editor.
global $tve_style_families, $tve_style_family_classes;

// append version to dynamically changed stylesheets, because browsers will cache them
$_version = get_bloginfo('version');
$tve_style_families = array(
    "Flat" => TVE_EDITOR_CSS . '/thrive_flat.css?ver=' . $_version,
    "Classy" => TVE_EDITOR_CSS . '/thrive_classy.css?ver=' . $_version,
    "Minimal" => TVE_EDITOR_CSS . '/thrive_minimal.css?ver=' . $_version
);

$tve_style_family_classes = array(
    "Flat" => 'tve_flt',
    "Classy" => 'tve_clsy',
    "Minimal" => 'tve_min'
);

// control panel locations so that developers can add elements to the control panel through API
global $tve_cpanel_locations;
$tve_cpanel_locations = array(
    "main_left",
    "main_right",
    "main_extended_left",
    "main_extended_right",
    "text_menu_left",
    "text_menu_right",
    "button_menu_left",
    "button_menu_right",
    "content_box_menu_left",
    "content_box_menu_right",
    "calltoaction_menu_left",
    "calltoaction_menu_right",
    "testimonial_menu_left",
    "testimonial_menu_right",
    "bullets_menu_left",
    "bullets_menu_right",
    "tabs_menu_left",
    "tabs_menu_right",
    "custom_html_menu_left",
    "custom_html_menu_right"
);

/* theme shortcodes available in TCB */
// list of shortcode identifier => callback function
/*
 * the callback function will be called with an array of attributes and must return a html code to be inserted into the DOM
 */
$tve_thrive_shortcodes = array(
    'optin' => 'tve_do_optin_shortcode',
    'posts_list' => 'tve_do_posts_list_shortcode',
    'custom_menu' => 'tve_do_custom_menu_shortcode',
    'custom_phone' => 'tve_do_custom_phone_shortcode',
    'widget' => 'tve_do_widget_shortcode',
    'post_grid' => 'tve_do_post_grid_shortcode'
);

// global variables for the API
global $tve_api_menu_items;
global $tve_api_dropdown_lists;
global $tve_api_includes;

foreach ($tve_cpanel_locations as $value) {
    $tve_api_menu_items[$value] = array();
    $tve_api_dropdown_lists[$value] = array();
}

// global variable to capture all API elements
global $tve_api_elements;
$tve_api_elements = array();

// set colour schemes for all shortcode templates.  The "tve_" prefix is added at a later stage, so no need to add these here.
global $tve_shortcode_colours;
$tve_shortcode_colours = array("yellow", "black", "blue", "green", "orange", "purple", "red", "teal", "white");

// register thrive visual editor global settings
function tve_global_options_init()
{
    /* register the "lightbox" post type */
    register_post_type('tcb_lightbox', array(
        'labels' => array(
            'name' => __('Thrive Lightboxes'),
            'singular_name' => __('Thrive Lightbox'),
            'add_new_item' => __('Add New Thrive Lightbox'),
            'edit_item' => __('Edit Thrive Lightbox')
        ),
        'public' => true,
        'has_archive' => false
    ));

    $plugin_db_version = get_option('tve_version');
    if (!$plugin_db_version || $plugin_db_version != TVE_VERSION) {
        tve_run_plugin_upgrade($plugin_db_version, TVE_VERSION);
    }
    update_option('tve_version', TVE_VERSION);
}

// add live editor button to edit screen
function tve_admin_button()
{
    $post_type = get_post_type();

    if (!tve_is_post_type_editable($post_type)) {
        return;
    }

    $url = tcb_get_editor_url(get_the_ID());
    echo '<br/><a class="button" href="' . $url . '" id="thrive_preview_button" target="_blank"><span class="thrive-adminbar-icon"></span>Edit with Thrive Content Builder</a><br/><br/>';
    ?>
    <style type="text/css">
        .thrive-adminbar-icon {
            background: url('<?php echo TVE_EDITOR_CSS; ?>/images/admin-bar-logo.png') no-repeat 0px 0px;
            padding-left: 25px !important;
            width: 20px !important;
            height: 20px !important;
        }
    </style>
    <?php
    if (tve_post_is_landing_page(get_the_ID())) {
        if (!empty($_GET['tve_revert_theme'])) {
            delete_post_meta(get_the_ID(), 'tve_landing_page');
        } else {
            ?>
            <div class="postbox" style="text-align: center;">
                <div class="inside">
                    You are currently using a Content Builder landing page to display this piece of content.<br>If you'd
                    like to revert back to your theme
                    template than click the button below:
                    <br><br> <a href="javascript:void(0)" onclick="location.href=location.href+'&tve_revert_theme=1'"
                                class="button">Revert to theme template</a>
                </div>
            </div>
        <?php
        }
    }
}

/**
 * Returns the url for the TCB editing screen.
 *
 * If no post id is set then will use native WP functions to get the editing URL for the piece of content that's currently being edited
 *
 * @param bool $post_id
 * @return string
 */
function tcb_get_editor_url($post_id = false)
{
    $post_id = ($post_id) ? $post_id : get_the_ID();
    $editor_link = set_url_scheme(get_permalink($post_id));
    $editor_link = esc_url(apply_filters('preview_post_link', add_query_arg(array('tve' => 'true'), $editor_link)));
    return $editor_link;
}

/**
 * Returns the preview URL for any given post/page
 *
 * If no post id is set then will use native WP functions to get the editing URL for the piece of content that's currently being edited
 *
 * @param bool $post_id
 * @return string
 */
function tcb_get_preview_url($post_id = false)
{
    $post_id = ($post_id) ? $post_id : get_the_ID();
    $preview_link = set_url_scheme(get_permalink($post_id));
    $preview_link = esc_url(apply_filters('preview_post_link', add_query_arg('preview', 'true', $preview_link)));
    return $preview_link;
}

// only enqueue scripts on our own editor pages
function tve_enqueue_editor_scripts()
{
    if (is_editor_page()) {

        if (get_option('tve_license_status')) {
            $post_id = get_the_ID();

            //set nonce for security
            $ajax_nonce = wp_create_nonce("tve-le-verify-sender-track129");

            global $tve_shortcode_colours;
            global $tve_style_families, $tve_style_family_classes;

            // thrive content builder javascript file (loaded both frontend and backend).
            tve_enqueue_script('tve_frontend', TVE_EDITOR_JS . '/thrive_content_builder_frontend.min.js', array('jquery', 'editor'), false, true);

            tve_enqueue_script("tve_drag", TVE_EDITOR_JS . '/util/drag.min.js', array('jquery'));
            tve_enqueue_script("tve_event_manager", TVE_EDITOR_JS . '/util/events.min.js', array('jquery'));
            tve_enqueue_script("tve_controls", TVE_EDITOR_JS . '/util/controls.min.js', array('jquery'));
            tve_enqueue_script("tve_colors", TVE_EDITOR_JS . '/util/colors.min.js', array('jquery'));

            /** control panel scripts and dependancies */
            tve_enqueue_script("tve_editor", TVE_EDITOR_JS . '/editor.min.js',
                array(
                    'jquery',
                    'tve_drag',
                    'tve_event_manager',
                    'tve_controls',
                    'tve_colors',
                    'jquery-ui-slider',
                    'jquery-ui-datepicker',
                    'iris',
                    'tve_clean_html',
                    'tve_undo_manager',
                    'tve-rangy-core',
                    'tve-rangy-css-module',
                    'tve-rangy-save-restore-module',
                ), false, true);

            // jQuery UI stuff
            // no need to append TVE_VERSION for these scripts
            wp_enqueue_script("jquery");
            wp_enqueue_script('jquery-serialize-object');
            wp_enqueue_script("jquery-ui-core", array('jquery'));
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script("jquery-ui-slider", array('jquery', 'jquery-ui-core'));
            tve_enqueue_script("jquery-ui-datepicker", array('jquery', 'jquery-ui-core'));

            wp_enqueue_script("jquery-masonry", array('jquery'));

            wp_enqueue_script("jquery-ui-datepicker", array('jquery', 'jquery-ui-core'));

            if (tve_check_if_thrive_theme()) {
                wp_enqueue_script('thrive-datetime-picker', get_template_directory_uri() . '/inc/js/jquery-ui-timepicker.js', array('jquery-ui-datepicker'));
            }
            wp_enqueue_script('iris', admin_url('js/iris.min.js'), array('jquery', 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);

            // WP colour picker - this is now needed only if a Thrive Theme is used - to allow colorpicker options on the WordPress Content element
            if (tve_check_if_thrive_theme()) {
                wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'), array('jquery', 'iris'), false, 1);
                wp_localize_script('wp-color-picker', 'wpColorPickerL10n', array(
                    'clear' => __('Clear'),
                    'defaultString' => __('Default'),
                    'pick' => __('Select Color'),
                    'current' => __('Current Color'),
                ));
                // WP colour picker
                wp_enqueue_style('wp-color-picker');
            }

            // helper scripts for various functions
            wp_enqueue_script("tve_clean_html", TVE_EDITOR_JS . '/jquery.htmlClean.min.js', array('jquery'), '1.0.0', true);
            wp_enqueue_script("tve_undo_manager", TVE_EDITOR_JS . '/tve_undo_manager.min.js', array('jquery'), '1.0.0', true);

            // rangy for selection
            wp_enqueue_script("tve-rangy-core", TVE_EDITOR_JS . '/rangy-core.js', array('jquery'));
            wp_enqueue_script("tve-rangy-css-module", TVE_EDITOR_JS . '/rangy-cssclassapplier.js', array('jquery', 'tve-rangy-core'));
            wp_enqueue_script("tve-rangy-save-restore-module", TVE_EDITOR_JS . '/rangy-selectionsaverestore.js', array('jquery', 'tve-rangy-core'));
            wp_enqueue_style('jquery-ui-datepicker', TVE_EDITOR_CSS . '/jquery-ui-1.10.4.custom.min.css');

            // now enqueue the styles
            tve_enqueue_style("tve_editor_style", TVE_EDITOR_CSS . '/editor.css');
            tve_enqueue_style("tve_default", TVE_EDITOR_CSS . '/thrive_default.css');
            tve_enqueue_style("tve_colors", TVE_EDITOR_CSS . '/thrive_colors.css');
            tve_enqueue_style('thrive_events', TVE_EDITOR_CSS . '/events.css');

            if (tve_check_if_thrive_theme()) {
                /* include the css needed for the shortcodes popup (users are able to insert Thrive themes shortcode inside the WP editor on frontend) - using the "Insert WP Shortcode" element */
                tve_enqueue_style('tve_shortcode_popups', TVE_EDITOR_CSS . '/thrive_shortcodes_popup.css');
            }

            if (is_rtl()) {
                tve_enqueue_style("tve_rtl", TVE_EDITOR_CSS . '/editor_rtl.css');
            }

            // load style family
            $loaded_style_family = tve_get_style_family($post_id);

            // load any custom css
            tve_load_custom_css();

            // scan templates directory and build array of template file names
            $shortcode_files = array_diff(scandir(dirname(__FILE__) . '/shortcodes/templates/', 1), array("..", ".", "css", "fonts", "images", "html", "js"));

            // get array of user templates
            $user_templates = get_option("tve_user_templates");

            $user_template_names = array();

            if (isset($user_templates) && is_array($user_templates)):
                $i = 0;
                foreach ($user_templates as $template_name => $template_content) {
                    array_push($user_template_names, $template_name);
                    $i++;
                }
            endif;


            // load API data into array sorted by menu location
            $tve_api_controls = tve_load_api_controls();

            // build html to be added to control panel
            $tve_api_html = tve_get_api_html($tve_api_controls);

            // get standing data array mapping id to template path
            $tve_map_api_paths = tve_get_api_paths($tve_api_controls);

            // pass any API path information
            $tve_api_includes = tve_get_api_includes();

            // list of credit cards for cc widget
            $tve_cc_icons = array("cc_amex, cc_discover, cc_mc, cc_paypal, cc_visa");

            // check if this is a thrive theme or not
            $tve_is_thrive_theme = tve_check_if_thrive_theme();

            $current_theme = wp_get_theme();

            if ($tve_is_thrive_theme && function_exists('_thrive_get_patterns_from_directory')) {
                $tve_page_section_patterns = _thrive_get_patterns_from_directory();
                array_shift($tve_page_section_patterns);
            }

            // get control panel display options and their defaults
            $tve_config_display_attributes = array(
                "position" => "right",
                "color" => "light"
            );

            // generate cp config array to send to front end
            $tve_cp_config = array();
            foreach ($tve_config_display_attributes as $attribute => $default_value) {
                $saved_setting = get_option("tve_config_" . $attribute, NULL);
                if ($saved_setting) {
                    $tve_cp_config[$attribute] = $saved_setting;
                } else {
                    $tve_cp_config[$attribute] = $default_value;
                }
            }

            // get mapping for custom colour controls
            $tve_colour_mapping = include dirname(__FILE__) . '/custom_colour_mappings.php';

            // variables for custom colours used for post
            $tve_remembered_colours = get_option("thrv_custom_colours", null);

            if (!$tve_remembered_colours) {
                $tve_remembered_colours = array();
            }
            $thrive_optins = $thrive_optin_colors = $categories = $custom_menus = array();
            if ($tve_is_thrive_theme) {
                $thrive_optins = tve_get_thrive_optins();
                $thrive_optin_colors = _thrive_get_color_scheme_options('optin');
                $categories = array(0 => "All categories");
                foreach (get_categories() as $cat) {
                    $categories[$cat->cat_ID] = $cat->cat_name;
                }

                $menu_items = get_terms('nav_menu', array('hide_empty' => false));
                $all_menus = array();
                foreach ($menu_items as $menu) {
                    $all_menus[$menu->term_id] = $menu->name;
                }
                $custom_menus = $all_menus;
            }

            $timezone_offset = get_option('gmt_offset');
            $sign = ($timezone_offset < 0 ? '-' : '+');
            $min = abs($timezone_offset) * 60;
            $hour = floor($min / 60);
            $tzd = $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($min % 60, 2, '0', STR_PAD_LEFT);

            // if the post is a TCB landing page, get the landing page configuration and send it to javascript
            $landing_page_config = array();
            if (($template = tve_post_is_landing_page(get_the_ID())) !== false) {
                $landing_page_config = tve_get_landing_page_config($template);
                if (!empty($landing_page_config['custom_color_mappings'])) {
                    $tve_colour_mapping = array_merge_recursive($tve_colour_mapping, $landing_page_config['custom_color_mappings']);
                    unset($landing_page_config['custom_color_mappings']); // clean it up, we don't want this in our js
                }
                /* if we have specific editor JS for the landing page, include that also */
                if (is_file(plugin_dir_path(__FILE__) . "/landing-page/js/editor_{$template}.min.js")) {
                    tve_enqueue_script('tve_landing_page_editor', plugin_dir_url(__FILE__) . "/landing-page/js/editor_{$template}.min.js", array('tve_editor'));
                }
            }

            // read in all the available landing page templates
            $landing_page_templates = tve_get_landing_page_templates();

            // custom fonts from Font Manager
            $all_fonts = tve_get_all_custom_fonts();

            // get the post fonts that will be loaded in the page, we should not load those twice
            $post_fonts = get_post_meta(get_the_ID(), 'thrive_post_fonts', true);
            $post_fonts = empty($post_fonts) ? array() : json_decode($post_fonts);

            $index = count($post_fonts);
            foreach ($all_fonts as $font) {
                $href = tve_custom_font_get_link($font);
                if (!in_array($href, $post_fonts)) {
                    wp_enqueue_style('thrive_custom_font_' . get_the_ID() . '_' . ($index++), $href);
                }
            }

            $tve_post_globals = tve_get_post_meta(get_the_ID(), 'tve_globals', true);
            if (empty($tve_post_globals) || (isset($tve_post_globals[0]) && empty($tve_post_globals[0]))) {
                $tve_post_globals = array('e' => 1);
            }

            /* landing page template - we need to allow the user to setup head and footer scripts */
            $tve_global_scripts = get_post_meta(get_the_ID(), 'tve_global_scripts', true);
            if (empty($template) || empty($tve_global_scripts)) {
                $tve_global_scripts = array('head' => '', 'footer' => '');
            }

            $post_type = get_post_type(get_the_ID());

            // pass variables needed to client side
            $tve_path_params = array(
                'cpanel_dir' => TVE_DIR . '/editor',
                'shortcodes_dir' => TVE_DIR . '/shortcodes/templates/',
                'editor_dir' => TVE_EDITOR_CSS,
                'template_uri' => rtrim(get_template_directory_uri(), '/'),
                'shortcodes_array' => $shortcode_files,
                'shortcode_colours' => $tve_shortcode_colours,
                'style_families' => $tve_style_families,
                'style_classes' => $tve_style_family_classes,
                'loaded_style' => $loaded_style_family,
                'post_id' => get_the_ID(),
                'preview_url' => tcb_get_preview_url(get_the_ID()),
                'tve_ajax_nonce' => $ajax_nonce,
                'tve_version' => TVE_VERSION,
                'user_templates' => $user_template_names,
                'tve_api_data' => $tve_api_html,
                'tve_api_paths' => $tve_map_api_paths,
                'tve_api_includes' => $tve_api_includes,
                'tve_cc_icons' => $tve_cc_icons,
                'is_thrive_theme' => $tve_is_thrive_theme,
                'current_theme_name' => $current_theme->get('Name'),
                'page_section_patterns' => isset($tve_page_section_patterns) ? $tve_page_section_patterns : array(),
                'cpanel_display_options' => $tve_cp_config,
                'tve_colour_mapping' => $tve_colour_mapping,
                'tve_loaded_stylesheet' => $loaded_style_family,
                'tve_colour_picker_colours' => $tve_remembered_colours,
                'front_ajax_url' => plugin_dir_url(__FILE__) . 'editor/ajax.php',
                'ajax_url' => admin_url('admin-ajax.php'),
                'font_settings_url' => admin_url('admin.php?page=thrive_font_manager'),
                'thrive_optins' => $thrive_optins,
                'thrive_optin_colors' => $thrive_optin_colors,
                'wp_timezone' => $tzd,
                'wp_timezone_offset' => $timezone_offset,
                'posts_categories' => $categories,
                'custom_menus' => $custom_menus,
                'is_rtl' => (int)is_rtl(),
                'landing_page' => $template,
                'landing_page_config' => $landing_page_config,
                'landing_pages' => $landing_page_templates,
                'custom_fonts' => $all_fonts,
                'post_type' => $post_type,
                'tve_globals' => $tve_post_globals,
                'landing_page_lightbox' => $post_type === 'tcb_lightbox' ? get_post_meta(get_the_ID(), 'tve_lp_lightbox', true) : '',
                'tve_global_scripts' => $tve_global_scripts,
                'extra_body_class' => version_compare(get_bloginfo('version'), '4.0', '>=') ? 'tve_mce_fixed' : ''
            );
            $tve_path_params['extra_body_class'] .= ($tve_cp_config['position'] == 'left' ? ' tve_cpanelFlip' : '');

            wp_localize_script('tve_editor', 'tve_path_params', $tve_path_params);

            /* some params will be needed also for the frontend script */
            $frontend_options = array(
                'is_editor_page' => true,
            );
            wp_localize_script('tve_frontend', 'tve_frontend_options', $frontend_options);

            // enqueue scripts for tapping into media thickbox
            wp_enqueue_media();

        } else {
            tve_license_notice();
        }
    }

// now print scripts for preview logo in admin bar. will write directly to page because only a small snippet and thus will load faster than another external css file load.
    if (is_admin_bar_showing() && !isset($_GET['tve']) && (is_single() || is_page())): ?>
        <style type="text/css">
            .thrive-adminbar-icon {
                background: url('<?php echo TVE_EDITOR_CSS; ?>/images/admin-bar-logo.png') no-repeat 0px 0px;
                padding-left: 25px !important;
                width: 20px !important;
                height: 20px !important;
            }
        </style>
    <?php endif;

}

// only load scripts on post / page edit pages
function tve_edit_page_scripts($hook_suffix)
{
    if ('post.php' == $hook_suffix || 'post-new.php' == $hook_suffix) {
        wp_enqueue_style("wp-pointer");
        wp_enqueue_script("wp-pointer");
        tve_enqueue_script('tve_post_ready', TVE_EDITOR_JS . '/tve_admin_post_ready.min.js', array('autosave', 'wp-pointer'));
    }
}

// adds a div element to the page used to manipulate editor html before writing to database
function pre_save_filter_wrapper()
{
    if (isset($_GET["tve"]) && (current_user_can('manage_options') || current_user_can('edit_posts'))) {
        echo '<div id="pre_save_filter_wrapper" style="display:none"></div>';
    }
}

// Ajax listener to save the post in database.  Handles "Save" and "Update" buttons together.
// If either button pressed, then write to saved field.
// If publish button pressed, then write to both save and published fields
function tve_save_post()
{
    check_ajax_referer("tve-le-verify-sender-track129", "security");
    if (isset($_POST['post_id']) && current_user_can('edit_post', $_POST['post_id'])) {
        ob_clean();

        $landing_page_template = empty($_POST['tve_landing_page']) ? 0 : $_POST['tve_landing_page'];

        if (!empty($_POST['custom_action'])) {
            switch ($_POST['custom_action']) {
                case 'landing_page': //change or remove the landing page template for this post
                    tve_change_landing_page_template($_POST['post_id'], $landing_page_template);
                    break;
                case 'landing_page_reset':
                    /* clear the contents of the current landing page */
                    if (!($landing_page_template = tve_post_is_landing_page($_POST['post_id']))) {
                        break;
                    }
                    tve_landing_page_reset($_POST['post_id'], $landing_page_template);
                    break;
                case 'landing_page_delete':
                    $template_index = intval(str_replace('user-saved-template-', '', $landing_page_template));
                    $contents = get_option('tve_saved_landing_pages_content');
                    $meta = get_option('tve_saved_landing_pages_meta');

                    unset($contents[$template_index], $meta[$template_index]);
                    /* array_values - reorganize indexes */
                    update_option('tve_saved_landing_pages_content', array_values($contents));
                    update_option('tve_saved_landing_pages_meta', array_values($meta));

                    tve_landing_pages_load();
                    break;
            }
            exit();
        }

        $key = $landing_page_template ? ('_' . $landing_page_template) : '';
        $content_split = tve_get_extended($_POST['tve_content_more']);
        update_post_meta($_POST['post_id'], "tve_content_before_more{$key}", $content_split['main']);
        update_post_meta($_POST['post_id'], "tve_content_more_found{$key}", $content_split['more_found']);
        update_post_meta($_POST['post_id'], "tve_save_post{$key}", $_POST['tve_content']);
        update_post_meta($_POST['post_id'], "tve_custom_css{$key}", $_POST['inline_rules']);
        /* user defined Custom CSS rules here, had to use different key because tve_custom_css was already used */
        update_post_meta($_POST['post_id'], "tve_user_custom_css{$key}", $_POST['tve_custom_css']);
        update_option("thrv_custom_colours", isset($_POST['custom_colours']) ? $_POST['custom_colours'] : array());

        if ($_POST['update'] == "true") {
            update_post_meta($_POST['post_id'], "tve_updated_post{$key}", $_POST['tve_content']);
        }
        /* global options for a post that are not included in the editor */
        update_post_meta($_POST['post_id'], "tve_globals{$key}", empty($_POST['tve_globals']) ? array() : array_filter($_POST['tve_globals']));
        /* custom fonts used for this post */
        tve_update_post_custom_fonts($_POST['post_id'], empty($_POST['custom_font_classes']) ? array() : $_POST['custom_font_classes']);
        if ($landing_page_template) {
            update_post_meta($_POST['post_id'], 'tve_landing_page', $_POST['tve_landing_page']);
            /* global Scripts for landing pages */
            update_post_meta($_POST['post_id'], 'tve_global_scripts', !empty($_POST['tve_global_scripts']) ? $_POST['tve_global_scripts'] : array());
            if (!empty($_POST['tve_landing_page_save'])) {

                /* save the contents of the current landing page for later use */
                $template_content = array(
                    'before_more' => $content_split['main'],
                    'more_found' => $content_split['more_found'],
                    'content' => $_POST['tve_content'],
                    'inline_css' => $_POST['inline_rules'],
                    'custom_css' => $_POST['tve_custom_css'],
                    'tve_globals' => empty($_POST['tve_globals']) ? array() : array_filter($_POST['tve_globals']),
                    'tve_global_scripts' => empty($_POST['tve_global_scripts']) ? array() : $_POST['tve_global_scripts']
                );
                $template_meta = array(
                    'name' => $_POST['tve_landing_page_save'],
                    'template' => $landing_page_template,
                    'date' => date('Y-m-d')
                );
                if (empty($template_content['more_found'])) { // save some space
                    unset($template_content['before_more']); // this is the same as the tve_save_post field
                    unset($template_content['more_found']);
                }
                $templates_content = get_option('tve_saved_landing_pages_content'); // this should get unserialized automatically
                $templates_meta = get_option('tve_saved_landing_pages_meta'); // this should get unserialized automatically
                if (empty($templates_content)) {
                    $templates_content = array();
                    $templates_meta = array();
                }
                $templates_content [] = $template_content;
                $templates_meta [] = $template_meta;

                // make sure these are not autoloaded, as it is a potentially huge array
                add_option('tve_saved_landing_pages_content', null, '', 'no');

                update_option('tve_saved_landing_pages_content', $templates_content);
                update_option('tve_saved_landing_pages_meta', $templates_meta);
            }
        } else {
            delete_post_meta($_POST['post_id'], 'tve_landing_page');
        }
        tve_update_post_meta($_POST['post_id'], 'thrive_icon_pack', empty($_POST['has_icons']) ? 0 : 1);
        tve_update_post_meta($_POST['post_id'], 'tve_has_masonry', empty($_POST['tve_has_masonry']) ? 0 : 1);
        die;
    } else {
        echo "You don't have administrator permissions to make a save!";
        die;
    }
}

/**
 *  axaj listener - saves control panel display configuration when user updates in front end.  Options are saved globally, rather than at post level
 */
function tve_editor_display_config()
{
    $attribute = $_POST['attribute'];
    $value = $_POST['value'];
    check_ajax_referer("tve-le-verify-sender-track129", "security");
    if (current_user_can('manage_options') || current_user_can('edit_posts')) {
        update_option('tve_config_' . $attribute, $value);
        return 1;
    }
}

// add the editor content to $content, but at priority 101 so not affected by custom theme shortcode functions that are common with some theme developers
function tve_editor_content($content)
{
    global $post, $is_thrive_theme;
    if (post_password_required($post)) {
        return $content;
    }

    if (isset($GLOBALS['TVE_CONTENT_SKIP_ONCE'])) {
        unset($GLOBALS['TVE_CONTENT_SKIP_ONCE']);
        return $content;
    }

    /* this will hold the html for the tinymce editor instantiation, only if we're on the editor page */
    $tinymce_editor = $page_loader = '';
    $post_id = get_the_ID();
    
    if (is_editor_page()) {

        // this is an editor page
        $tve_saved_content = stripslashes(tve_get_post_meta(get_the_ID(), "tve_save_post", true));

        /*
         * this was completely destroying the page when using wordpress SEO plugin
         * it called get_the_excerpt early in the head section, which in turn called apply_filters('the_content')
         * the wp_editor function should only be called once per request
         */
        if (in_the_loop() || tve_post_is_landing_page(get_the_ID())) {
            add_action('wp_footer', 'tve_output_wysiwyg_editor');
        }

        $page_loader = '<div id="tve_page_loader" style="display: none" class="tve_page_loader"><div class="tve_loader_inner">
                <img src="' . TVE_EDITOR_CSS . '/images/loader.gif" alt=""/></div></div>';

    } else {
        // this is the frontend - check to see if on blog and find tve-more excerpt if available
        if (!tve_check_in_loop($post_id)) {
            tve_load_custom_css($post_id);
        }
        if (!is_singular()) {
            $more_found = tve_get_post_meta(get_the_ID(), "tve_content_more_found", true);
            $content_before_more = tve_get_post_meta(get_the_ID(), "tve_content_before_more", true);
            if (!empty($content_before_more) && $more_found) {
                $more_link = apply_filters('the_content_more_link', '<a href="' . get_permalink() . '#more-' . $post->ID . '" class="more-link">Continue Reading</a>', 'Continue Reading');
                $tve_saved_content = stripslashes($content_before_more) . $more_link;
                $content = ''; /* clear out anything else after this point */
            }
        }
        if (!isset($tve_saved_content)) {
            $tve_saved_content = tve_restore_script_tags(stripslashes(tve_get_post_meta(get_the_ID(), "tve_updated_post", true)));
        }
        if (empty($tve_saved_content)) {
            // return empty content if nothing is inserted in the editor - this is to make sure that first page section on the page will actually be displayed ok
            return $content;
        }
        /* load up custom fonts */
        foreach (tve_get_post_custom_fonts($post_id) as $key => $href) {
            wp_enqueue_style('thrive_tcb_custom_font' . $post_id . '_' . $key, $href);
        }

        /* prepare Events configuration */
        if (in_the_loop() || tve_post_is_landing_page(get_the_ID())) {
            // append lightbox HTML to the end of the body
            tve_parse_events($tve_saved_content);
        }
    }

    $tve_saved_content = tve_thrive_shortcodes($tve_saved_content, is_editor_page());

    /* render the content added through WP Editor (element: "WordPress Content") */
    $tve_saved_content = tve_do_wp_shortcodes($tve_saved_content, is_editor_page());

    $style_family_class = tve_get_style_family_class($post_id);

    $style_family_id = is_singular() ? ' id="' . $style_family_class . '" ' : ' ';

    $wrap = array(
        'start' => '<div' . $style_family_id . 'class="' . $style_family_class . '"><div id="tve_editor" class="tve_shortcode_editor">',
        'end' => '</div></div>'
    );

    if (is_editor_page() && get_post_type($post_id) == 'tcb_lightbox') {
        $wrap['start'] .= '<div class="tve_p_lb_control tve_editable tve_editor_main_content tve_content_save tve_empty_dropzone">';
        $wrap['end'] .= '</div>';
    }

    if (tve_get_post_meta($post_id, 'thrive_icon_pack')) {
        tve_enqueue_icon_pack();
    }

    return $wrap['start'] . $tve_saved_content . $wrap['end'] . $content . $tinymce_editor . $page_loader;
}

// determine whether the user is on the editor page or not
function is_editor_page()
{
    if (!is_singular()) {
        return false;
    }
    
    if (isset($_GET["tve"]) && (current_user_can('edit_post', get_the_ID()))) {
        return true;
    } else {
        return false;
    }
}

// determine whether the user is on the editor page or not
/**
 * modification: WP 4 removed the "preview" parameter
 * @return bool
 */
function is_editor_page_raw()
{
    if (isset($_GET["tve"])) {
        return true;
    } else {
        return false;
    }
}

// load front end scripts
function tve_frontend_enqueue_scripts()
{
    if(tve_get_post_meta(get_the_ID(), 'tve_has_masonry')) {
        wp_enqueue_script("jquery-masonry", array('jquery'));
    }
    wp_enqueue_style("tve_default", TVE_EDITOR_CSS . '/thrive_default.css');
    wp_enqueue_style("tve_colors", TVE_EDITOR_CSS . '/thrive_colors.css');
    tve_enqueue_style_family();

    tve_enqueue_script("tve_frontend", TVE_EDITOR_JS . '/thrive_content_builder_frontend.min.js', array('jquery'), false, true);
    wp_enqueue_script("jquery_cookie", TVE_EDITOR_JS . '/jquery.cookie.min.js', array('jquery'), false, true); // no version needed here
    /* params for the frontend script */
    $frontend_options = array(
        'is_editor_page' => true,
        'is_single' => (string)((int)is_singular())
    );
    // hide tve more tag from front end display
    if (!is_editor_page()) {
        tve_load_custom_css();
        tve_hide_more_tag();
        $frontend_options['is_editor_page'] = false;
    }
    wp_localize_script('tve_frontend', 'tve_frontend_options', $frontend_options);

}

// adds an icon and link to the admin bar for quick access to the editor. Only shows when not already in thrive content builder
function thrive_editor_admin_bar($wp_admin_bar)
{
    if (is_admin_bar_showing() && !isset($_GET['tve']) && (is_single() || is_page()) && tve_is_post_type_editable(get_post_type())) {

        $editor_link = tcb_get_editor_url(get_the_ID());
        $args = array(
            'id' => 'tve_button',
            'title' => '<span class="thrive-adminbar-icon"></span>Edit with Thrive Content Builder',
            'href' => $editor_link,
            'meta' => array(
                'class' => 'thrive-admin-bar'
            )
        );
        $wp_admin_bar->add_node($args);
    }
}

/**
 * enqueue the associated style family for a post / page
 *
 * this also gets called in archive (list) pages, there we need to load style families for each post from the list
 *
 * @param null $post_id optional this will only come filled in when calling it from a lightbox
 */
function tve_enqueue_style_family($post_id = null)
{
    global $tve_style_families, $tve_style_family_classes, $wp_query;

    if (null === $post_id) {
        $posts_to_load = $wp_query->posts;
        $post_id = array();
        foreach ($posts_to_load as $post) {
            $post_id []= $post->ID;
        }
    } else {
        $post_id = array($post_id);
    }

    foreach ($post_id as $p_id) {
        $current_post_style = tve_get_style_family($p_id);

        $style_key = 'tve_style_family_' . strtolower($tve_style_family_classes[$current_post_style]);
        if (!wp_style_is($style_key)) {
            tve_enqueue_style($style_key, $tve_style_families[$current_post_style]);
        }
    }
}

/**
 * retrieve the style family used for a specific post / page
 *
 * @param $post_id
 * @param string $default
 */
function tve_get_style_family($post_id, $default = 'Flat')
{
    global $tve_style_families;
    $current_post_style = get_post_meta($post_id, "tve_style_family", true);

    // Flat is default style family if nothing set
    $current_post_style = empty($current_post_style) || !isset($tve_style_families[$current_post_style]) ? $default : $current_post_style;

    return $current_post_style;
}

function tve_get_style_family_class($post_id)
{
    global $tve_style_family_classes;
    $style_family = get_post_meta($post_id, 'tve_style_family', true);

    return !empty($style_family) && isset($tve_style_family_classes[$style_family]) ? $tve_style_family_classes[$style_family] : $tve_style_family_classes['Flat'];
}

// ajax function for updating post meta with the current style family
function tve_change_style_family()
{
    check_ajax_referer("tve-le-verify-sender-track129", "security");
    if (current_user_can('manage_options') || current_user_can('edit_posts')) {
        if (ob_get_contents()) {
            ob_clean();
        }
        // style family should remain the same when switching over to landing page and back
        update_post_meta($_POST['post_id'], "tve_style_family", $_POST['style_family']);
        die;
    }
}

// notice to be displayed if license not validated - going to load the styles inline because there are so few lines and not worth an extra server hit.
function tve_license_notice()
{
    ?>
    <div id="tve_license_notice">
        <img src="<?php echo TVE_EDITOR_CSS; ?>/images/Logo-Large.png">

        <p>You need to
            <a href="<?php echo admin_url(); ?>options-general.php?page=tve_license_validation">activate your
                license</a> before you can use the editor!
        </p></div>
    <style type="text/css">
        #tve_license_notice {
            width: 500px;
            margin: 0 auto;
            text-align: center;
            top: 50%;
            left: 50%;
            margin-top: -100px;
            margin-left: -250px;
            padding: 50px;
            z-index: 3000;
            position: fixed;
            -moz-border-radius-bottomleft: 10px;
            -webkit-border-bottom-left-radius: 10px;
            border-bottom-left-radius: 10px;
            -moz-border-radius-bottomright: 10px;
            -webkit-border-bottom-right-radius: 10px;
            border-bottom-right-radius: 10px;
            border-bottom: 1px solid #bdbdbd;
            background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgi…3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyYWQpIiAvPjwvc3ZnPiA=');
            background-size: 100%;
            background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(20%, #ffffff), color-stop(100%, #e6e6e6));
            background-image: -webkit-linear-gradient(top, #ffffff 20%, #e6e6e6 100%);
            background-image: -moz-linear-gradient(top, #ffffff 20%, #e6e6e6 100%);
            background-image: -o-linear-gradient(top, #ffffff 20%, #e6e6e6 100%);
            background-image: linear-gradient(top, #ffffff 20%, #e6e6e6 100%);
            -moz-border-radius: 10px;
            -webkit-border-radius: 10px;
            border-radius: 10px;
            -webkit-box-shadow: 2px 5px 3px #efefef;
            -moz-box-shadow: 2px 5px 3px #efefef;
            box-shadow: 2px 5px 3px #efefef;
        }
    </style>
<?php
}

function tve_license_validation()
{
    include('tve_settings.php');
}


function tve_add_settings_menu()
{
    add_options_page('Thrive Content Builder', 'Thrive Content Builder', 'manage_options', 'tve_license_validation', 'tve_license_validation');
}

function tve_add_notification_box()
{
    ?>
    <div id='tve_notification_box'></div>
<?php
}

function tve_save_user_template()
{
    check_ajax_referer("tve-le-verify-sender-track129", "security");

    if (current_user_can('manage_options') || current_user_can('edit_posts')) {
        $new_template = $_POST;
        $existing_templates = get_option("tve_user_templates");
        $existing_template_styles = get_option("tve_user_templates_styles", array());

        // check if template name exists.  If it does, return error. If not save to database and return new list of template names
        if (($existing_templates && is_array($existing_templates) && array_key_exists($new_template['template_name'], $existing_templates))) {
            $response = array(
                "success" => 0,
                "error" => "That template name already exists, please use another name"
            );
        } else {
            $existing_templates[$new_template["template_name"]] = $new_template['template_content'];
            update_option("tve_user_templates", $existing_templates);

            $existing_template_styles[$new_template["template_name"]] = $new_template['custom_css'];
            update_option("tve_user_templates_styles", $existing_template_styles);

            $response = array(
                "success" => 1,
                "template_names" => get_option("tve_user_templates"),
                "new_template_added" => stripslashes($new_template["template_name"])
            );
        }
        if (ob_get_contents()) {
            ob_clean();
        }
        $response = json_encode($response);
        echo $response;
    }
    die;
}

function tve_load_user_template()
{
    check_ajax_referer("tve-le-verify-sender-track129", "security");
    if (current_user_can('manage_options') || current_user_can('edit_posts')) {
        $templates = get_option("tve_user_templates");
        $css = get_option("tve_user_templates_styles");
        $css[$_POST['template_name']] .= '';

        $response = array(
            "html_code" => stripslashes($templates[$_POST['template_name']]),
            "css_code" => stripslashes($css[$_POST['template_name']])
        );
        if (ob_get_contents()) {
            ob_clean();
        }
        $response = json_encode($response);
        echo $response;
        die;
    }
}

function tve_remove_user_template()
{
    check_ajax_referer("tve-le-verify-sender-track129", "security");
    if (current_user_can('manage_options') || current_user_can('edit_posts')) {
        $templates = get_option("tve_user_templates");
        foreach ($templates as $key => $value) {
            stripslashes($value);
        }
        unset($templates[$_POST['template_name']]);
        update_option("tve_user_templates", $templates);
        die;
    }
}

// integration with Wordpress SEO for page analysis.
function tve_yoast_seo_integration($content)
{
    $tve_saved_content = tve_get_post_meta(get_the_ID(), "tve_updated_post", true);
    $content = $tve_saved_content . $content;
    return $content;
}

// load controls and dropdowns from API
function tve_load_api_controls()
{
    global $tve_api_menu_items;
    global $tve_api_dropdown_lists;
    $tve_api['menu_items'] = apply_filters("tcb_menu_items", $tve_api_menu_items);
    $tve_api['dropdown_lists'] = apply_filters("tcb_dropdown_lists", $tve_api_dropdown_lists);
    $tve_api_sorted = tve_organise_api_data($tve_api);
    return $tve_api;
}

// Sort each menu location by priority
function tve_organise_api_data($tve_api)
{
    global $tve_cpanel_locations;
    foreach ($tve_cpanel_locations as $menu_location) {
        usort($tve_api['menu_items'][$menu_location], "tve_cmp");
        usort($tve_api['dropdown_lists'][$menu_location], "tve_cmp");
    }
    return $tve_api;
}

// simple sorting function for multi-dimensional arrays
function tve_cmp($a, $b)
{
    return $a["menu_priority"] - $b["menu_priority"];
}

// function to take API array data and convert into HTML
// returns multidimensional array for each menu location
function tve_get_api_html($tve_api_controls)
{
    global $tve_cpanel_locations;

    // loop through the API data for each menu position and generate the HTML from the data.
    foreach ($tve_cpanel_locations as $menu_position) {
        $tve_api_html['dropdown_lists'][$menu_position] = null;
        $tve_api_html['menu_items'][$menu_position] = null;

        // handle dropdown lists
        foreach ($tve_api_controls['dropdown_lists'][$menu_position] as $key => $value) {
            $tve_api_html['dropdown_lists'][$menu_position] .= tve_generate_dropdown_html($value);
        }
        // handle menu items
        foreach ($tve_api_controls['menu_items'][$menu_position] as $key => $value) {
            $tve_api_html['menu_items'][$menu_position] .= tve_generate_menu_item_html($value);;
        }
    }
    return $tve_api_html;
}

// user heredoc to generate html for API dropdown menus
function tve_generate_dropdown_html($value)
{
    //generate icon for menu bar icon (top level of the drop down)
    $tve_generated_html = <<<EOF
    <li class="tve_btn" title="{$value['menu_tooltip_text']}">
    <span class="tve_api_icon tve_awesome tve_left tve_sub_btn tve_sub_btn_caret {$value['custom_class']}">{$value['menu_icon']} </span>
    <div class="tve_sub_btn" title="{$value['menu_tooltip_text']}">
    <div class="tve_sub">
            <ul>
EOF;

    // now generate all the sub_menu items
    foreach ($value['sub_menu_items'] as $sub_menu_key => $sub_menu_value) {
        // generate html for all submenu items using heredoc
        $tve_generated_html .= <<<EOF
        <li class="cp_draggable tve_api_icon tve_api_id_{$sub_menu_value['id']} {$sub_menu_value['custom_class']}">
        {$sub_menu_value['menu_title']}
        </li>
EOF;
    }

    // now append the matching close tags
    $tve_generated_html .= <<<EOF
    </ul>
    </div>
    </div>
    </li>
EOF;
    return $tve_generated_html;
}

// Generate the HTML for the API menu items (these are single icons that don't have a dropdown attached)
function tve_generate_menu_item_html($value)
{
    $tve_generated_html = <<<EOF
<li class="tve_btn" title="{$value['menu_tooltip_text']}">
    <div class="tve_awesome tve_api_icon cp_draggable tve_api_id_{$value['id']} {$value['custom_class']} ui-draggable" title="{$value['menu_tooltip_text']}">{$value['menu_title']}</div>
</li>
EOF;

    return $tve_generated_html;
}

// searches through API data to build standing data table (as an array) of API element id and their respective template paths.
// this is passed through to the front end as an array and used to load the elements
function tve_get_api_paths($tve_api)
{
    $api_template_paths = array();

    // handle dropdown schema
    foreach ($tve_api['dropdown_lists'] as $dropdown_location_id => $dropdown_location_value) {
        foreach ($dropdown_location_value as $dropdown_list_id => $dropdown_contents) {
            foreach ($dropdown_contents['sub_menu_items'] as $sub_menu_item_id => $sub_menu_item_value) {
                $api_template_paths[$sub_menu_item_value['id']] = $sub_menu_item_value['html_on_drop'];
            }
        }
    }

    // handle menu item schema
    foreach ($tve_api['menu_items'] as $menu_items_location_id => $menu_item_location) {
        foreach ($menu_item_location as $menu_item_id => $menu_item_contents) {
            $api_template_paths[$menu_item_contents['id']] = $menu_item_contents['html_on_drop'];
        }
    }

    // return the standing data table array
    return $api_template_paths;
}

function tve_get_api_includes()
{
    global $tve_api_includes;
    $tve_api['includes'] = apply_filters("tcb_api_custom_data", $tve_api_includes);
    return $tve_api['includes'];
}

/**
 * some features in the editor can only be displayed if we have knowledge about the theme and thus should only display on a thrive theme (borderless content for instance)
 * this function checks the global variable that's set in all thrive themes to check if the user is using a thrive theme or not
 **/
function tve_check_if_thrive_theme()
{
    global $is_thrive_theme;
    if (isset($is_thrive_theme) && $is_thrive_theme == true) {
        return true;
    } else {
        return false;
    }
}

/**
 * Hides thrive editor custom fields from being modified in the standard WP post / page edit screen
 * @param $protected
 * @param $meta_key
 * @return bool
 */
function tve_hide_custom_fields($protected, $meta_key)
{
    $keys = array(
        'tve_save_post', 'tve_updated_post', 'tve_content_before_more_shortcoded', 'tve_content_before_more',
        'tve_style_family', 'tve_updated_post_shortcoded', 'tve_user_custom_css', 'tve_custom_css',
        'tve_content_more_found', 'tve_landing_page', 'thrive_post_fonts', 'thrive_tcb_post_fonts', 'tve_globals', 'tve_special_lightbox',
        'thrive_icon_pack', 'tve_global_scripts', 'tve_has_masonry'
    );
    $landing_page_templates = array_keys(include plugin_dir_path(__FILE__) . '/landing-page/templates/_config.php');

    foreach ($keys as $key) {
        if ($key == $meta_key || strpos($meta_key, $key) === 0) {
            return true;
        }
        foreach ($landing_page_templates as $suffix) {
            if ($key . '_' . $suffix == $meta_key) {
                return true;
            }
        }
    }

    return $protected;
}

/**
 * This is a replica of the WP function get_extended
 * The returned array has 'main', 'extended', and 'more_text' keys. Main has the text before
 * the <code><!--tvemore--></code>. The 'extended' key has the content after the
 * <code><!--tvemore--></code> comment. The 'more_text' key has the custom "Read More" text.
 * @param string $post Post content.
 * @return array Post before ('main'), after ('extended'), and custom readmore ('more_text').
 */
function tve_get_extended($post)
{
    //Match the new style more links
    if (preg_match('/<!--tvemore(.*?)?-->/', $post, $matches)) {
        list($main, $extended) = explode($matches[0], $post, 2);
        $more_text = $matches[1];
        $more_found = true;
    } else {
        $main = $post;
        $extended = '';
        $more_text = '';
        $more_found = false;
    }

    // ` leading and trailing whitespace
    $main = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $main);
    $extended = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $extended);
    $more_text = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $more_text);

    return array('main' => $main, 'extended' => $extended, 'more_text' => $more_text, 'more_found' => $more_found);
}

/**
 * Adds inline script to hide more tag from the front end display
 */
function tve_hide_more_tag()
{
    ?>
    <style type="text/css">
        .tve_more_tag {
            display: none !important;
        }
    </style>
<?php
}

/**
 * Loads user defined custom css in the header to override style family css
 * If called with $post_id != null, it will load the custom css and user custom css from inside the loop (in case of homepage consisting of other pages, for example)
 */
function tve_load_custom_css($post_id = null)
{
    if (!is_null($post_id)) {
        $custom_css = trim(tve_get_post_meta($post_id, "tve_custom_css", true) . tve_get_post_meta($post_id, 'tve_user_custom_css', true));
        if ($custom_css) {
            echo sprintf(
                '<style type="text/css" class="tve_custom_style">%s</style>',
                $custom_css
            );
        }
        return;
    }
    global $wp_query;
    $posts_to_load = $wp_query->posts;

    global $css_loaded_post_id;
    $css_loaded_post_id = array();

    /* user-defined css from the Custom CSS content element */
    $user_custom_css = '';
    if ($posts_to_load) {
        ?>
        <style type="text/css" class="tve_custom_style">
        <?php
        foreach($posts_to_load as $post) {
            $inline_styles = tve_get_post_meta($post->ID, "tve_custom_css", true);
            echo $inline_styles;
            $user_custom_css .= tve_get_post_meta($post->ID, 'tve_user_custom_css', true);
            array_push($css_loaded_post_id, $post->ID);
        }
        ?> </style><?php
        /* also check for user-defined custom CSS inserted via the "Custom CSS" content editor element */
        echo $user_custom_css ? sprintf('<style type="text/css" id="tve_head_custom_css" class="tve_user_custom_style">%s</style>', $user_custom_css) : '';
    }
}

/**
 * Sometimes the only way to make the plugin work with other scripts is by deregistering them on the editor page
 */
function tve_remove_conflicting_scripts()
{
    if (is_editor_page()) {
        /**  Genesis framework - Media Child theme contains a script that prevents users from being able to close the media library */
        wp_dequeue_script('yt-embed');
        wp_deregister_script('yt-embed');

        /** Member player loads jquery tools which conflicts with jQuery UI */
        wp_dequeue_script('mpjquerytools');
        wp_deregister_script('mpjquerytools');

    }
}

/**
 * checks to see if content being loaded is actually being loaded from within the loop (correctly) or being pulled
 * incorrectly to make up another page (for instance, a homepage that pulls different sections from pieces of content)
 */
function tve_check_in_loop($post_id)
{
    global $css_loaded_post_id;
    if (!empty($css_loaded_post_id) && in_array($post_id, $css_loaded_post_id)) {
        return true;
    }
    return false;
}

/**
 * restore all script tags from custom html controls. script tags are replaced with <code class="tve_js_placeholder">
 *
 * @param string $content
 * @return string having all <code class="tve_js_placeholder">..</code> replaced with their script tag equivalent
 */
function tve_restore_script_tags($content)
{
    $shortcode_js_pattern = '/\[tcb-script(.*?)\](.*?)\[\/tcb-script\]/s';

    return preg_replace($shortcode_js_pattern, '<script$1>$2</script>', $content);
}

/**
 *
 * checks whether the $post_type is editable using the TCB
 *
 * @param string $post_type
 *
 * @return bool true if the post type is editable
 */
function tve_is_post_type_editable($post_type)
{
    /* post types that are not editable using the content builder - handled as a blacklist */
    $blacklist_post_types = array(
        'focus_area',
        'thrive_optin'
    );

    $blacklist_post_types = apply_filters('tcb_post_types', $blacklist_post_types);

    if (in_array($post_type, $blacklist_post_types)) {
        return false;
    }

    return true;
}

/**
 * include the TCB saved meta into query search fields
 *
 * wordpress actually allows inserting post META fields in the search query,
 * but it will always build the clauses with AND (between post content and post meta) e.g.:
 *  WHERE (posts.title LIKE '%xx%' OR posts.post_content) AND (postsmeta.meta_key = 'tve_save_post' AND postsmeta.meta_value LIKE '%xx%')
 *
 * - we cannot use this, so we hook into the final pieces of the built SQL query - we need a solution like this:
 *  WHERE ( (posts.title LIKE '%xx%' OR posts.post_content OR (postsmeta.meta_key = 'tve_save_post' AND postsmeta.meta_value LIKE '%xx%') )
 *
 * @param array $pieces
 * @param WP_Query $wpQuery
 */
function tve_process_search_clauses($pieces, $wpQuery)
{
    if (is_admin() || empty($pieces) || !$wpQuery->is_search()) {
        return $pieces;
    }
    /** @var wpdb $wpdb */
    global $wpdb;

    $query = '';
    $n = !empty($q['exact']) ? '' : '%';
    $q = $wpQuery->query_vars;
    if (!empty($q['search_terms'])) {
        foreach ($q['search_terms'] as $term) {
            if (method_exists($wpdb, 'esc_like')) { // WP4
                $term = $wpdb->esc_like($term);
            } else {
                $term = like_escape($term); // like escape is deprecated in WP4
            }

            $like = $n . $term . $n;
            $query .= "((tve_pm.meta_key = 'tve_save_post')";
            $query .= $wpdb->prepare(" AND (tve_pm.meta_value LIKE %s)) OR ", $like);
        }
    }

    if (!empty($query)) {
        // add to where clause
        $pieces['where'] = str_replace("((({$wpdb->posts}.post_title LIKE '{$n}", "( {$query} (({$wpdb->posts}.post_title LIKE '{$n}", $pieces['where']);

        $pieces['join'] = $pieces['join'] . " LEFT JOIN {$wpdb->postmeta} AS tve_pm ON ({$wpdb->posts}.ID = tve_pm.post_id)";

        if (empty($pieces['groupby'])) {
            $pieces['groupby'] = "{$wpdb->posts}.ID";
        }
    }

    return ($pieces);
}

/**
 * get a list of all published Thrive Opt-Ins post types
 *
 * @return array pairs id => title
 */
function tve_get_thrive_optins()
{
    $optins = array();

    $args = array(
        'posts_per_page' => null,
        'numberposts' => null,
        'post_type' => 'thrive_optin'
    );

    foreach (get_posts($args) as $post) {
        $optins[$post->ID] = $post->post_title;
    }

    return $optins;
}

/**
 * parse content for configuration that belongs to theme-equivalent shortcodes, e.g. Opt-in shortcode
 *
 * for each key from $tve_thrive_shortcodes, it will search the content string for __CONFIG_{$key}__(.+)__CONFIG_{$key}__
 * if elements are found, the related callback will be called with the contents from between the two flags (this is a json_encoded string)
 *
 * shortcode configuration is held in JSON-encoded format inside a hidden div
 * these contents will get deleted if we're currently NOT in editor mode
 *
 * @param string $content
 * @param bool $keepConfig
 */
function tve_thrive_shortcodes($content, $keepConfig = false)
{
    global $tve_thrive_shortcodes;

    $shortcode_pattern = '#>__CONFIG_%s__(.+?)__CONFIG_%s__</div>#';

    foreach ($tve_thrive_shortcodes as $shortcode => $callback) {

        if (!tve_check_if_thrive_theme() && $shortcode !== 'widget' && $shortcode !== 'post_grid') {
            continue;
        }

        if (!function_exists($callback)) {
            continue;
        }

        /*
         * match all instances of the current shortcode
         */
        if (preg_match_all(sprintf($shortcode_pattern, $shortcode, $shortcode), $content, $matches, PREG_OFFSET_CAPTURE) !== false) {
            /* as we go over the $content and replace each shortcode, we must take into account the differences of replacement length and the length of the part getting replaced */
            $position_delta = 0;
            foreach ($matches[1] as $i => $data) {
                $m = $data[0]; // the actual matched regexp group
                $position = $matches[0][$i][1] + $position_delta; //the index at which the whole group starts in the string, at the current match
                $wholeGroup = $matches[0][$i][0];
                if (!($_params = @json_decode($m, true))) {
                    $_params = array();
                }
                $replacement = call_user_func($callback, $_params);
                $replacement = ($keepConfig ? ">__CONFIG_{$shortcode}__{$m}__CONFIG_{$shortcode}__</div>" : "></div>") . $replacement;

                $content = substr_replace($content, $replacement, $position, strlen($wholeGroup));
                /* increment the positioning offsets for the string with the difference between replacement and original string length */
                $position_delta += strlen($replacement) - strlen($wholeGroup);

            }
        }
    }

    return $content;
}

/**
 * Render post grid shortcode
 * Called from shortcode parser and when user drags element into page
 */
function tve_do_post_grid_shortcode($config)
{
    if (empty($config)) {
        //user drags new element
        check_ajax_referer("tve-le-verify-sender-track129", "security");
    }

    require_once dirname(__FILE__) . '/editor/inc/helpers/post_grid.php';

    $post_grid_helper = new PostGridHelper(empty($config) ? $_POST : $config);

    if (empty($config)) {
        //user drags new element
        echo $post_grid_helper->render();
        die;
    }

    $post_grid_helper->wrap = false;
    $html = $post_grid_helper->render();

    return $html;
}

/**
 * handle the Opt-In shortcode from the themes
 *
 * at this point this just forwards the call to the theme's Opt-In shortcode
 *
 * TODO: perhaps we can use the call to thrive_shortcode_optin (and other shortcodes) directly (?)
 *
 * @param array $attrs
 */
function tve_do_optin_shortcode($attrs)
{
    return '<div class="thrive-shortcode-html">' . thrive_shortcode_optin($attrs, '') . '</div>';
}

/**
 * handle the posts lists shortcode from the themes.  Full docs in function tve_do_optin_shortcode comments
 *
 * @param $atts
 * @return string
 */
function tve_do_posts_list_shortcode($atts)
{
    return '<div class="thrive-shortcode-html">' . thrive_shortcode_posts_list($atts, '') . '</div>';
}

/**
 * handle the custom menu shortcode
 *
 * @param $atts
 * @return string
 */
function tve_do_custom_menu_shortcode($atts)
{
    return '<div class="thrive-shortcode-html">' . thrive_shortcode_custom_menu($atts, '') . '</div>';
}


/**
 * handle the custom phone shortcode
 *
 * @param $atts
 * @return string
 */
function tve_do_custom_phone_shortcode($atts)
{
    return '<div class="thrive-shortcode-html">' . thrive_shortcode_custom_phone($atts, '') . '</div>';
}

/**
 * Adds TCB editing URL to underneath the post title in the Wordpress post listings view
 *
 * @param $actions
 * @param $page_object
 * @return mixed
 */
function thrive_page_row_buttons($actions, $page_object)
{
    // don't add url to blacklisted content types
    if (!tve_is_post_type_editable($page_object->post_type))
        return $actions;

    ?>
    <style type="text/css">
        .thrive-adminbar-icon {
            background: url('<?php echo TVE_EDITOR_CSS; ?>/images/admin-bar-logo.png') no-repeat 0px 0px;
            padding-left: 25px !important;
            width: 20px !important;
            height: 20px !important;
        }
    </style>
    <?php

    $url = tcb_get_editor_url($page_object->ID);
    $actions['tcb'] = "<span class='thrive-adminbar-icon'></span><a href='" . $url . "'>Edit with Thrive Content Builder</a>";
    return $actions;
}

/**
 * Handler for "get_the_content_limit" action applied by genesis themes
 *
 * Called on pages with posts list
 * If posts was created with TCB the more_element link is searched. If it is found the content before it is returned.
 * If more_element is not found the post's content added from admin is appended with TCB content then truncation is applied
 *
 * @param $content Truncated content post by genesis
 * @return string $content
 */
function tve_genesis_get_post_excerpt($content)
{
    global $post;
    $post_id = get_the_ID();

    if (!tve_check_in_loop($post_id)) {
        tve_load_custom_css($post_id);
    }

    if (!is_singular()) {
        $more_found = tve_get_post_meta(get_the_ID(), "tve_content_more_found", true);
        $content_before_more = tve_get_post_meta(get_the_ID(), "tve_content_before_more", true);
        if (!empty($content_before_more) && $more_found) {
            $more_link = apply_filters('the_content_more_link', '<a href="' . get_permalink() . '#more-' . $post->ID . '" class="more-link">Continue Reading</a>', 'Continue Reading');
            $content = "<div id='tve_editor' class='tve_shortcode_editor'>" .
                stripslashes($content_before_more) .
                $more_link .
                "</div>";
            return tve_restore_script_tags($content);
        }

        $maxLimit = genesis_get_option('content_archive_limit');
        $readMore = __('[Read more...]', 'genesis');

        $content = tve_restore_script_tags(stripslashes(tve_get_post_meta(get_the_ID(), "tve_updated_post", true))) . get_the_content();

        /**
         * inherited from genesis logic
         */
        $content = strip_tags(strip_shortcodes($content), apply_filters('get_the_content_limit_allowedtags', '<script>,<style>'));
        $content = trim(preg_replace('#<(s(cript|tyle)).*?</\1>#si', '', $content));
        $content = genesis_truncate_phrase($content, $maxLimit);
        $link = apply_filters('get_the_content_more_link', sprintf('&#x02026; <a href="%s" class="more-link">%s</a>', get_permalink(), $readMore), $readMore);
        $content = sprintf('<p>%s %s</p>', $content, $link);
    }

    return $content;
}

/*
 * Just a copy if it from focusblog theme and prefix it with "tve"
 * Parse the autoresponder code and generate a response array
 * @param string $code The autoresponder code
 * @param array $options Parse options
 * @return array Response array with the form fields and attributes *
 */

function tve_thrive_parse_autoresponder_code($code, $options = array()) {
    $response = array(
        'form_action' => "",
        'hidden_inputs' => "",
        'text_inputs' => array(),
        'form_method' => 'POST',
        'parse_status' => 0
    );

    if ($code == "") {
        return $response;
    }

    $is_mailchimp = false;
    if (strpos(strtolower($code), "mailchimp") !== false) {
        $is_mailchimp = true;
    }

    $DOM = new DOMDocument;

    try {

        $loadDom = @$DOM->loadHTML($code);
        if (!$loadDom) {
            return $response;
        }

        $form_elements = @$DOM->getElementsByTagName('form');

        if ($form_elements->length > 0) {
            $response['form_action'] = $form_elements->item(0)->getAttribute('action');
            $response['method'] = $form_elements->item(0)->getAttribute('method');
        }

        $input_elements = @$DOM->getElementsByTagName('input');

        for ($i = 0; $i < $input_elements->length; $i++) {
            $element = $input_elements->item($i);
            $element_type = $element->getAttribute('type');
            if ($element_type == "hidden") {
                $element_name = str_replace(" ", "_tsp_", $element->getAttribute('name'));
                //$element_value = str_replace(" ", "_tsp_", $element->getAttribute('value'));
                $element_value = $element->getAttribute('value');
                $temp_hidden_input = "<input type='hidden' name='" . $element_name . "' value='" . $element_value . "' />";
                $response['hidden_inputs'] .= $temp_hidden_input;
            }
            if ($element_type == "text" || $element_type == "email") {
                $element_name = $element->getAttribute('name');
                $element_name = str_replace("[", "_tbl_", $element_name);
                $element_name = str_replace("]", "_tbr_", $element_name);
                $element_name = str_replace("(", "_tbl2_", $element_name);
                $element_name = str_replace(")", "_tbr2_", $element_name);
                $element_name = str_replace(" ", "_tsp_", $element_name);
                //hot fix for mailchimp
                if ($is_mailchimp && strlen($element_name) > 30) {
                    $element_value = str_replace(" ", "", $element->getAttribute('value'));
                    $temp_hidden_input = "<input type='hidden' name='" . $element_name . "' value='" . $element_value . "' />";
                    $response['hidden_inputs'] .= $temp_hidden_input;
                } else {
                    array_push($response['text_inputs'], $element_name);
                }
            }
        }
        $response['parse_status'] = 1;
    } catch (Exception $e) {
        $response['parse_status'] = 0;
    }

    return $response;
}

function tve_thrive_get_optin_name_attr_fixed($attr) {
    $attr = str_replace("_tbl_", "[", $attr);
    $attr = str_replace("_tbr_", "]", $attr);
    $attr = str_replace("_tbl2_", "(", $attr);
    $attr = str_replace("_tbr2_", ")", $attr);
    $attr = str_replace("_tsp_", " ", $attr);
    return $attr;
}

/**
 * Old Geo's parser function
 * @param $code
 * @param array $options
 * @return array
 */
function tve_parse_autoresponder_code($code, $options = array())
{
    $response = array(
        'form_action' => "",
        'form_method' => 'POST',
        'hidden_inputs' => "",
        'text_inputs' => array(),
        'parse_status' => 0,
    );

    if ($code == "") {
        return $response;
    }

    $is_mailchimp = false;
    if (strpos(strtolower($code), "mailchimp") !== false) {
        $is_mailchimp = true;
    }

    $DOM = new DOMDocument;

    try {

        $loadDom = @$DOM->loadHTML($code);
        if (!$loadDom) {
            return $response;
        }

        $form_elements = @$DOM->getElementsByTagName('form');

        if ($form_elements->length > 0) {
            $response['form_action'] = $form_elements->item(0)->getAttribute('action');
            $response['form_method'] = (string)$form_elements->item(0)->getAttribute('method');
        }
        if (empty($response['form_method'])) {
            $response['form_method'] = 'POST';
        }

        $input_elements = @$DOM->getElementsByTagName('input');

        for ($i = 0; $i < $input_elements->length; $i++) {
            $element = $input_elements->item($i);
            $element_type = $element->getAttribute('type');
            $current = $element;
            $visible = true;
            while ($current->parentNode) {
                $style = str_replace(' ', '', $current->getAttribute('style'));
                if ($style && strpos($style, 'display:none') !== false) {
                    $visible = false;
                    break;
                }
                $current = $current->parentNode;
            }
            if ($element_type == "hidden" || !$visible) {
                $element_name = str_replace(" ", "", $element->getAttribute('name'));
                $element_value = str_replace(" ", "", $element->getAttribute('value'));
                $temp_hidden_input = "<input type='hidden' name='" . $element_name . "' value='" . $element_value . "' />";
                $response['hidden_inputs'] .= $temp_hidden_input;
            } elseif ($element_type == "text" || $element_type == "email") {
                $element_name = $element->getAttribute('name');
                $element_name = str_replace("[", "_tbl_", $element_name);
                $element_name = str_replace("]", "_tbr_", $element_name);
                $element_name = str_replace("(", "_tbl_", $element_name);
                $element_name = str_replace(")", "_tbr_", $element_name);
                //hot fix for mailchimp
                if ($is_mailchimp && strlen($element_name) > 30) {
                    $element_value = str_replace(" ", "", $element->getAttribute('value'));
                    $temp_hidden_input = "<input type='hidden' name='" . $element_name . "' value='" . $element_value . "' />";
                    $response['hidden_inputs'] .= $temp_hidden_input;
                } else {
                    array_push($response['text_inputs'], $element_name);
                }
            }
        }
        $response['parse_status'] = 1;
    } catch (Exception $e) {
        $response['parse_status'] = 0;
    }

    return $response;
}

function tve_optin_render_fields()
{

    if (!wp_verify_nonce($_REQUEST['nonce'], "tve-le-verify-sender-track129")) {
        echo 0;
        die;
    }
    if (!isset($_POST['id_post']) || !isset($_POST['autoresponder_code'])) {
        echo 0;
        die;
    }
    $current_optin = get_post($_POST['id_post']);
    if (!$current_optin) {
        echo 0;
        die;
    }

    $autoresponder_code = stripslashes($_POST['autoresponder_code']);
    $parsed_responder_code = tve_thrive_parse_autoresponder_code($autoresponder_code);

    if ($parsed_responder_code['parse_status'] == 0 || !is_array($parsed_responder_code['text_inputs']) || count($parsed_responder_code['text_inputs']) == 0) {
        echo 0;
        die;
    }
    ob_start();
    ?>
    <table class="widefat">
        <thead>
        <tr>
            <td>
                <?php _e("Field Number"); ?>
            </td>
            <td>
                <?php _e("Field Properties"); ?>
            </td>
            <td>
                <?php _e("Field Label / Description"); ?>
            </td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($parsed_responder_code['text_inputs'] as $key => $field):
            $field = str_replace(" ", "_", $field);
            ?>
            <tr>
                <td><?php echo $key + 1; ?></td>
                <td>
                    <?php echo tve_thrive_get_optin_name_attr_fixed($field) ?>
                </td>
                <td>
                    <input type="text" value="" class='thrive_txt_field_label' id='txt_label_<?php echo $field; ?>'/>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <button id="thrive_btn_save_autoresponder_fields" class="tve_btn_green">Save</button>
    <?php
    $html = ob_get_contents();
    ob_clean();
    echo json_encode(array(
        'html' => $html,
        'fields' => $parsed_responder_code
    ));
    die;
}

/**
 * process and display wp editor contents
 * used in "Insert Shortcode" element
 */
function tve_render_shortcode()
{
    check_ajax_referer("tve-le-verify-sender-track129", "security");
    if (empty($_POST['content'])) {
        exit('');
    }

    echo tcb_render_wp_shortcode(stripslashes($_POST['content']));
    exit();
}

/**
 * mimics all wordpress called functions when rendering a shortcode
 *
 * @param $content
 */
function tcb_render_wp_shortcode($content)
{
    $content = wptexturize(stripslashes($content));
    $content = convert_smilies($content);
    $content = convert_chars($content);
    $content = wpautop($content);
    $content = shortcode_unautop($content);

    return do_shortcode(shortcode_unautop(wptexturize($content)));
}

/**
 * render any shortcodes that might be included in the post meta-data using the Insert Shortcode element
 * raw shortcode texts are saved between 2 flags: ___TVE_SHORTCODE_RAW__ AND __TVE_SHORTCODE_RAW___
 *
 * @param string $content
 * @param bool $is_editor_page
 */
function tve_do_wp_shortcodes($content, $is_editor_page = false)
{
    list($start, $end) = array(
        '___TVE_SHORTCODE_RAW__',
        '__TVE_SHORTCODE_RAW___'
    );

    if (strpos($content, $start) === false) {
        return $content;
    }
    if (!preg_match_all("/{$start}((<p>)?(.+?)(<\/p>)?){$end}/s", $content, $matches, PREG_OFFSET_CAPTURE)) {
        return $content;
    }

    $position_delta = 0;
    foreach ($matches[1] as $i => $data) {
        $raw_shortcode = $data[0]; // the actual matched regexp group
        $position = $matches[0][$i][1] + $position_delta; //the index at which the whole group starts in the string, at the current match
        $wholeGroup = $matches[0][$i][0];

        $replacement = tcb_render_wp_shortcode($raw_shortcode);
        $replacement = ($is_editor_page ? $wholeGroup : '') . ('</div><div class="tve_shortcode_rendered">' . $replacement);

        $content = substr_replace($content, $replacement, $position, strlen($wholeGroup));
        /* increment the positioning offsets for the string with the difference between replacement and original string length */
        $position_delta += strlen($replacement) - strlen($wholeGroup);
    }

    return $content;
}

/**
 * if the current post is a landing page created with TCB, forward the control over to the landing page layout.php file
 *
 * if the current post is a Thrive Lightbox, display it on a page that will mimic it's behaviour (semi-transparent background, close button etc)
 *
 * @return bool
 */
function tcb_landing_page_content()
{
    // don't apply template redirects unless single post / page is being displayed.
    if (!is_singular()) {
        return false;
    }

    $post_id = get_the_ID();
    $post_type = get_post_type($post_id);
    if ($post_type != 'tcb_lightbox' && !($lp_template = tve_post_is_landing_page(get_the_ID()))) {
        return false;
    }

    $landing_page_dir = plugin_dir_path(__FILE__) . '/landing-page';

    if ($post_type == 'tcb_lightbox') {
        is_editor_page() && tve_enqueue_style('tve_lightbox_post', TVE_EDITOR_CSS . '/editor_lightbox.css');

        /**
         * Fix added by Paul McCarthy 16th October 2014 - added to solve THesis Child themes not loading CSS in Thrive lightboxes
         * Thesis v 2.1.9 loads style sheets for their child themes with this:- add_filter('template_include', array($this, '_skin'));
         * The filter isn't applied when the content builder lightbox is loaded because of our template_redirect filter
         * This function checks if the theme is Thesis, if so it checks for the existance of the css.css file that all Thesis child themes should have
         * If the file is found, it enqueuest the stylesheet in both editor and front end mode.
         */
        if (tve_is_thesis()) {
            if (defined('THESIS_USER_SKIN_URL') && file_exists(THESIS_USER_SKIN . '/css.css')) {
                wp_enqueue_style('tve_thesis_css', THESIS_USER_SKIN_URL . '/css.css');
            }
        }

        tve_enqueue_style('thrive_events', TVE_EDITOR_CSS . '/events.css');

        if ($for_landing_page = get_post_meta($post_id, 'tve_lp_lightbox', true)) {
            if (is_file($landing_page_dir . '/templates/css/' . $for_landing_page . '_lightbox.css')) {
                /* load up the lightbox style for this landing page */
                tve_enqueue_style('thrive_landing_page_lightbox', TVE_LANDING_PAGE_TEMPLATE . '/css/' . $for_landing_page . '_lightbox.css');
            }
        }

        include plugin_dir_path(__FILE__) . '/lightbox/layout-edit.php';
        exit();
    }

    if (empty($lp_template) || !is_file($landing_page_dir . '/templates/' . $lp_template . ".php")) {
        $lp_template = 'blank';
    }

    /* instantiate the $tcb_landing_page object - this is used throughout the layout.php for the landing page */
    require_once plugin_dir_path(__FILE__) . '/landing-page/inc/TCB_Landing_Page.php';
    $tcb_landing_page = new TCB_Landing_Page($post_id, $lp_template);

    /* base CSS file for all Page Templates */
    if (!tve_check_if_thrive_theme()) {
        tve_enqueue_style('tve_landing_page_base_css', TVE_LANDING_PAGE_TEMPLATE . '/css/base.css', 99);
    }
    // enqueue the css file for the template
    tve_enqueue_style('tve_landing_page_' . $lp_template, TVE_LANDING_PAGE_TEMPLATE . '/css/' . $lp_template . '.css', 100);

    /* give the control over to the landing page template */
    include $landing_page_dir . '/layout.php';
    exit();
}

/**
 * check if post having id $id is a landing page created with TCB
 *
 * @param $id
 */
function tve_post_is_landing_page($id)
{
    $is_landing_page = get_post_meta($id, 'tve_landing_page', true);

    if (!$is_landing_page) {
        return false;
    }

    return $is_landing_page; // this is the actual landing page template
}

/**
 * get post meta key. Also takes into account whether or not this post is a landing page
 * each regular meta key from the editor has the associated meta key for the landing page constructed by appending a "_{template_name}" after the key
 *
 * @param int $post_id
 * @param string $meta_key
 */
function tve_get_post_meta($post_id, $meta_key, $single = true)
{
    if (($template = tve_post_is_landing_page($post_id)) !== false) {
        $meta_key = $meta_key . '_' . $template;
    }

    return get_post_meta($post_id, $meta_key, $single);
}

/**
 * update a post meta key. Also takes into account whether or not this post is a landing page
 * each regular meta key from the editor has the associated meta key for the landing page constructed by appending a "_{template_name}" after the key
 *
 * @param $post_id
 * @param $meta_key
 * @param $value
 */
function tve_update_post_meta($post_id, $meta_key, $meta_value)
{
    if (($template = tve_post_is_landing_page($post_id)) !== false) {
        $meta_key = $meta_key . '_' . $template;
    }

    return update_post_meta($post_id, $meta_key, $meta_value);
}

/**
 * loads the landing pages configuration file and returns the item in that array corresponding to the template passed in as parameter
 *
 * @param $template_name
 */
function tve_get_landing_page_config($template_name)
{
    $config = include plugin_dir_path(__FILE__) . '/landing-page/templates/_config.php';

    return isset($config[$template_name]) ? $config[$template_name] : array();
}

/**
 * returns the default template content for a landing page post
 *
 * @param string $template_name
 * @param string $default possibility to use a template as default
 */
function tve_landing_page_default_content($template_name, $default = 'blank')
{
    $landing_page_dir = plugin_dir_path(__FILE__) . '/landing-page';

    if (empty($template_name) || !is_file($landing_page_dir . '/templates/' . $template_name . ".php")) {
        $template_name = $default;
    }

    ob_start();
    include $landing_page_dir . '/templates/' . $template_name . '.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

/**
 * get all the available landing page templates
 * this function reads in the landing page config file and returns an array with names, thumbnail images, and template codes
 *
 */
function tve_get_landing_page_templates()
{
    $templates = array();
    $config = include plugin_dir_path(__FILE__) . '/landing-page/templates/_config.php';
    foreach ($config as $code => $template) {
        $templates[$code] = array(
            'name' => $template['name'],
            'tags' => isset($template['tags']) ? $template['tags'] : array()
        );
    }

    return $templates;
}

/**
 * remove or change the current landing page template for the post with a default landing page, or a previously saved landing page
 * this also updates the post meta fields related to the selected template
 *
 * if it's a default template, then it will not change anything related to post content, as it will try to load it from the saved template
 *
 * each template will have it's own fields saved for the post, this helps users to not lose any content when switching back and forth various templates
 *
 * @param int $post_id
 * @param $landing_page_template
 */
function tve_change_landing_page_template($post_id, $landing_page_template)
{
    if (!$landing_page_template) {
        delete_post_meta($post_id, 'tve_landing_page');
        return;
    }
    /* if it's a user-saved template, the incoming param will start with 'user-saved-template-' */
    if (strpos($landing_page_template, 'user-saved-template-') !== 0) {
        /* default landing page template: load in the default template content */
        update_post_meta($post_id, 'tve_landing_page', $landing_page_template);
        /* 2014-09-19: reset the landing page contents, the whole page will reload using the clear new template */
        tve_landing_page_reset($post_id, $landing_page_template, false);
        $skip_update = true;
    } else {
        /* at this point, the template is one of the previously saved templates (saved by the user) - it holds the index from the tve_saved_landing_pages_content which needs to be loaded */
        $contents = get_option('tve_saved_landing_pages_content');
        $meta = get_option('tve_saved_landing_pages_meta');
        $template_index = intval(str_replace('user-saved-template-', '', $landing_page_template));

        /* make sure we don't mess anything up */
        if (empty($contents) || empty($meta) || !isset($contents[$template_index])) {
            return;
        }
        $content = $contents[$template_index];
        $landing_page_template = $meta[$template_index]['template'];
    }

    if (empty($content['more_found'])) {
        $content['more_found'] = false;
        $content['before_more'] = $content['content'];
    }

    $key = '_' . $landing_page_template;

    if (!isset($skip_update)) {
        update_post_meta($post_id, "tve_content_before_more{$key}", $content['before_more']);
        update_post_meta($post_id, "tve_content_more_found{$key}", $content['more_found']);
        update_post_meta($post_id, "tve_save_post{$key}", $content['content']);
        update_post_meta($post_id, "tve_custom_css{$key}", $content['inline_css']);
        /* user defined Custom CSS rules here, had to use different key because tve_custom_css was already used */
        update_post_meta($post_id, "tve_user_custom_css{$key}", $content['custom_css']);
        update_post_meta($post_id, "tve_updated_post{$key}", $content['content']);
        update_post_meta($post_id, "tve_globals{$key}", !empty($content['tve_globals']) ? $content['tve_globals'] : array());
        /* global scripts */
        update_post_meta($post_id, 'tve_global_scripts', !empty($content['tve_global_scripts']) ? $content['tve_global_scripts'] : array());
    }

    update_post_meta($post_id, 'tve_landing_page', $landing_page_template);
}

/**
 * reset landing page to its default content
 * this assumes that the tve_landing_page post meta field is set to the value of the correct landing page template
 *
 * @param int $post_id
 * @param string $landing_page_template - from where we should take the default LP content
 */
function tve_landing_page_reset($post_id, $landing_page_template, $reset_global_scripts = true)
{
    $post_content = tve_landing_page_default_content($landing_page_template);
    $globals = tve_get_post_meta($_POST['post_id'], 'tve_globals');

    tve_update_post_meta($post_id, 'tve_globals', array('lightbox_id' => isset($globals['lightbox_id']) ? $globals['lightbox_id'] : 0));
    tve_update_post_meta($post_id, 'tve_save_post', $post_content);
    tve_update_post_meta($post_id, 'tve_updated_post', $post_content);
    tve_update_post_meta($post_id, 'tve_custom_css', '');
    tve_update_post_meta($post_id, 'tve_user_custom_css', '');
    tve_update_post_meta($post_id, 'thrive_icon_pack', '');

    if ($reset_global_scripts) {
        update_post_meta($post_id, 'tve_global_scripts', array());
    }

    /* check to see if a default lightbox exists for this and if neccessary, create it */
    require_once plugin_dir_path(__FILE__) . '/landing-page/inc/TCB_Landing_Page.php';
    $tcb_landing_page = new TCB_Landing_Page($post_id, $landing_page_template);

    /* make sure the associated lightbox exists and is setup in the event manager */
    $tcb_landing_page->checkLightbox();

    tve_update_post_custom_fonts($post_id, array());
}

/**
 * return a list with the current saved Landing Page templates
 */
function tve_landing_pages_load()
{
    check_ajax_referer("tve-le-verify-sender-track129", "security");
    $templates = get_option('tve_saved_landing_pages_meta');
    $templates = empty($templates) ? array() : array_reverse($templates, true); // order by date DESC

    $html = '';

    $input = '<input type="hidden" class="lp_code" value="user-saved-template-%s"/>';
    $img = '<img src="' . TVE_LANDING_PAGE_TEMPLATE . '/thumbnails/%s" width="178" height="150"/>';
    $caption = '<span class="tve_cell_caption" style="font-size:10px">%s</span><span class="tve_cell_check"></span>';

    $item = '<span class="tve_grid_cell tve_landing_page_template tve_click" title="Choose %s">%s</span>';

    foreach ($templates as $index => $template) {
        if (!empty($_POST['template']) && $_POST['template'] != $template['template']) {
            continue;
        }

        $_content = sprintf($input, $index) .
            sprintf($img, $template['template'] . '.png') .
            sprintf($caption, $template['name'] . ' (' . strftime('%d.%m.%y', strtotime($template['date'])) . ')');
        $html .= sprintf($item, $template['name'], $_content);
    }
    echo $html ? $html : 'No saved Templates found';
    exit();
}

function tve_widget_options()
{
    check_ajax_referer("tve-le-verify-sender-track129", "security");

    if (!isset($_POST['widget']) || empty($_POST['widget'])) {
        die;
    }

    $widget_id_base = $_POST['widget'];
    $widget = tve_get_widget_class($widget_id_base);

    if ($widget) {
        call_user_func_array($widget->_get_form_callback(), array(array('number' => -1,)));
    }
    exit();
}

function tve_widgets_list()
{
    check_ajax_referer("tve-le-verify-sender-track129", "security");

    global $wp_widget_factory;

    foreach ($wp_widget_factory->widgets as $class_name => $widget) {
        if (strpos($class_name, "WP_") !== false || 1) {
            echo '<option value="' . $widget->id_base . '">' . $widget->name . '</option>';
        }
    }
    die;
}

function tve_display_widget()
{
    check_ajax_referer("tve-le-verify-sender-track129", "security");

    if (!isset($_POST['widget']) || empty($_POST['widget'])) {
        die;
    }

    $widget = tve_get_widget_class($_POST['widget']);
    $keys = array_keys($_POST['options']);

    if (!isset($keys[1]) || empty($keys[1])) {
        die;
    }

    $instance = isset($_POST['options'][$keys[1]]) && !empty($_POST['options'][$keys[1]]) ? $_POST['options'][$keys[1]] : array();
    if (count($instance)) {
        foreach ($instance as $key => $value) {
            if ($value === 'false') {
                $instance[$key] = 0;
            }
        }
    }
    the_widget(get_class($widget), $instance);

    exit();
}


function tve_do_widget_shortcode($atts)
{
    $widget = tve_get_widget_class($atts['widget']);

    $instance = array();

    foreach ($atts['options'] as $key => $value) {
        $pattern = '/^(.+?)\[(.+?)\]\[(.+?)\]$/';
        if (preg_match($pattern, $key, $var)) {
            $instance[$var[3]] = $value;
        }
    }

    if (!$widget) {
        return '<div class="tve_widget_container"></div>';
    }

    ob_start();
    the_widget(get_class($widget), $instance);
    $html = ob_get_contents();
    ob_end_clean();
    return '<div class="tve_widget_container">' . $html . '</div>';
}

function tve_get_widget_class($id_base)
{
    global $wp_widget_factory;
    $widget = null;

    foreach ($wp_widget_factory->widgets as $widgetObj) {
        if ($widgetObj->id_base === $id_base) {
            $widget = $widgetObj;
        }
    }
    return $widget;
}

function tve_widget_form_callback($instance)
{
    if (isset($_POST['options']) && !empty($_POST['options'])) {
        $keys = array_keys($_POST['options']);
        if (!isset($keys[1]) || empty($keys[1])) {
            return $instance;
        }
        $instance = $_POST['options'][$keys[1]];
        if (count($instance)) {
            foreach ($instance as $key => $value) {
                if ($value === 'false') {
                    $instance[$key] = 0;
                } else if ($value === 'true') {
                    $instance[$key] = 1;
                }
            }
        }
    }
    return $instance;
}

/**
 * get the link to the google font based on $font
 *
 * @param array|object $font
 */
function tve_custom_font_get_link($font)
{
    if (is_array($font)) {
        $font = (object)$font;
    }
    return "//fonts.googleapis.com/css?family=" . str_replace(" ", "+", $font->font_name) . ($font->font_style != 0 ? ":" . $font->font_style : "") . ($font->font_bold != 0 ? "," . $font->font_bold : "") . ($font->font_italic != 0 ? $font->font_italic : "") . ($font->font_character_set != 0 ? "&subset=" . $font->font_character_set : "");
}

/**
 * get all fonts created with the font manager
 * @return array
 */
function tve_get_all_custom_fonts()
{
    $all_fonts = get_option('thrive_font_manager_options');
    if (empty($all_fonts)) {
        $all_fonts = array();
    } else {
        $all_fonts = json_decode($all_fonts);
    }

    return (array)$all_fonts;
}

/**
 *
 * @param $post_id
 * @param $custom_font_classes array containing all the custom font css classes
 */
function tve_update_post_custom_fonts($post_id, $custom_font_classes)
{
    $all_fonts = tve_get_all_custom_fonts();

    $post_fonts = array();
    foreach (array_unique($custom_font_classes) as $cls) {
        foreach ($all_fonts as $font) {
            if ($font->font_class == $cls) {
                $post_fonts[] = tve_custom_font_get_link($font);
                break;
            }
        }
    }

    tve_update_post_meta($post_id, 'thrive_tcb_post_fonts', $post_fonts);
}

/**
 * get all custom fonts used for a post
 *
 * @param $post_id
 *
 * @return array with index => href link
 */
function tve_get_post_custom_fonts($post_id)
{
    $post_fonts = tve_get_post_meta($post_id, 'thrive_tcb_post_fonts');
    if (empty($post_fonts)) {
        return array();
    }
    $all_fonts = tve_get_all_custom_fonts();
    $all_fonts_links = array();
    foreach ($all_fonts as $f) {
        $all_fonts_links [] = tve_custom_font_get_link($f);
    }
    if (empty($all_fonts)) {
        // all fonts have been deleted - delete the saved fonts too for this post
        tve_update_post_meta($post_id, 'thrive_tcb_post_fonts', array());
    } else {
        $fixed = array_intersect($post_fonts, $all_fonts_links);
        if (count($fixed) != count($post_fonts)) {
            $post_fonts = $fixed;
            tve_update_post_meta($post_id, 'thrive_tcb_post_fonts', $post_fonts);
        }
    }

    $theme_post_fonts = get_post_meta($post_id, 'thrive_post_fonts', true);
    $theme_post_fonts = empty($theme_post_fonts) ? array() : json_decode($theme_post_fonts);

    /* return just fonts that will not be loaded from any possible theme shortcodes */
    return array_diff($post_fonts, $theme_post_fonts);
}

/**
 * called only if the user is not currently using a Thrive theme
 * it's a hook on the wp_head WP action
 *
 * outputs the CSS needed for the custom fonts
 */
function tve_load_font_css()
{
    $all_fonts = tve_get_all_custom_fonts();

    if (empty($all_fonts)) {
        return;
    }
    echo '<style type="text/css">';
    foreach ($all_fonts as $font) {
        echo '.' . $font->font_class . '{';
        echo 'font-family:' . $font->font_name . ' !important;';
//        echo 'font-size:' . $font->font_size . ';';
//        echo 'line-height:' . $font->font_height . ';';
        echo 'color:' . $font->font_color . ';';
        echo '}';
        $inputs_css = <<<CSS
.$font->font_class input, .$font->font_class select, .$font->font_class textarea, .$font->font_class button {
    font-family: $font->font_name !important;
}
CSS;
        echo $inputs_css;
        echo $font->custom_css;
    }
    echo '</style>';
}

/**
 * check if we are in editing mode and add an extra body class for this
 *
 * @param array $classes
 */
function tve_add_body_editor_class($classes)
{
    if (is_editor_page()) {
        $classes [] = 'tve-body-editor';
    }
    return $classes;
}

/**
 * parse and prepare all the required configuration for the different events
 *
 * @param string $content TCB - meta post content
 */
function tve_parse_events($content)
{
    list($start, $end) = array(
        '__TCB_EVENT_',
        '_TNEVE_BCT__'
    );
    if (strpos($content, $start) === false) {
        return;
    }
    $triggers = tve_get_event_triggers();
    $actions = tve_get_event_actions();

    $event_pattern = "#data-tcb-events=('|\"){$start}(.+?){$end}('|\")#";

    /* hold all the javascript callbacks required for the identified actions */
    $javascript_callbacks = isset($GLOBALS['tve_event_manager_callbacks']) ? $GLOBALS['tve_event_manager_callbacks'] : array();
    /* holds all the Global JS required by different actions and event triggers on page load */
    $registered_javascript_globals = isset($GLOBALS['tve_event_manager_global_js']) ? $GLOBALS['tve_event_manager_global_js'] : array();

    /* hold all instances of the Action classes in order to output stuff in the footer, we need to get out of the_content filter */
    $registered_actions = isset($GLOBALS['tve_event_manager_actions']) ? $GLOBALS['tve_event_manager_actions'] : array();

    /*
     * match all instances for Event Configurations
     */
    if (preg_match_all($event_pattern, $content, $matches, PREG_OFFSET_CAPTURE) !== false) {

        foreach ($matches[2] as $i => $data) {
            $m = htmlspecialchars_decode($data[0]); // the actual matched regexp group
            if (!($_params = @json_decode($m, true))) {
                $_params = array();
            }
            if (empty($_params)) {
                continue;
            }

            foreach ($_params as $index => $event_config) {
                if (empty($event_config['t']) || empty($event_config['a']) || !isset($triggers[$event_config['t']]) || !isset($actions[$event_config['a']])) {
                    continue;
                }
                /** @var TCB_Event_Action_Abstract $action */
                $action = $actions[$event_config['a']];
                $registered_actions [] = array(
                    'class' => $action,
                    'event_config' => $event_config
                );

                if (!isset($javascript_callbacks[$event_config['a']])) {
                    $javascript_callbacks[$event_config['a']] = $action->getJsActionCallback();
                    $action->mainPostCallback($event_config);
                }
                if (!isset($registered_javascript_globals['action_' . $event_config['a']])) {
                    $registered_javascript_globals['action_' . $event_config['a']] = $action;
                }
                if (!isset($registered_javascript_globals['trigger_' . $event_config['t']])) {
                    $registered_javascript_globals['trigger_' . $event_config['t']] = $triggers[$event_config['t']];
                }
            }
        }
    }

    if (empty($javascript_callbacks)) {
        return;
    }

    /* include the Event Manager css file only at this point, when we know that we'll have events on page */
    tve_enqueue_style('thrive_events', TVE_EDITOR_CSS . '/events.css');

    /* we need to add all the javascript callbacks into the page */
    /* this cannot be done using wp_localize_script WP function, as each if the callback will actually be JS code */
    ///euuuughhh

    //TODO: how could we handle this in a more elegant fashion ?
    $GLOBALS['tve_event_manager_callbacks'] = $javascript_callbacks;
    $GLOBALS['tve_event_manager_global_js'] = $registered_javascript_globals;
    $GLOBALS['tve_event_manager_actions'] = $registered_actions;

    /* remove previously assigned callback, if any - in case of list pages */
    remove_action('wp_print_footer_scripts', 'tve_print_footer_events');
    add_action('wp_print_footer_scripts', 'tve_print_footer_events');
}

/**
 * load up all event manager callbacks into the page
 */
function tve_print_footer_events()
{
    if (!empty($GLOBALS['tve_event_manager_callbacks'])) {
        echo '<script type="text/javascript">var TVE_Event_Manager_Registered_Callbacks = {};';
        foreach ($GLOBALS['tve_event_manager_callbacks'] as $key => $js_function) {
            echo 'TVE_Event_Manager_Registered_Callbacks.' . $key . ' = ' . $js_function . ';';
        }
        echo '</script>';
    }

    if (!empty($GLOBALS['tve_event_manager_global_js'])) {
        foreach ($GLOBALS['tve_event_manager_global_js'] as $object) {
            $object->outputGlobalJavascript();
        }
    }

    if (!empty($GLOBALS['tve_event_manager_actions'])) {
        foreach ($GLOBALS['tve_event_manager_actions'] as $data) {
            if (!empty($data['class']) && $data['class'] instanceof TCB_Event_Action_Abstract) {
                echo $data['class']->applyContentFilter($data['event_config']);
            }
        }
    }
}

/**
 * transform the tve_globals meta field into css / html properties and rules
 * @param $post_id
 *
 * @return array
 */
function tve_get_lightbox_globals($post_id)
{
    $config = get_post_meta($post_id, 'tve_globals', true);

    $html = array(
        'overlay' => array(
            'css' => empty($config['l_oo']) ? '' : 'opacity:' . $config['l_oo'],
            'custom_color' => empty($config['l_ob']) ? '' : ' data-tve-custom-colour="' . $config['l_ob'] . '"'
        ),
        'content' => array(
            'custom_color' => empty($config['l_cb']) ? '' : ' data-tve-custom-colour="' . $config['l_cb'] . '"',
            'class' => empty($config['l_ccls']) ? '' : ' ' . $config['l_ccls'],
            'css' => ''
        ),
        'inner' => array(
            'css' => ''
        ),
        'close' => array(
            'class' => '',
            'css' => ''
        )
    );

    if (!empty($config['l_cimg'])) { // background image
        $html['content']['css'] .= "background-image:url('{$config['l_cimg']}');background-repeat:no-repeat;background-size:cover;";
    } elseif (!empty($config['l_cpat'])) {
        $html['content']['css'] .= "background-image:url('{$config['l_cpat']}');background-repeat:repeat;";
    }

    if (!empty($config['l_cbs'])) { // content border style
        $html['content']['class'] .= ' ' . $config['l_cbs'];
        $html['close']['class'] .= ' ' . $config['l_cbs'];
    }

    if (!empty($config['l_cbw'])) { // content border width
        $html['content']['css'] .= "border-width:{$config['l_cbw']};";
        $html['close']['css'] .= "border-width:{$config['l_cbw']};";
    }

    if (!empty($config['l_cmw'])) { // content max width
        $html['content']['css'] .= "max-width:{$config['l_cmw']}";
    }

    if (!empty($config['l_cmh'])) { // content max height
        if (!is_editor_page()) {
            $_height = intval($config['l_cmh']);
            /* we need to substract 30px, the padding of the lightbox - when not in editing mode */
            $config['l_cmh'] = ($_height - 30) . 'px';
        }
        $html['inner']['css'] .= "max-height:{$config['l_cmh']}";
    }

    return $html;
}

/**
 * this is used to fix any plugin conflicts that might appear
 *
 * 1. YARPP - we need to disable their the_content filter when in editing mode,
 *      - they apply the_content filter automatically when querying the database for related posts
 *      - they have a filter for blacklisting a filters the_content, but that does not solve the issue - wp will never call our filter anymore
 */
function tve_fix_plugin_conflicts()
{
    global $yarpp;
    if (is_editor_page_raw()) {
        if ($yarpp) {
            remove_filter('the_content', array($yarpp, 'the_content'), 1200);
        }
    }
}

/**
 * fills in some default font data and adds the custom font to the custom fonts list
 *
 * @return array the full array for the added font
 */
function tve_add_custom_font($font_data)
{
    $custom_fonts = tve_get_all_custom_fonts();

    if (!isset($font_data['font_id'])) {
        $font_data['font_id'] = count($custom_fonts) + 1;
    }

    if (!isset($font_data['font_class'])) {
        $font_data['font_class'] = 'ttfm' . $font_data['font_id'];
    }
    if (!isset($font_data['custom_css'])) {
        $font_data['custom_css'] = '';
    }
    if (!isset($font_data['font_color'])) {
        $font_data['font_color'] = '';
    }
    if (!isset($font_data['font_height'])) {
        $font_data['font_height'] = '1.6em';
    }
    if (!isset($font_data['font_size'])) {
        $font_data['font_size'] = '1.6em';
    }
    if (!isset($font_data['font_character_set'])) {
        $font_data['font_character_set'] = 'latin';
    }

    $custom_fonts [] = $font_data;

    update_option('thrive_font_manager_options', json_encode($custom_fonts));

    return $font_data;
}

/**
 * run any necessary code that would be required during an upgrade
 *
 * @param $old_version
 * @param $new_version
 */
function tve_run_plugin_upgrade($old_version, $new_version)
{
    /* this is extremely expensive in terms of performance - so we avoid running it  on each request */
    global $wp_rewrite;

    /** @var WP_Rewrite $wp_rewrite */
    $wp_rewrite->flush_rules(true);
}

/**
 *
 * when a page is edited from admin -> we need to use the same title for the associated lightbox, if the page in question is a landing page
 * @param $post_id
 */
function tve_save_post_callback($post_id)
{
    $post_type = get_post_type($post_id);
    if ($post_type != 'page') {
        return;
    }
    $is_landing_page = tve_post_is_landing_page($post_id);
    $tve_globals = tve_get_post_meta($post_id, 'tve_globals');

    if (!$is_landing_page || empty($tve_globals['lightbox_id'])) {
        return;
    }
    $lightbox = get_post($tve_globals['lightbox_id']);
    if (!$lightbox) {
        return;
    }

    wp_update_post(array(
        'ID' => $tve_globals['lightbox_id'],
        'post_title' => 'Lightbox - ' . get_the_title($post_id)
    ));
}

/**
 * output the tinymce editor in the footer area
 */
function tve_output_wysiwyg_editor()
{
    $lb_before = '<div class="tve_mce_holder_overlay"></div><div id="tve_mce_holder" class="tve_mce_holder" style="display: none">' .
        '<a class="tve-lightbox-close" href="javascript:void(0)" title="Close"><span class="tve_lightbox_close tve_mce_close tve_click" data-ctrl="controls.lb_close"></span></a>' .
        '<div class="tve_mce_inner">';
    $lb_after = '</div><div class="tve_lightbox_buttons"><input type="button" class="tve_save_lightbox tve_btn_green tve_mce_save tve_mousedown" data-ctrl="controls.lb_save" value="Save"></div></div>';

    echo $lb_before;
    wp_editor('', 'tve_tinymce_tpl', array(
        'dfw' => true,
        'tabfocus_elements' => 'insert-media-button,save-post',
        'editor_height' => 360,
        'textarea_rows' => 15,
    ));
    echo $lb_after;
}

/**
 * @param string $title lightbox title
 * @param string $tcb_content
 * @param array $tve_globals tve_globals array to save for the lightbox
 * @param array $extra_meta_data array of key => value pairs, each will be saved in a meta field for the lightbox
 *
 * @return int the saved lightbox id
 */
function tve_create_lightbox($title = '', $tcb_content = '', $tve_globals = array(), $extra_meta_data = array())
{
    /* just to make sure that our content filter does not get applied when inserting a (possible) new lightbox */
    $GLOBALS['TVE_CONTENT_SKIP_ONCE'] = true;
    $lightbox_id = wp_insert_post(array(
        'post_content' => '',
        'post_title' => $title,
        'post_status' => 'publish',
        'post_type' => 'tcb_lightbox',
    ));
    foreach ($extra_meta_data as $meta_key => $meta_value) {
        update_post_meta($lightbox_id, $meta_key, $meta_value);
    }

    update_post_meta($lightbox_id, 'tve_save_post', $tcb_content);
    update_post_meta($lightbox_id, 'tve_updated_post', $tcb_content);
    update_post_meta($lightbox_id, 'tve_globals', $tve_globals);

    unset($GLOBALS['TVE_CONTENT_SKIP_ONCE']);

    return $lightbox_id;
}

/**
 * Fix added by Paul McCarthy - 25th September 2014.
 * This is a fix for the theme called "Pitch" that applies a filter to wordpress media gallery that runs a backend only native Wordpress function get_current_screen()
 * As we're loading the media library in the front end, the function that's called doesn't exist and causes a fatal error
 * This function removes the filter so that it isn't processed while in Thrive Editor mode.
 */
function tve_turn_off_get_current_screen()
{
    if (is_editor_page()) {
        remove_filter('media_view_strings', 'siteorigin_settings_media_view_strings', 10, 2);
    }
}

/**
 * wrapper over the wp enqueue_style function
 * it will append the TVE_VERSION as a query string parameter to the $src if $ver is left empty
 *
 * @param $handle
 * @param $src
 * @param array $deps
 * @param bool $ver
 * @param $media
 */
function tve_enqueue_style($handle, $src, $deps = array(), $ver = false, $media = 'all')
{
    if ($ver === false) {
        $ver = TVE_VERSION;
    }
    wp_enqueue_style($handle, $src, $deps, $ver, $media);
}

/**
 * wrapper over the wp_enqueue_script functions
 * it will add the plugin version to the script source if no version is specified
 *
 * @param $handle
 * @param string $src
 * @param array $deps
 * @param bool $ver
 * @param bool $in_footer
 */
function tve_enqueue_script($handle, $src = '', $deps = array(), $ver = false, $in_footer = false)
{
    if ($ver === false) {
        $ver = TVE_VERSION;
    }
    wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
}

/**
 * enqueue the CSS for the icon pack used by the user
 */
function tve_enqueue_icon_pack()
{
    if (!wp_style_is('thrive_icon_pack', 'enqueued')) {
        $icon_pack = get_option('thrive_icon_pack');
        !empty($icon_pack['css']) && wp_enqueue_style('thrive_icon_pack', $icon_pack['css'], array(), isset($icon_pack['css_version']) ? $icon_pack['css_version'] : TVE_VERSION);
    }
}

/**
 * Fix added by Paul McCarthy 16th October 2014
 * Check to see if the theme is thesis or not so that we can load the the Thesis child theme stylesheet on tcb_lightbox custom post types
 *
 * @return bool
 */
function tve_is_thesis()
{
    return (wp_get_theme() == "Thesis") ? true : false;
}

function tve_categories_list() {
    check_ajax_referer("tve-le-verify-sender-track129", "security");
    $search_term = isset($_POST['term']) ? $_POST['term'] : '';
    $terms = get_terms('category', array('search' => $search_term));
    $response = array();
    foreach($terms as $item) {
        $term = array();
        $term['label'] = $item->name;
        $term['value'] = $item->name;
        $response[] = $term;
    }
    wp_send_json($response);
}

function tve_tags_list() {
    check_ajax_referer("tve-le-verify-sender-track129", "security");
    $search_term = isset($_POST['term']) ? $_POST['term'] : '';
    $terms = get_terms('post_tag', array('search' => $search_term));
    $response = array();
    foreach($terms as $item) {
        $term = array();
        $term['label'] = $item->name;
        $term['value'] = $item->name;
        $response[] = $term;
    }
    wp_send_json($response);
}

function tve_custom_taxonomies_list() {
    check_ajax_referer("tve-le-verify-sender-track129", "security");
    $search_term = isset($_POST['term']) ? $_POST['term'] : '';

    function filter_taxonomies($value) {
        $search_term = isset($_POST['term']) ? $_POST['term'] : '';
        $banned = array('category', 'post_tag');
        if(in_array($value, $banned)) {
            return false;
        }
        if(!$search_term) {
            return true;
        }
        return strpos($value, $search_term) !== false;
    }
    $taxonomies = get_taxonomies();
    $terms = array_filter($taxonomies, 'filter_taxonomies');

    $response = array();
    foreach($terms as $item) {
        $term = array();
        $term['label'] = $item;
        $term['value'] = $item;
        $response[] = $term;
    }
    wp_send_json($response);
}

function tve_authors_list() {
    check_ajax_referer("tve-le-verify-sender-track129", "security");

    $search_term = isset($_POST['term']) ? $_POST['term'] : '';
    $users = get_users(array('search' => "*$search_term*"));

    $response = array();
    foreach($users as $item) {
        $term = array();
        $term['label'] = $item->data->display_name;
        $term['value'] = $item->data->display_name;
        $response[] = $term;
    }
    wp_send_json($response);
}

function tve_posts_list() {
    check_ajax_referer("tve-le-verify-sender-track129", "security");
    $search_term = isset($_POST['term']) ? $_POST['term'] : '';
    $args = array(
        'order_by' => 'post_title',
        'post_type' => 'any',
        's' => $search_term
    );
    $results = new WP_Query($args);

    $response = array();
    foreach($results->get_posts() as $post) {
        $term = array();
        $term['label'] = $post->post_title;
        $term['value'] = $post->post_title;
        $response[] = $term;
    }
    wp_send_json($response);
}
