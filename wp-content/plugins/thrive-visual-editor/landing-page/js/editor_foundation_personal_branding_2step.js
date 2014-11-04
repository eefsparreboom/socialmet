/**
 * Created by radu on 21.08.2014.
 */
var TVE_Content_Builder = TVE_Content_Builder || {};

(function ($) {
    TVE_Content_Builder.landing_page = {
        click: {
            clear_shadow: function($btn, $element) {
                $element.addClass('tve_no_shadow');
                tve_remove_css_rule($element.tve_color_selector(), 'box-shadow');
            }
        },
        content_width_selector: function() {
            return '.tve_post_lp';
        },
        max_width_callback: function (element, value) {
            tve_path_params.tve_globals['lp_cmw'] = value;
            tve_path_params.tve_globals['lp_cmw_apply_to'] = 'tve_post_lp';
        }
    }
})(jQuery);
