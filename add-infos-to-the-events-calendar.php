<?php
/**
 * File to handle the plugin tasks.
 *
 * @package add-infos-to-the-events-calendar
 */

// securing against unauthorized access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// do nothing if PHP-version is not 7.4 or newer.
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	return;
}

// set version.
const AIT_VERSION = '1.5.1';

/**
 * Load language files.
 *
 * TODO remove and replace with translate.wordpress.org.
 *
 * @return void
 */
function ait_meine_textdomain_laden(): void {
	load_plugin_textdomain(
		'add-infos-to-the-events-calendar',
		false,
		basename( __DIR__ ) . '/languages'
	);
}
add_action( 'init', 'ait_meine_textdomain_laden' );

/**
 * Get the color settings from style_fuss.css for the design of the buttons
 *
 * @return void
 */
function ait_fs_style_fuss_plugin_scripts(): void {
	// include css file.
	wp_enqueue_style(
		'ait',
		plugin_dir_url( __FILE__ ) . 'assets/css/ait_style_fuss.css',
		array(),
		AIT_VERSION,
	);

	// variables for button design.
	$add_infos_to_tec_options = get_option( 'add_infos_to_tec_settings' );
	if ( ! is_array( $add_infos_to_tec_options ) ) {
		$add_infos_to_tec_options = array();
	}
	$button_hintergrund       = ! empty( $add_infos_to_tec_options['fs_hintergrundfarbe_button'] ) ? $add_infos_to_tec_options['fs_hintergrundfarbe_button'] : '';
	$button_vordergrund       = ! empty( $add_infos_to_tec_options['fs_vordergrundfarbe_button'] ) ? $add_infos_to_tec_options['fs_vordergrundfarbe_button'] : '';
	$button_hover_hintergrund = ! empty( $add_infos_to_tec_options['fs_hover_hintergrundfarbe_button'] ) ? $add_infos_to_tec_options['fs_hover_hintergrundfarbe_button'] : '';
	$button_hover_vordergrund = ! empty( $add_infos_to_tec_options['fs_hover_vordergrundfarbe_button'] ) ? $add_infos_to_tec_options['fs_hover_vordergrundfarbe_button'] : '';
	$button_rund              = ! empty( $add_infos_to_tec_options['fs_runder_button'] ) ? $add_infos_to_tec_options['fs_runder_button'] : '';
	$custom_css               = '
        a.fuss_button-beitrag {
            color: ' . esc_attr( $button_vordergrund ) . '!important;
            background-color: ' . esc_attr( $button_hintergrund ) . '!important;
                text-decoration: none!important;
                border-radius: ' . absint( $button_rund ) . 'px;
        }
        a.fuss_button-beitrag:hover{
          color: ' . esc_attr( $button_hover_vordergrund ) . '!important;
          background-color: ' . esc_attr( $button_hover_hintergrund ) . '!important;
            text-decoration: none!important;
        }';
	wp_add_inline_style( 'ait', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'ait_fs_style_fuss_plugin_scripts' );

/**
 * Shortcodes for footer at the single event.
 *
 * Automatically displays the text from "Caption" in italics by default for an event or a post.
 *
 * Call Examples:
 * [fuss link="https://externer_link.de" vl=""] --> always shows picture credits, then more info with the link to external website and at vl="" the link to "more events".
 * [fuss vl=""] --> always shows picture credits, but no link to external website and at vl="" the link to "more events".
 * -> vl = list of events
 * [fuss] --> always shows picture credits, but no link to external website.
 * [fuss link="https://externer_link.de" vl="nature"] --> always shows picture credits, then more info with the link to external website and at vl="Nature" the link to "more events: nature".
 * -> (of course the category must exist in The Events Calendar (this is checked by a function). If the category does not exist, the event list will be shown.)
 * [fuss vl="" il="http://internal_link.de/example"] --> always shows picture credits, but no link to external website and at vl="" the link to "more events" and at il="http://internal_link.de/example" the link to another external or internal webesite.
 * -> internal used: fm, kfm, ferien
 *
 * TODO using hooks for internal things.
 *
 * @param array $attributes List of attributes.
 * @return string
 */
function ait_fs_beitrags_fuss_pi( array $attributes ): string {
	// secure the attributes.
	$werte = shortcode_atts(
		array(
			'link'   => '',
			'fm'     => 'nein',
			'kfm'    => 'nein',
			'ferien' => 'nein',
			'vl'     => 'nein',
			'il'     => '',
		),
		$attributes
	);

	// collect the output.
	$fs_ausgabe = '';

	// output line above.
	$add_infos_to_tec_options = get_option( 'add_infos_to_tec_settings' );

	// generate default values for not existing entries in the settings.
	$add_infos_to_tec_options = ait_set_default_settings( $add_infos_to_tec_options );
	if ( 1 === absint( $add_infos_to_tec_options['fs_linie_oben'] ) ) {
		$fs_ausgabe .= '<hr>';
	}

	// get path from the settings.
	$ait_pfad = ! empty( $add_infos_to_tec_options['fs_option_pfad'] ) ? $add_infos_to_tec_options['fs_option_pfad'] : '';

	// get categories used by TEC.
	$kategorien = ait_cliff_get_events_taxonomies();

	// caption for buttons - 12.05.2019.
	$button_externer_link = $add_infos_to_tec_options['fs_bezeichnung_externer_link'];
	$button_events_link   = $add_infos_to_tec_options['fs_bezeichnung_events_link'];
	$button_interner_link = $add_infos_to_tec_options['fs_bezeichnung_interner_link'];

	if ( '' !== $werte['link'] ) {
		// optionally also the link as button.
		if ( 1 === absint( $add_infos_to_tec_options['fs_alle_buttons'] ) ) {
			$fs_ausgabe .= '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . esc_url( $werte['link'] ) . ' target="_blank">' . esc_html( $button_externer_link ) . '</a></p><br>';
		} else {
			$fs_ausgabe .= '<a href=' . esc_url( $werte['link'] ) . ' target="_blank" rel="noopener noreferrer">' . esc_html( $button_externer_link ) . '</a><br>';
		}
	}

	// get font.
	$fs_schriftart_kennzeichen = ! empty( $add_infos_to_tec_options['fs_schriftart'] ) ? absint( $add_infos_to_tec_options['fs_schriftart'] ) : 0;
	$fs_schriftart_ein         = '';
	$fs_schriftart_aus         = '';
	if ( 1 === $fs_schriftart_kennzeichen ) {
		$fs_schriftart_ein = '<em>';
		$fs_schriftart_aus = '</em>';
	}
	if ( 2 === $fs_schriftart_kennzeichen ) {
		$fs_schriftart_ein = '<strong>';
		$fs_schriftart_aus = '</strong>';
	}

	/**
	 * Display the copyright of the thumbnail / feature image if the field is not empty.
	 */
	// get the thumbnail ID.
	$thumbnail_id = get_post_thumbnail_id();

	// get the excerpt only if a thumbnail id is given.
	if( $thumbnail_id > 0 ) {
		// get the attachment as object.
		$thumbnail_attachment = get_post( $thumbnail_id );

		// get the excerpt, if object could be loaded.
		if( $thumbnail_attachment instanceof WP_Post ) {
			// get the excerpt.
			$fs_copyright = $thumbnail_attachment->post_excerpt;

			// add the excerpt if it is set.
			if ( ! empty( $fs_copyright ) ) {
				$fs_ausgabe .= '<p class="fuss_button-absatz">' . $fs_schriftart_ein . $fs_copyright . $fs_schriftart_aus . '</p><br>';
			}
		}
	}

	// only internal for special use.
	if ( 'nein' !== $werte['fm'] ) {
		$fs_ausgabe .= '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . $ait_pfad . 'flohmarkt target="_blank">Weitere Flohmärkte</a></p>';
	}
	if ( 'nein' !== $werte['kfm'] ) {
		$fs_ausgabe .= '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . $ait_pfad . 'flohmarkt/Karte target="_blank">Weitere Kinderflohmärkte</a></p>';
	}
	if ( 'nein' !== $werte['ferien'] ) {
		$fs_ausgabe .= '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . $ait_pfad . 'ferien target="_blank">Weitere Ferienveranstaltungen</a></p>';
	}

	// check if TEC is installed.
	if ( ait_tec_installed() ) {
		$veranstaltungen = esc_url( tribe_get_listview_link() );
		$vergleichswert  = '';
		if ( 'nein' !== $werte['vl'] ) {
			if ( ! empty( trim( $werte['vl'] ) ) ) {
				// Space characters are replaced by "-" if necessary (security measure when entering categories that contain space characters, e.g. "nature and wood").
				$vergleichswert = $werte['vl'];

				// search in array with categories.
				$ait_key = array_search( $vergleichswert, array_column( $kategorien, 'Kategorie' ), true );

				// if value has been found.
				if ( $ait_key > -1 ) {
					// get the slug out of the associative array.
					$ait_slug = remove_accents( $kategorien[ $ait_key ]['Slug'] );

					// get path.
					$veranstaltungen = $ait_pfad . str_replace( ' ', '-', $ait_slug );

					// Space and colon behind the name, because the category appear behind it.
					$button_events_link = $button_events_link . ': ';
				} else {
					$vergleichswert = '';
				}
			}
			$fs_ausgabe .= '<p class="fuss_button-absatz"> <a class="fuss_button-beitrag" href=' . $veranstaltungen . ' target="_blank">' . $button_events_link . $vergleichswert . '</a></p>';
		}
	} else {
		// TEC not installed - may be another Event-Plugin installed (19.5.2019).
		$veranstaltungen = esc_url_raw( $add_infos_to_tec_options['fs_option_pfad'] );
		$fs_ausgabe      = $fs_ausgabe . '<p class="fuss_button-absatz"> <a class="fuss_button-beitrag" href=' . $veranstaltungen . ' target="_blank">' . $button_events_link . '</a></p>';
	}

	// internal link (can also be an external link).
	if ( ! empty( trim( $werte['il'] ) ) ) {
		$fs_ausgabe .= '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . esc_url( $werte['il'] ) . ' target="_blank">' . $button_interner_link . '</a></p>';
	}

	// output line below.
	if ( 1 === absint( $add_infos_to_tec_options['fs_linie_unten'] ) ) {
		$fs_ausgabe .= '<hr>';
	}

	// return resulting text.
	return $fs_ausgabe;
}
add_shortcode( 'fuss', 'ait_fs_beitrags_fuss_pi' );

/**
 * Check if TEC is installed.
 *
 * @return bool
 */
function ait_tec_installed(): bool {
	return function_exists( 'tribe_get_listview_link' );
}

/**
 * The Events Calendar: See all Events Categories.
 *
 * @source https://theeventscalendar.com/support/forums/topic/getting-list-of-event-categories/
 * @source https://gist.github.com/cliffordp/36d2b1f5b4f03fc0c8484ef0d4e0bbbb
 *
 * @return array
 */
function ait_cliff_get_events_taxonomies(): array {
	// bail if tribe events does not exist.
	if ( ! class_exists( 'Tribe__Events__Main' ) ) {
		return array();
	}

	// get the tribe event categories.
	$events_cats = get_terms(
		array(
			'taxonomy'   => 'tribe_events_cat',
			'parent'     => 0,
			'hide_empty' => false,
		)
	);

	// bail if term query results in error.
	if ( is_wp_error( $events_cats ) ) {
		return array();
	}

	// bail if list of cats is empty.
	if ( empty( $events_cats ) ) {
		return array();
	}

	// bail if list of event cats is not an array.
	if ( ! is_array( $events_cats ) ) {
		return array();
	}

	// array to collect the categories.
	$events_cats_names = array();
	foreach ( $events_cats as $value ) {
		// slug instead of name.
		$events_cats_names[] = array(
			'Slug'      => $value->slug,
			'Kategorie' => $value->name,
		);
	}

	// return resulting list of categories.
	return $events_cats_names;
}
add_action( 'tribe_events_before_template', 'ait_cliff_get_events_taxonomies' );

/**
 * Set default values for not existing entries in the settings.
 *
 * @param mixed $ait_options The settings array.
 * @return array
 */
function ait_set_default_settings( mixed $ait_options ): array {
	if ( ! is_array( $ait_options ) ) {
		$ait_options = array();
	}
	if ( empty( $ait_options['fs_alle_buttons'] ) ) {
		$ait_options['fs_alle_buttons'] = '0';
	}
	if ( empty( $ait_options['fs_linie_oben'] ) ) {
		$ait_options['fs_linie_oben'] = '0';
	}
	if ( empty( $ait_options['fs_linie_unten'] ) ) {
		$ait_options['fs_linie_unten'] = '0';
	}
	if ( empty( $ait_options['fs_bezeichnung_externer_link'] ) ) {
		$ait_options['fs_bezeichnung_externer_link'] = 'Read More';
	}
	if ( empty( $ait_options['fs_bezeichnung_events_link'] ) ) {
		$ait_options['fs_bezeichnung_events_link'] = 'More Events';
	}
	if ( empty( $ait_options['fs_bezeichnung_interner_link'] ) ) {
		$ait_options['fs_bezeichnung_interner_link'] = 'Read More on this website ';
	}
	if ( empty( $ait_options['fs_sortierung_categories'] ) ) {
		$ait_options['fs_sortierung_categories'] = '0';
	}

	// return resulting settings with its default values.
	return $ait_options;
}

/**
 * Add our custom menu for settings.
 *
 * @return void
 */
function ait_add_infos_to_tec_create_menu(): void {
	add_options_page(
		'Add Infos to TEC Plugin Settings',
		__( 'Add Infos to TEC Settings', 'add-infos-to-the-events-calendar' ),
		'manage_options',
		'ait_add_infos_to_tec_settings_page',
		'ait_add_infos_to_tec_settings_page'
	);
}
add_action( 'admin_menu', 'ait_add_infos_to_tec_create_menu' );

/**
 * Register our settings.
 *
 * @return void
 */
function ait_register_add_infos_to_tec_settings(): void {
	register_setting( 'add_infos_to_tec_settings-group', 'add_infos_to_tec_settings' );
}
add_action( 'admin_init', 'ait_register_add_infos_to_tec_settings' );

/**
 * Add button in tinyMCE.
 *
 * @return void
 */
function ait_button(): void {
	// bail if current user has no capabilities.
	if ( ! ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) ) {
		return;
	}

	// bail if WYSIWYG is disabled in user setting.
	if ( 'true' !== get_user_meta( get_current_user_id(), 'rich_editing', true ) ) {
		return;
	}

	// use filter to add the button.
	add_filter( 'mce_buttons', 'ait_to_tec_register_tinymce_button' );
	add_filter( 'mce_external_plugins', 'ait_to_tec_add_tinymce_button' );
}
add_action( 'admin_init', 'ait_button' );

