<?php
/** setup the event and event callbacks( = actions) manager */

require_once dirname(__FILE__) . '/classes/TCB_Event_Action_Abstract.php';
require_once dirname(__FILE__) . '/classes/TCB_Event_Trigger_Abstract.php';

/**
 * get all available Event triggers
 *
 * Each event trigger a a way for the user to interact with a DOM element on a page
 * each trigger must have a javascript name for the event, e.g. 'click', 'dblclick' as a key and an existing class as value
 *
 * each Event Trigger class must override TCB_Event_Trigger_Abstract
 *
 * @see TCB_Event_Trigger_Abstract
 */
function tve_get_event_triggers()
{
    /* make sure these will not get overwritten */
    $tcb_triggers = array(
        'click' => 'TCB_Event_Trigger_Click',
        'mouseover' => 'TCB_Event_Trigger_Mouseover',
        'tve-viewport' => 'TCB_Event_Trigger_Viewport'
    );

    $api_triggers = apply_filters('tcb_event_triggers', array());

    if (is_array($api_triggers)) {
        foreach ($api_triggers as $key => $class_name) {
            $key = strtolower($key);
            if (isset($tcb_triggers[$key]) || !is_string($class_name) || !preg_match('#^([a-z0-9-_]+)$#', $key) || !class_exists($class_name)) {
                continue;
            }
            $tcb_triggers[$key] = $class_name;
        }
    }

    $triggers = array();
    foreach ($tcb_triggers as $key => $class) {
        $triggers[$key] = TCB_Event_Trigger_Abstract::triggerFactory($class);
    }

    return $triggers;
}

/**
 * get all available event actions
 *
 * Event Actions are behaviours that happen after a user interaction via an event, such as 'click'
 *
 * each event action must extend the TCB_Event_Action_Abstract class and implement its required methods
 *
 * All the classes that are specified here MUST be previously loaded
 * Each array key has to be a lowercase, unique identifier for the Action
 *
 * @return array with action_key => Action Name (Action name will be taken from the class representing the Action)
 */
function tve_get_event_actions()
{
    $tcb_event_actions = array(
        'thrive_lightbox' => 'TCB_Thrive_Lightbox',
        'thrive_animation' => 'TCB_Thrive_CSS_Animation',
    );

    if(isset($_POST['post_id']) && !empty($_POST['post_id'])) {
        $_post_type = get_post_type((int)$_POST['post_id']);
        if($_post_type === 'tcb_lightbox') {
            unset($tcb_event_actions['thrive_lightbox']);
        }
    }

    $api_event_actions = apply_filters('tcb_event_actions', array());
    if (is_array($api_event_actions)) {
        foreach ($api_event_actions as $key => $class_name) {
            $key = strtolower($key);
            if (isset($tcb_event_actions[$key]) || !is_string($class_name) || !preg_match('#^([a-z0-9-_]+)$#', $key) || !class_exists($class_name)) {
                continue;
            }
            $tcb_event_actions[$key] = $class_name;
        }
    }

    $actions = array();
    foreach ($tcb_event_actions as $key => $class) {
        $actions[$key] = TCB_Event_Action_Abstract::actionFactory($class);
    }

    return $actions;
}

/**
 * receives the AJAX calls for various actions related to the Event Manager setup
 */
function tve_event_manager_ajax()
{
    check_ajax_referer("tve-le-verify-sender-track129", "security");
    require_once dirname(__FILE__) . '/classes/TCB_Event_Manager_Controller.php';

    $route = isset($_POST['route']) ? $_POST['route'] : '';
    if (empty($route)) {
        exit('No route provided');
    }

    TCB_Event_Manager_Controller::getInstance()->dispatch();

}
