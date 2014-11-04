<?php
/**
 * Created by PhpStorm.
 * User: radu
 * Date: 06.08.2014
 * Time: 10:08
 */

/**
 * controller class to handle all the server-side logic for the Event Manager setup
 *
 * it will read the requested "step" / "route" from POST and return the appropriate HTML for that screen
 * Class TCB_Event_Manager_Controller
 */
class TCB_Event_Manager_Controller
{
    /**
     * @var TCB_Event_Manager_Controller
     */
    private static $_instance = null;

    /**
     * @var array
     */
    protected $request = array();

    /**
     * the response
     * @var string
     */
    protected $response = '';

    /**
     * singleton getInstance implementation
     * @return TCB_Event_Manager_Controller
     */
    public static final function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new TCB_Event_Manager_Controller($_REQUEST);
        }

        return self::$_instance;
    }

    /**
     *
     * @param $data array request data
     */
    public function __construct($data)
    {
        $this->request = $data;
    }

    /**
     * get a parameter from the request. If the requested parameter is not present, returns the $default value
     *
     * @param $key
     * @param null $default
     */
    protected function param($key, $default = null)
    {
        return isset($this->request[$key]) ? $this->request[$key] : $default;
    }

    /**
     * dispatch an AJAX request:
     * call the propriate method based on 'route' parameter
     */
    public function dispatch()
    {
        /* check if we can execute the requested route */
        $route = $this->param('route');
        $fn = $route . 'Action';

        if (!method_exists($this, $fn)) {
            $this->send();
        }

        $viewVariables = call_user_func(array($this, $fn));

        $this->renderView($route, $viewVariables)
            ->send();
    }

    /**
     * render the associated view for the matched route
     * this will search in the views/ folder for a file that matches the $route
     *
     * @param $route
     * @param array $data
     *
     * @return TCB_Event_Manager_Controller
     */
    protected function renderView($route, $data = array())
    {
        $view = dirname(dirname(__FILE__)) . '/views/' . $route . '.php';

        if (!file_exists($view)) {
            $this->response = 'No view found for ' . $route;
            return $this;
        }

        extract($data);

        ob_start();
        include $view;
        $this->response = ob_get_contents();
        ob_end_clean();

        return $this;
    }

    /**
     * list the Events associated for an element, or display a message if no event is added
     */
    public function listAction()
    {
        return array(
            'triggers' => tve_get_event_triggers(),
            'actions' => tve_get_event_actions(),
            'events' => $this->param('events', array())
        );
    }

    /**
     * display controls for adding / editing new events
     */
    public function formAction()
    {
        $actions = tve_get_event_actions();

        $action_code = $this->param('a', '');
        $action = empty($action_code) || !isset($actions[$action_code]) ? null : $actions[$action_code];

        if (!empty($action)) {
            $action->setConfig($this->param('config', array()));
        }

        return array(
            'config' => $this->param('config', array()),
            'triggers' => tve_get_event_triggers(),
            'actions' => $actions,
            'selected_trigger_code' => $this->param('t', ''),
            'selected_action_code' => $action_code,
            'selected_action' => $action,
            'is_edit' => $this->param('edit_page', '')
        );
    }

    /**
     * render the settings control for a specific action
     */
    public function settingsAction($extra_data = array())
    {
        $actions = tve_get_event_actions();
        $action_code = $this->param('event_action', '');
        $trigger_code = $this->param('event_trigger', '');

        if (!isset($actions[$action_code])) {
            $this->response = '';
            $this->send();
        }

        /** @var TCB_Event_Action_Abstract $action */
        $action = $actions[$action_code];

        $settings_data = array(
            'action' => $action_code,
            'trigger' => $trigger_code,
            'config' => $this->param('current_settings', array())
        );

        foreach ($extra_data as $k => $v) {
            $settings_data[$k] = $v;
        }

        /* give output control to the action handler */
        $action->renderSettings($settings_data);
        exit();
    }

    /**
     * create a new lightbox, either for a landing page or for a regular page
     */
    public function add_lightboxAction()
    {
        $post_id = $this->param('post_id');
        if (!$post_id) {
            $this->send();
        }

        $post = get_post($post_id);

        $landing_page_template = tve_post_is_landing_page($post_id);
        if ($landing_page_template) {
            $config = tve_get_landing_page_config($landing_page_template);
            /* lightbox has to have the same style family as the landing page */
//            if (empty($config['has_lightbox'])) {
//                $landing_page_template = '';
//            }
        }

        $lightbox_title = 'Lightbox - ' . $post->post_title . (!empty($config['name']) ? ' (' . $config['name'] . ')' : '');
        if ($landing_page_template) {
            require_once dirname(dirname(dirname(__FILE__))) . '/landing-page/inc/TCB_Landing_Page.php';
            $tcb_landing_page = new TCB_Landing_Page($post_id, $landing_page_template);
            $lightbox_id = $tcb_landing_page->newLightbox();
        } else {
            $lightbox_id = tve_create_lightbox($lightbox_title, '', array(), array());
        }

        $this->request = $this->param('previous_call'); // mimic the settings action

        $this->settingsAction(array(
            'success_message' => sprintf(
                '<strong>%s</strong> has been created. Click <a href="%s" target="_blank"><strong style="color: #000000">here</strong></a> to edit it (will open in a new tab).',
                $lightbox_title,
                tcb_get_editor_url($lightbox_id)
            )
        ));
    }

    /**
     * output the response
     */
    protected function send()
    {
        echo $this->response;
        exit();
    }
} 