/**
 * Add the ait button in tinyMCE.
 *
 * @param array $buttons List of buttons.
 * @return array
 */
function ait_to_tec_register_tinymce_button( array $buttons ): array {
	$buttons[] = 'ait_button';
	return $buttons;
}

/**
 * Add the JS for the air button in tinyMCE.
 *
 * @param array $plugin_array List of plugin scripts.
 * @return array
 */
function ait_to_tec_add_tinymce_button( array $plugin_array ): array {
	$plugin_array['ait_button_script'] = plugins_url( '/assets/js/ait_buttons.js', __FILE__ );
	return $plugin_array;
}

/**
 * Localization of the tinyMCE JS-script.
 *
 * @return void
 */
function ait_load_scripts(): void {
	global $pagenow;

	// bail if we are not on edit page.
	if ( ! ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) ) {
		return;
	}

	// get cats and tec marker.
	$ait_cats          = '';
	$ait_tec_installed = 'false';

	// build an array of categories only if TEC is installed.
	if ( ait_tec_installed() ) {
		$ait_cats          = ait_categories();
		$ait_tec_installed = 'true';
	}

	// register our custom script.
	wp_register_script(
		'ait',
		plugin_dir_url( __FILE__ ) . 'assets/js/ait_buttons.js',
		array(),
		AIT_VERSION,
		true
	);

	// localize the script with new data.
	wp_localize_script(
		'ait',
		'ait_php_var',
		array(
			'mouseover_title'   => __( 'Add Infos to the events calendar', 'add-infos-to-the-events-calendar' ),
			'window_title'      => __( 'Add Infos to the events calendar Shortcode Generator', 'add-infos-to-the-events-calendar' ),
			'external_link'     => __( 'Ext. Link:', 'add-infos-to-the-events-calendar' ),
			'event_category'    => __( 'Choose Event Category:', 'add-infos-to-the-events-calendar' ),
			'internal_link'     => __( 'Int. Link:', 'add-infos-to-the-events-calendar' ),
			'ait_categories'    => $ait_cats,
			'ait_tec_installed' => $ait_tec_installed,
		)
	);
	// enqueued script with localized data.
	wp_enqueue_script( 'ait' );
}
add_action( 'admin_enqueue_scripts', 'ait_load_scripts' );

