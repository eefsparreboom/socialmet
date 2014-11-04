var TVE_Content_Builder = TVE_Content_Builder || {};

(function ($) {
    TVE_Content_Builder.landing_page = {
        click: {
        },
        content_width_selector: function() {
            return '.tve_content_width, .out .in';
        },
        max_width_callback: function (element, value) {
            tve_path_params.tve_globals['lp_cmw'] = value;
            tve_path_params.tve_globals['lp_cmw_apply_to'] = 'tve_lp_content';
        }
    }

})(jQuery);