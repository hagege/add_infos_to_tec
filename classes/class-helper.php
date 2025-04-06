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
}