/**
 * Get TEC categories as array with simple objects for each category.
 *
 * @return array
 */
function ait_categories(): array {
	// get the settings.
	$add_infos_to_tec_options = ait_set_default_settings( get_option( 'add_infos_to_tec_settings' ) ); // TODO validate via get_option.

	// define query for the categories.
	$query = array(
		'taxonomy'   => Tribe__Events__Main::TAXONOMY,
		'hide_empty' => false,
		'orderby'    => 'name',
	);

	// add sort if enabled.
	if ( 1 === absint( $add_infos_to_tec_options['fs_sortierung_categories'] ) ) {
		$query['orderby'] = 'count';
		$query['order']   = 'DESC';
	}

	// get the terms.
	$ait_terms = get_terms( $query );

	// bail on error or if list is empty.
	if ( empty( $ait_terms ) || is_wp_error( $ait_terms ) ) {
		return array();
	}

	// prepare array for the list.
	$ait_categories = array();

	// set counter to 0.
	$ait_counter = 0;

	foreach ( $ait_terms as $ait_single_term ) {
		$ait_categories[ $ait_counter ]        = new stdClass();
		$ait_categories[ $ait_counter ]->text  = get_term_field( 'name', $ait_single_term );
		$ait_categories[ $ait_counter ]->value = get_term_field( 'name', $ait_single_term );
		++$ait_counter;
	}

	// return resulting list of categories.
	return $ait_categories;
}
add_action( 'tribe_events_bar_after_template', 'ait_categories' );

