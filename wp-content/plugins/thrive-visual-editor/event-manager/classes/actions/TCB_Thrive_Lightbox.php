<?php
/**
 * Created by PhpStorm.
 * User: radu
 * Date: 05.08.2014
 * Time: 14:35
 */

/**
 *
 * handles the server-side logic for the Thrive Lightbox action = opens a lightbox on an Event Trigger
 *
 * Class TCB_Thrive_Lightbox
 */
class TCB_Thrive_Lightbox extends TCB_Event_Action_Abstract
{
    private static $_LOADED_LIGHTBOXES = array();

    /**
     * available lightbox animations
     * @var array
     */
    protected $_animations = array(
        'instant' => 'Instant (No animation)',
        'zoom_in' => 'Zoom',
        'zoom_out' => 'Zoom Out',
        'rotate' => 'Rotational',
        'slide_top' => 'Slide in from Top',
        'slide_bottom' => 'Slide in from Bottom',
        'lateral' => 'Lateral'
    );

    /**
     * Should return the user-friendly name for this Action
     *
     * @return string
     */
    public function getName()
    {
        return 'Open Thrive Lightbox';
    }

    /**
     * Should output the settings needed for this Action when a user selects it from the list
     *
     * @param mixed $data
     */
    public function renderSettings($data)
    {
        $post_id = $_POST['post_id'];
        $landing_page_template = tve_post_is_landing_page($post_id);

        $all_lightboxes = get_posts(array(
            'posts_per_page' => -1,
            'post_type' => 'tcb_lightbox',
        ));

        $data['lightboxes'] = array();
        foreach ($all_lightboxes as $lightbox) {
            $lightbox_lp = get_post_meta($lightbox->ID, 'tve_lp_lightbox', true);
            if (!empty($landing_page_template)) {
                if ($lightbox_lp != $landing_page_template) {
                    continue;
                }
            } elseif (!empty($lightbox_lp)) {
                continue;
            }
            $data['lightboxes'] []= $lightbox;
        }
        /* we use this to display the user the possibility of creating a new lightbox */
        $data['for_landing_page'] = $landing_page_template;
        $this->renderTCBSettings('lightbox', $data);
    }

    /**
     * Should return an actual string containing the JS function that's handling this action.
     * The function will be called with 3 parameters:
     *      -> event_trigger (e.g. click, dblclick etc)
     *      -> action_code (the action that's being executed)
     *      -> config (specific configuration for each specific action - the same configuration that has been setup in the settings section)
     *
     * Example (php): return 'function (trigger, action, config) { console.log(trigger, action, config); }';
     *
     * The output MUST be a valid JS function definition.
     *
     * @return string the JS function definition (declaration + body)
     */
    public function getJsActionCallback()
    {
        ob_start();
        include dirname(dirname(dirname(__FILE__))) . '/views/js/lightbox.php';

        $js = ob_get_contents();
        ob_end_clean();

        return preg_replace('#^<script type="text/javascript">(.+)</script>#s', '$1', trim($js));
    }

    /**
     * makes all necessary changes to the content depending on the $data param
     *
     * this gets called each time this action is encountered in the DOM event configuration
     *
     * @param $data
     */
    public function applyContentFilter($data)
    {
        $lightbox_id = isset($data['config']['l_id']) ? intval($data['config']['l_id']) : 0;

        if (!$lightbox_id) {
            return false;
        }

        if (isset(self::$_LOADED_LIGHTBOXES[$lightbox_id])) {
            return '';
        }

        $lightbox = get_post($lightbox_id);
        if (empty($lightbox)) {
            return '';
        }

        global $post;
        $old_post = $post;
        $post = $lightbox;

        $lightbox_content = str_replace(']]>', ']]&gt;', apply_filters('the_content', $lightbox->post_content));
        $config = tve_get_lightbox_globals($lightbox->ID);

        $lightbox = sprintf(
            '<div style="display: none" id="tve_thrive_lightbox_%s"><div class="tve_p_lb_overlay" style="%s"%s></div>' .
            '<div class="tve_p_lb_content cnt%s" style="%s"%s><div class="tve_p_lb_inner" id="tve-p-scroller" style="%s"><article>%s</article></div>' .
            '<a href="javascript:void(0)" class="tve_p_lb_close%s" style="%s"%s title="Close">x</a></div></div>',
            $lightbox_id,
            $config['overlay']['css'],
            $config['overlay']['custom_color'],
            $config['content']['class'],
            $config['content']['css'],
            $config['content']['custom_color'],
            $config['inner']['css'],
            $lightbox_content,
            $config['close']['class'],
            $config['close']['css'],
            $config['content']['custom_color']
        );
        $post = $old_post;
        self::$_LOADED_LIGHTBOXES[$lightbox_id] = true;

        return $lightbox;
    }

    /**
     * output the main options for this lightbox (in the editor events list)
     * @return string
     */
    public function getSummary()
    {
        $config = $this->config;
        if (empty($config)) {
            return '';
        }

        $animation = empty($config['l_anim']) ? $this->_animations['instant'] : $this->_animations[$config['l_anim']];
        return '; Animation: ' . $animation;
    }

    /**
     * output edit links for the lightbox
     */
    public function getRowActions()
    {
        if (empty($this->config)) {
            return '';
        }

        return sprintf(
            '<br><a href="%s" title="Edit this Lightbox" target="_blank" class="tve_link_no_warning">Edit this Lightbox</a>',
            tcb_get_editor_url($this->config['l_id'])
        );
    }

    /**
     * check if the associated lightbox exists and it's not trashed
     * @return bool
     */
    public function validateConfig()
    {
        $lightbox_id = $this->config['l_id'];
        if (empty($lightbox_id)) {
            return false;
        }

        $lightbox = get_post($lightbox_id);
        if (empty($lightbox) || $lightbox->post_status === 'trash' || $lightbox->post_type != 'tcb_lightbox') {
            return false;
        }

        return true;
    }

    /**
     * make sure that if custom icons are used, the CSS for that is included in the main page
     * @param array $data
     */
    public function mainPostCallback($data)
    {
        $lightbox_id = empty($data['config']['l_id']) ? 0 : $data['config']['l_id'];
        if (tve_get_post_meta($lightbox_id, 'thrive_icon_pack') && !wp_style_is('thrive_icon_pack', 'enqueued')) {
            tve_enqueue_icon_pack();
        }

        /* check for the lightbox style and include it */
        tve_enqueue_style_family($lightbox_id);
    }


} 