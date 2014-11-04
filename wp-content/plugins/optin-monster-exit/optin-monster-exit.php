<?php
/**
 * Plugin Name: OptinMonster - Exit Intent Addon
 * Plugin URI:  http://optinmonster.com
 * Description: Enables exit intent for OptinMonster optins.
 * Author:      Thomas Griffin
 * Author URI:  http://thomasgriffinmedia.com
 * Version:     2.0.0
 * Text Domain: optin-monster-exit
 * Domain Path: languages
 *
 * OptinMonster is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * OptinMonster is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OptinMonster. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define necessary addon constants.
define( 'OPTIN_MONSTER_EXIT_PLUGIN_NAME', 'OptinMonster - Exit Intent Addon' );
define( 'OPTIN_MONSTER_EXIT_PLUGIN_VERSION', '2.0.0' );
define( 'OPTIN_MONSTER_EXIT_PLUGIN_SLUG', 'optin-monster-exit' );

add_action( 'plugins_loaded', 'optin_monster_exit_plugins_loaded' );
/**
 * Ensures the full OptinMonster plugin is active before proceeding.
 *
 * @since 2.0.0
 *
 * @return null Return early if OptinMonster is not active.
 */
function optin_monster_exit_plugins_loaded() {

    // Bail if the main class does not exist.
    if ( ! class_exists( 'Optin_Monster' ) ) {
        return;
    }

    // Fire up the addon.
    add_action( 'optin_monster_init', 'optin_monster_exit_plugin_init' );

}

/**
 * Loads all of the addon hooks and filters.
 *
 * @since 2.0.0
 */
function optin_monster_exit_plugin_init() {

    add_action( 'optin_monster_updater', 'optin_monster_exit_updater' );
    add_filter( 'optin_monster_panel_configuration_fields', 'optin_monster_exit_setting', 10, 2 );
    add_filter( 'optin_monster_save_optin', 'optin_monster_exit_save', 10, 3 );
    add_filter( 'optin_monster_data', 'optin_monster_exit_data', 10, 3 );

}

/**
 * Initializes the addon updater.
 *
 * @since 2.0.0
 *
 * @param string $key The user license key.
 */
function optin_monster_exit_updater( $key ) {

    $args = array(
        'plugin_name' => OPTIN_MONSTER_EXIT_PLUGIN_NAME,
        'plugin_slug' => OPTIN_MONSTER_EXIT_PLUGIN_SLUG,
        'plugin_path' => plugin_basename( __FILE__ ),
        'plugin_url'  => trailingslashit( WP_PLUGIN_URL ) . OPTIN_MONSTER_EXIT_PLUGIN_SLUG,
        'remote_url'  => 'http://optinmonster.com/',
        'version'     => OPTIN_MONSTER_EXIT_PLUGIN_VERSION,
        'key'         => $key
    );
    $optin_monster_exit_updater = new Optin_Monster_Updater( $args );

}

/**
 * Adds the exit intent setting to the Configuration panel.
 *
 * @since 2.0.0
 *
 * @param array $config    Array of configuration settings.
 * @param object $instance The edit panel object.
 * @return array $config   Amended array of configuration settings.
 */
function optin_monster_exit_setting( $config, $instance ) {

    // Return early if not the proper optin type.
    if ( isset( $instance->meta['type'] ) && ( 'lightbox' !== $instance->meta['type'] && 'footer' !== $instance->meta['type'] && 'slide' !== $instance->meta['type'] && 'canvas' !== $instance->meta['type'] ) ) {
        return $config;
    }

    // Add the exit intent option to the configuration setting.
    $config['exit']             = $instance->get_checkbox_field( 'exit', $instance->get_checkbox_setting( 'exit', '', 0 ), __( 'Enable Exit Intent?', 'optin-monster-exit' ), __( 'Shows the optin when a user navigates their mouse outside of the website window (exit intent).', 'optin-monster-exit' ) );
    $config['exit_sensitivity'] = $instance->get_text_field( 'exit_sensitivity', $instance->get_setting( 'exit_sensitivity', '', 20 ), __( 'Exit Intent Sensitivity', 'optin-monster-exit' ), __( 'Sets the sensitivity of exit intent detection on the horizontal X axis. Higher numbers make exit detection more sensitive.', 'optin-monster-exit' ) );
    return $config;

}

/**
 * Saves the exit intent setting.
 *
 * @since 2.0.0
 *
 * @param array $meta   The meta for the optin in the "_om_meta" field.
 * @param int $optin_id The opton ID to target.
 * @param array $fields Array of $_POST fields with "optin-monster" key.
 * @return array $meta  Amended optin meta.
 */
function optin_monster_exit_save( $meta, $optin_id, $fields ) {

    // Return early if not the proper optin type.
    if ( isset( $meta['type'] ) && ( 'lightbox' !== $meta['type'] && 'canvas' !== $meta['type'] ) ) {
        return $meta;
    }

    $meta['exit']             = isset( $fields['exit'] ) ? 1 : 0;
    $meta['exit_sensitivity'] = isset( $fields['exit_sensitivity'] ) ? absint( $fields['exit_sensitivity'] ) : 20;
    return $meta;

}

/**
 * Adds the exit intent setting to the data sent to the JS API.
 *
 * @since 2.0.0
 *
 * @param array $data   Data to be converted to JSON for JS API..
 * @param int $optin_id The opton ID to target.
 * @param array $meta   The optin meta.
 * @return array $data  Amended optin data.
 */
function optin_monster_exit_data( $data, $optin_id, $meta ) {

    // Return early if not the proper optin type.
    if ( isset( $meta['type'] ) && ( 'lightbox' !== $meta['type'] && 'canvas' !== $meta['type'] ) ) {
        return $data;
    }

    $data['exit']             = isset( $meta['exit'] ) && $meta['exit'] ? 1 : 0;
    $data['exit_sensitivity'] = isset( $meta['exit_sensitivity'] ) ? $meta['exit_sensitivity'] : 20;
    return $data;

}