/**
 * Load color chooser.
 *
 * @return void
 */
function ait_get_color_chooser(): void {
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script(
		'ait-color-chooser',
		plugins_url( 'assets/js/ait_script.js', __FILE__ ),
		array( 'jquery', 'wp-color-picker' ),
		AIT_VERSION,
		false
	);
}
add_action( 'admin_enqueue_scripts', 'ait_get_color_chooser' );

/**
 * Return path for events and suggest path if necessary.
 *
 * @return string
 */
function ait_path_for_tec(): string {
	// show default URL is TEC is not installed.
	if ( ! ait_tec_installed() ) {
		// The Events Calendar is not installed.
		return 'http://that_is_my_website/interesting_post/';
	}

	// get the list view link from tribe.
	$ait_path = tribe_get_listview_link();

	// delete last.
	$ait_path     = substr( $ait_path, 0, strlen( $ait_path ) - 1 );
	$tec_category = __( 'category', 'the-events-calendar' );
	$tec_category = strtolower( $tec_category );
	// show the path without the kind of view.
	return substr( $ait_path, 0, strrpos( $ait_path, '/' ) ) . '/' . $tec_category . '/';
}

/**
 * Add setting link in plugin list.
 *
 * @param array $links List of links.
 * @return array
 */
function ait_plugin_settings_link( array $links ): array {
	// create the URL.
	$url = add_query_arg(
		array(
			'page' => 'ait_add_infos_to_tec_settings_page',
		),
		get_admin_url() . 'options-general.php'
	);

	// add the setting link.
	$links[] = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'add-infos-to-the-events-calendar' ) . '</a>';

	// return resulting links.
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ait_plugin_settings_link' );

