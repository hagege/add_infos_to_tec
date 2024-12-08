<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package add-infos-to-the-events-calendar
 */

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// delete options again.
delete_option( 'add_infos_to_tec_settings' );
