<?php
/**
 * File with helper-functions for this plugin.
 *
 * @package add-infos-to-the-events-calendar
 */

namespace addInfosToTheEventsCalendar;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Helper-method.
 */
class Helper {
	/**
	 * Return the version of the given file.
	 *
	 * With WP_DEBUG or plugin-debug enabled its @filemtime().
	 * Without this it's the plugin-version.
	 *
	 * @param string $filepath The absolute path to the requested file.
	 *
	 * @return string
	 */
	public static function get_file_version( string $filepath ): string {
		// check for WP_DEBUG.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			return filemtime( $filepath );
		}

		$plugin_version = AIT_VERSION;

		/**
		 * Filter the used file version (for JS- and CSS-files which get enqueued).
		 *
		 * @since 1.6.0 Available since 1.6.0.
		 *
		 * @param string $plugin_version The plugin-version.
		 * @param string $filepath The absolute path to the requested file.
		 */
		return apply_filters( 'ait_file_version', $plugin_version, $filepath );
	}

	/**
	 * Return the plugin support url: the forum on WordPress.org.
	 *
	 * @return string
	 */
	public static function get_plugin_support_url(): string {
		return 'https://wordpress.org/support/plugin/add-infos-to-the-events-calendar/';
	}

	/**
	 * Return the settings-URL.
	 *
	 * @return string
	 */
	public static function get_settings_url(): string {
		$params = array(
			'page' => 'ait_add_infos_to_tec_settings_page',
		);
		return add_query_arg( $params, get_admin_url() . 'options-general.php' );
	}
}