/**
 * Show settings page.
 *
 * @return void
 */
function ait_add_infos_to_tec_settings_page(): void {
	// check that user has proper security level.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permissions to perform this action!', 'add-infos-to-the-events-calendar' ) );
	}

	?>
		<div class="wrap">
		<h1><?php echo esc_html__( 'Add Infos to The Events Calendar', 'add-infos-to-the-events-calendar' ) . ' - ' . esc_html__( 'Version:', 'add-infos-to-the-events-calendar' ) . ' ' . esc_html( AIT_VERSION ); ?></h1>
		<hr>

		<form method="post" action="<?php echo esc_url( get_admin_url() . 'options.php' ); ?>">
			<?php
			// show the fields.
			settings_fields( 'add_infos_to_tec_settings-group' );
			do_settings_sections( 'add_infos_to_tec_settings-group' );

			// get plugin options from the database.
			$add_infos_to_tec_options = get_option( 'add_infos_to_tec_settings' );

			// set options if the options do not yet exist.
			if ( empty( $add_infos_to_tec_options ) ) {
				$add_infos_to_tec_options = array(
					'fs_option_pfad'                   => ait_path_for_tec(),
					'fs_hintergrundfarbe_button'       => '#77BCC7',
					'fs_vordergrundfarbe_button'       => '#ffffff',
					'fs_hover_hintergrundfarbe_button' => '#F9B81E',
					'fs_hover_vordergrundfarbe_button' => '#ffffff',
					'fs_runder_button'                 => '5',
					'fs_alle_buttons'                  => '0',
					'fs_schriftart'                    => '1',
					'fs_linie_oben'                    => '1',
					'fs_linie_unten'                   => '0',
					'fs_bezeichnung_externer_link'     => 'Read More',
					'fs_bezeichnung_events_link'       => 'More Events',
					'fs_bezeichnung_interner_link'     => 'Read More on this website',
					'fs_sortierung_categories'         => '1',
				);
				add_option( 'add_infos_to_tec_settings', $add_infos_to_tec_options, '', 'no' );
			}

			// generate default values for not existing entries in the settings.
			$add_infos_to_tec_options = ait_set_default_settings( $add_infos_to_tec_options );

			// output.
			?>
			<table class="form-table">
				<tr>
					<?php
					$tec_path = '';
					if ( ait_tec_installed() ) {
						// this should be the path to The Events Calendar.
						$tec_path = ait_path_for_tec();
						?>
						<th colspan="2">
						<?php
							echo esc_html__( 'This could be the path to the categories of The Events Calendar (TEC): ', 'add-infos-to-the-events-calendar' ) . '<strong style="color: #FF0000">' . esc_url( $tec_path ) . '</strong><br />';
							echo esc_html__( 'To be on the safe side, however, you should check this by going to the relevant event after using the shortcut and checking that the links are executed correctly.', 'add-infos-to-the-events-calendar' );
						?>
						</th>
						</tr>
						<tr>
							<th scope="row"><label for="fs_option_pfad"><?php echo esc_html__( 'Path e.g. categories to The Events Calendar (e.g. http://example.com/events/category/):', 'add-infos-to-the-events-calendar' ); ?></label></th>
							<?php
					} else {
						?>
						<th colspan="2">
						<?php
						echo esc_html__( 'It seems that you do not use The Events Calendar. However, you can still use this plugin and enter a URL on your website that is particularly important to you. This URL will be used instead.', 'add-infos-to-the-events-calendar' );
						?>
						</th>
						</tr>
						<tr>
								<th scope="row"><label for="fs_option_pfad"><?php echo esc_html__( 'Enter a URL on your website that is particularly important to you:', 'add-infos-to-the-events-calendar' ); ?></label></th>
							<?php
					}
					?>
					<td><input type="text" name="add_infos_to_tec_settings[fs_option_pfad]" class="widefat" id="fs_option_pfad" value="<?php echo esc_url_raw( $add_infos_to_tec_options['fs_option_pfad'] ); ?>" /></td>
				</tr>

				<tr>
					<th colspan="2"><h3><?php echo esc_html__( 'Settings for Buttons:', 'add-infos-to-the-events-calendar' ); ?></h3></th>
				</tr>
				<tr>
					<th><label for="fs_hintergrundfarbe_button"><?php echo esc_html__( 'Background color:', 'add-infos-to-the-events-calendar' ); ?></label></th>
					<td><input type="text" id="fs_hintergrundfarbe_button" name="add_infos_to_tec_settings[fs_hintergrundfarbe_button]" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_hintergrundfarbe_button'] ); ?>" class="color" /></td>
				</tr>
				<tr>
					<th><label for="fs_vordergrundfarbe_button"><?php echo esc_html__( 'Font color:', 'add-infos-to-the-events-calendar' ); ?></label></th>
					<td><input type="text" id="fs_vordergrundfarbe_button" name="add_infos_to_tec_settings[fs_vordergrundfarbe_button]" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_vordergrundfarbe_button'] ); ?>" class="color" /></td>
				</tr>

				<tr>
					<th><label for="fs_hover_hintergrundfarbe_button"><?php echo esc_html__( 'Background color when driving over the button (Hover):', 'add-infos-to-the-events-calendar' ); ?></label></th>
					<td><input type="text" id="fs_hover_hintergrundfarbe_button" name="add_infos_to_tec_settings[fs_hover_hintergrundfarbe_button]" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_hover_hintergrundfarbe_button'] ); ?>" class="color" /></td>
				</tr>

				<tr>
					<th><label for="fs_hover_vordergrundfarbe_button"><?php echo esc_html__( 'Font color when driving over the button (Hover):', 'add-infos-to-the-events-calendar' ); ?></label></th>
					<td><input type="text" id="fs_hover_vordergrundfarbe_button" name="add_infos_to_tec_settings[fs_hover_vordergrundfarbe_button]" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_hover_vordergrundfarbe_button'] ); ?>" class="color" /></td>
				</tr>

				<tr>
					<th><label for="fs_bezeichnung_externer_link"><?php echo esc_html__( 'Description for external link:', 'add-infos-to-the-events-calendar' ); ?></label></th>
					<td><input type="text" id="fs_bezeichnung_externer_link" name="add_infos_to_tec_settings[fs_bezeichnung_externer_link]" class="widefat" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_bezeichnung_externer_link'] ); ?>" /></td>
				</tr>

				<tr>
					<?php
					// The Events Calendar installed.
					if ( ! empty( $tec_path ) ) {
						?>
							<th><?php echo esc_html__( 'Description for events:', 'add-infos-to-the-events-calendar' ); ?></th>
						<?php
					} else {
						?>
							<th><?php echo esc_html__( 'Description for particularly link:', 'add-infos-to-the-events-calendar' ); ?></th>
						<?php
					}
					?>
					<td><input type="text" name="add_infos_to_tec_settings[fs_bezeichnung_events_link]" class="widefat" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_bezeichnung_events_link'] ); ?>" /></td>
				</tr>

				<tr>
					<th><label for="fs_bezeichnung_interner_link"><?php echo esc_html__( 'Description for internal link:', 'add-infos-to-the-events-calendar' ); ?></label></th>
					<td><input type="text" id="fs_bezeichnung_interner_link" name="add_infos_to_tec_settings[fs_bezeichnung_interner_link]" class="widefat" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_bezeichnung_interner_link'] ); ?>" /></td>
				</tr>

				<tr>
					<th><h3><?php echo esc_html__( 'Setting for design on website:', 'add-infos-to-the-events-calendar' ); ?></h3></th>
				</tr>

				<tr>
					<th><label for="fs_runder_button"><?php echo esc_html__( 'Rounded corners (values from 0 - 30):', 'add-infos-to-the-events-calendar' ); ?></label></th>
					<td><input type="number" id="fs_runder_button" min="0" max="30" step="1" name="add_infos_to_tec_settings[fs_runder_button]" size=2 value="<?php echo esc_attr( $add_infos_to_tec_options['fs_runder_button'] ); ?>" /></td>
				</tr>


				<tr>
					<th><label for="fs_alle_buttons"><?php echo esc_html__( 'All links as buttons:', 'add-infos-to-the-events-calendar' ); ?></label></th>
					<td><input type="checkbox" id="fs_alle_buttons" name="add_infos_to_tec_settings[fs_alle_buttons]" value="1" <?php echo esc_attr( checked( $add_infos_to_tec_options['fs_alle_buttons'], 1, true ) ); ?> />
				</tr>

				<tr>
					<th><label for="fs_schriftart"><?php echo esc_html__( 'Font for Copyright:', 'add-infos-to-the-events-calendar' ); ?></label></th>
					<td><input type="radio" id="fs_schriftart" name="add_infos_to_tec_settings[fs_schriftart]" value="1" <?php echo esc_attr( checked( 1, $add_infos_to_tec_options['fs_schriftart'], true ) ); ?>><?php echo esc_html__( 'italic', 'add-infos-to-the-events-calendar' ); ?>
						<input type="radio" name="add_infos_to_tec_settings[fs_schriftart]" value="2" <?php echo esc_attr( checked( 2, $add_infos_to_tec_options['fs_schriftart'], true ) ); ?>><?php echo esc_html__( 'bold', 'add-infos-to-the-events-calendar' ); ?>
						<input type="radio" name="add_infos_to_tec_settings[fs_schriftart]" value="3" <?php echo esc_attr( checked( 3, $add_infos_to_tec_options['fs_schriftart'], true ) ); ?>><?php echo esc_html__( 'normal', 'add-infos-to-the-events-calendar' ); ?>
					</td>
				</tr>

				<tr>
					<th><label for="fs_linie_oben"><?php echo esc_html__( 'Horizontal line above the block:', 'add-infos-to-the-events-calendar' ); ?></label></th>
					<td><input type="checkbox" id="fs_linie_oben" name="add_infos_to_tec_settings[fs_linie_oben]" value="1" <?php echo esc_attr( checked( $add_infos_to_tec_options['fs_linie_oben'], 1, true ) ); ?> />
				</tr>

				<tr>
					<th><label for="fs_linie_unten"><?php echo esc_html__( 'Horizontal line below the block:', 'add-infos-to-the-events-calendar' ); ?></label></th>
					<td><input type="checkbox" id="fs_linie_unten" name="add_infos_to_tec_settings[fs_linie_unten]" value="1" <?php echo esc_attr( checked( $add_infos_to_tec_options['fs_linie_unten'], 1, true ) ); ?> />
				</tr>

				<?php
					// The Events Calendar installed.
				if ( ! empty( $tec_path ) ) {
					?>
							<tr>
								<th colspan="2"><h3><?php echo esc_html__( 'Further Settings:', 'add-infos-to-the-events-calendar' ); ?></h3></th>
							</tr>

							<tr>
								<th><label for="fs_sortierung_categories"><?php echo esc_html__( 'Sorting categories by occurrence (Frequently selected categories first):', 'add-infos-to-the-events-calendar' ); ?></label></th>
								<td><input type="checkbox" id="fs_sortierung_categories" name="add_infos_to_tec_settings[fs_sortierung_categories]" value="1" <?php echo esc_attr( checked( $add_infos_to_tec_options['fs_sortierung_categories'], 1, true ) ); ?> />
							</tr>
						<?php
				}
				?>
			</table>
		<?php
		submit_button();
		?>
	</form>
</div>
	<?php
}
