<?php
/**
 * Plugin Name: Add infos to the events calendar
 * Description: Provides a shortcode block (image copyright, button with link to events with a special category, link to the website of the organizer) in particular to single events for The Events Calendar Free Plugin (by MODERN TRIBE)
 * Version:     1.4
 * Author:      Hans-Gerd Gerhards (haurand.com)
 * Author URI:  https://haurand.com
 * Plugin URI:  https://haurand.com/add-infos-to-the-events-calendar/
 * Text Domain: add-infos-to-the-events-calendar
 * Domain Path: /languages
 * License:     GPL2

* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 2 of the License, or
* (at your option) any later version.

* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.

* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.

**************************************************************************/



define("AIT_VERSION", "1.4");
define("AIT_HTTP", "http://");
define("AIT_HTTPS", "https://");
define('AIT__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
global $ait_add_options;

// Securing against unauthorized access //
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// load admin-functions //
require_once(AIT__PLUGIN_DIR . 'ait_admin_functions.php');


// Load language files
function ait_meine_textdomain_laden() {
	load_plugin_textdomain(
	'add-infos-to-the-events-calendar',
	false,
	basename( dirname( __FILE__ ) ) . '/languages'
	);
}
add_action('plugins_loaded','ait_meine_textdomain_laden');




/*----------------------------------------------------------------*/
// Start: get the color settings from style_fuss.css
// for the design of the buttons
/*----------------------------------------------------------------*/

function ait_fs_style_fuss_plugin_scripts() {
		// Include CSS file:
		$script = plugin_dir_url( __FILE__ ) . 'assets/css/ait_style_fuss.css';
		wp_enqueue_style( 'custom_style',  $script);

		// Variables for button design
		$add_infos_to_tec_options = get_option( 'add_infos_to_tec_settings' );
		$button_hintergrund = esc_attr( $add_infos_to_tec_options['fs_hintergrundfarbe_button']);
		$button_vordergrund = esc_attr( $add_infos_to_tec_options['fs_vordergrundfarbe_button']);
		$button_hover_hintergrund = esc_attr( $add_infos_to_tec_options['fs_hover_hintergrundfarbe_button']);
		$button_hover_vordergrund = esc_attr( $add_infos_to_tec_options['fs_hover_vordergrundfarbe_button']);
		$button_rund = esc_attr( $add_infos_to_tec_options['fs_runder_button']);
		$custom_css= "
			a.fuss_button-beitrag {
			    color: {$button_vordergrund}!important;
			    background-color: {$button_hintergrund}!important;
					text-decoration: none!important;
					border-radius: {$button_rund}px;
			}
			a.fuss_button-beitrag:hover{
			  color: {$button_hover_vordergrund}!important;
			  background-color: {$button_hover_hintergrund}!important;
				text-decoration: none!important;
			}";
		wp_add_inline_style( 'custom_style', $custom_css );
    // not used: wp_enqueue_script( 'style_fuss' ); //
}
add_action( 'wp_enqueue_scripts', 'ait_fs_style_fuss_plugin_scripts' );

/*----------------------------------------------------------------*/
// End: get the color settings from style_fuss.css
// for the design of the buttons
/*----------------------------------------------------------------*/


/*----------------------------------------------------------------*/
/* Start: shortcodes for footer at the single event
/*----------------------------------------------------------------*/

// Automatically displays the text from "Caption" in italics by default for an event or a post.
// Call Examples:
// [fuss link="https://externer_link.de" vl=""] --> always shows picture credits, then more info with the link to external website and at vl="" the link to "more events".
// [fuss vl=""] --> always shows picture credits, but no link to external website and at vl="" the link to "more events".
// vl = list of events
// [fuss] --> always shows picture credits, but no link to external website.
// [fuss link="https://externer_link.de" vl="nature"] --> always shows picture credits, then more info with the link to external website and at vl="Nature" the link to "more events: nature".
// (of course the category must exist in The Events Calendar (this is checked by a function). If the category does not exist, the event list will be shown.)
// [fuss vl="" il="http://internal_link.de/example"] --> always shows picture credits, but no link to external website and at vl="" the link to "more events" and at il="http://internal_link.de/example" the link to another external or internal webesite.
// internal used: fm, kfm, ferien

function ait_fs_beitrags_fuss_pi($atts) {
  	$werte = shortcode_atts( array(
  	  'link' => '',
			'fm' => 'nein',
			'kfm' => 'nein',
			'ferien' => 'nein',
      'vl' => 'nein',
      'il' => '',
  	  ), $atts);
    $fs_ausgabe = '';
		//
		// Output line above //
		//
		$add_infos_to_tec_options = get_option( 'add_infos_to_tec_settings' );
		// workaround to prevent the item from not being created (checked) and create a notice, if WP_DEBUG is true:
		$add_infos_to_tec_options = ait_test_array($add_infos_to_tec_options);
		$fs_l_o = esc_attr($add_infos_to_tec_options['fs_linie_oben']);
		if (esc_attr($add_infos_to_tec_options['fs_linie_oben']) == '1') {
			  // echo 'Linie oben: ' . var_dump($l_o); //
				$fs_ausgabe = $fs_ausgabe . '<hr>';
				// echo 'Ausgabe: ' . var_dump($fs_ausgabe); //
		}
		//
		// linking
		//
		// Get path from the settings: //
		$ait_pfad = esc_url_raw( $add_infos_to_tec_options['fs_option_pfad']);
		// Save file path
		// Categories used by TEC
    $kategorien = ait_cliff_get_events_taxonomies();

		// caption for buttons - 12.05.2019://
		$button_externer_link = trim(esc_attr( $add_infos_to_tec_options['fs_bezeichnung_externer_link']));
		$button_events_link = trim(esc_attr( $add_infos_to_tec_options['fs_bezeichnung_events_link']));
		$button_interner_link = trim(esc_attr( $add_infos_to_tec_options['fs_bezeichnung_interner_link']));

		// var_dump($kategorien); //
    if ( trim($werte['link']) != '') {
			// optionally also the link as button:
			if (esc_attr($add_infos_to_tec_options['fs_alle_buttons']) == 1){
				$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"> <a class="fuss_button-beitrag" href=' . ait_check_http_https($werte['link']) . ' target="_blank">' . $button_externer_link . '</a></p><br>';
			} else {
				$fs_ausgabe = $fs_ausgabe . '<a href=' . ait_check_http_https($werte['link']) . ' target="_blank">'. $button_externer_link . '</a><br>';
			}
		}
		//
		// font
		//
		$fs_schriftart_kennzeichen =  esc_attr($add_infos_to_tec_options['fs_schriftart']);
		$fs_schriftart_ein = '';
		$fs_schriftart_aus = '';
		if ($fs_schriftart_kennzeichen == 1) {
				// echo 'kursiv'; //
        $fs_schriftart_ein = '<em>';
				$fs_schriftart_aus = '</em>';
		}
		if ($fs_schriftart_kennzeichen == 2) {
				// echo 'fett'; //
				$fs_schriftart_ein = '<strong>';
				$fs_schriftart_aus = '</strong>';
		}
		//
		// Display the copyright if the field is not empty //
		//
		$fs_copyright = get_post(get_post_thumbnail_id())->post_excerpt;
		if ( $fs_copyright != '' ) {
    		$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz">' . $fs_schriftart_ein  .  $fs_copyright . $fs_schriftart_aus . '</p><br>';
		}


		// only internal for special use //
		if ( $werte['fm'] != 'nein' ) {
			// $fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . $ait_pfad . 'flohmarkt target="_blank">'. __( 'More Events: flea markets', 'add-infos-to-the-events-calendar' ) . '</a></p>';
			$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . $ait_pfad . 'flohmarkt target="_blank">' . 'Weitere Flohmärkte' . '</a></p>';
		}
		if ( $werte['kfm'] != 'nein' ) {
			$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . $ait_pfad . 'flohmarkt/Karte target="_blank">' . 'Weitere Kinderflohmärkte' . '</a></p>';
		}
		if ( $werte['ferien'] != 'nein' ) {
			$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . $ait_pfad . 'ferien target="_blank">' . 'Weitere Ferienveranstaltungen' . '</a></p>';
		}
		// only internal for special use //

		//
		// Events with category
		//

		// Preset variables (path to event list and without category) //
		// no real category, so it should be the path to all events: //
		// fixed: 1.02 - check if TEC is installed (18.5.2019) //
		if ( ait_tec_installed() ) {
			$veranstaltungen = esc_url( tribe_get_listview_link() );
			$vergleichswert = '';
	    if ( $werte['vl'] != 'nein' ) {
		      if ( trim($werte['vl']) != '') {
		        /* Space characters are replaced by "-" if necessary (security measure when entering categories that contain space characters, e.g. "nature and wood"). */
		        $vergleichswert = $werte['vl'];
						// Set value for $ait_key to -1, so that you can query later whether the value has changed.
						$ait_key = -1;
						// search in array with categories
						$ait_key = array_search($vergleichswert, array_column($kategorien, 'Kategorie'));
						// $ait_key = ait_searchForId($vergleichswert, $kategorien); //
						// echo 'Key: ' . $ait_key . "\n"; //
						// if the comparison value is contained in the array of categories - found, then value is greater -1 //
		        if ($ait_key > -1 ){
							// Get the slug out of the associative array.
							$ait_slug = $kategorien[$ait_key]['Slug']; //
							// Replace special characters //
		          $ait_slug = ait_fs_sonderzeichen ($ait_slug);
		          $veranstaltungen = $ait_pfad . str_replace(" ", "-", $ait_slug); //
							// Space and colon behind the name, because the category appear behind it.
							$button_events_link = $button_events_link . ': ';
							// after description - option for buttons not used any more, 12.5.2019
		          // $vergleichswert = ': ' . $vergleichswert . ''; //
		          }
		        else {
		          $vergleichswert = '';
		          }
		      }
					$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"> <a class="fuss_button-beitrag" href=' . $veranstaltungen . ' target="_blank">'. $button_events_link . $vergleichswert . '</a></p>';
				}
	}
	// TEC not installed - may be another Event-Plugin installed (19.5.2019) //
	else {
		$veranstaltungen = esc_url_raw( $add_infos_to_tec_options['fs_option_pfad']);
		$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"> <a class="fuss_button-beitrag" href=' . $veranstaltungen . ' target="_blank">'. $button_events_link . '</a></p>';
	}
	//
	// Internal link (can also be an external link)
	//
  if ( trim($werte['il']) != '') {
		$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . ait_check_http_https($werte['il']) . ' target="_blank">' . $button_interner_link . '</a></p>';
  }
	//
	// Output line below //
	//
	if (esc_attr($add_infos_to_tec_options['fs_linie_unten']) == 1) {
			$fs_ausgabe = $fs_ausgabe . '<hr>';
	}
	return $fs_ausgabe;
}
add_shortcode('fuss', 'ait_fs_beitrags_fuss_pi');




/*----------------------------------------------------------------*/
/* Ende: shortcodes for footer at the single event
/*----------------------------------------------------------------*/


// fixed: 1.02 - check if TEC is installed (18.5.2019) //
function ait_tec_installed() {
	$tec_installed = TRUE;
	if ( ! function_exists( 'tribe_get_listview_link' ) ) {
		$tec_installed = FALSE;
	}
	// var_dump ($tec_installed); //
	return $tec_installed;
}


/**
  * The Events Calendar: See all Events Categories - var_dump at top of Events archive page
  * Screenshot: https://cl.ly/0Q0B1D0g2a43
  *
  * for https://theeventscalendar.com/support/forums/topic/getting-list-of-event-categories/
  *
  * From https://gist.github.com/cliffordp/36d2b1f5b4f03fc0c8484ef0d4e0bbbb
  */
add_action( 'tribe_events_before_template', 'ait_cliff_get_events_taxonomies' );
function ait_cliff_get_events_taxonomies(){
	if( ! class_exists( 'Tribe__Events__Main' ) ) {
		return false;
	}

	$tecmain = Tribe__Events__Main::instance();

	// https://developer.wordpress.org/reference/functions/get_terms/
	$cat_args = array(
		'hide_empty' => true,
	);
	// see here: https://theeventscalendar.com/support/forums/topic/get_terms-with-tribe_events_cat-returning-wp_error-in-functions-php/
	$events_cats = get_terms(array(
		'taxonomy' => 'tribe_events_cat',
		'parent' => 0,
		'hide_empty' => false
	));
	// $events_cats = get_terms( $tecmain::TAXONOMY, $cat_args ); // hat hier nicht funktioniert
	/* $events_cats_names = array(); */
	if( ! is_wp_error( $events_cats ) && ! empty( $events_cats ) && is_array( $events_cats) ) {
		$events_cats_names = array();
		foreach( $events_cats as $key => $value ) {
			// slug instead of name
			$events_cats_names[] = array ('Slug' => $value->slug,
																		'Kategorie' => $value->name);
			// $events_cats_names[] = $value->name; //
		}
	}
	return $events_cats_names;
}

/*----------------------------------------------------------------*/
// Convert German Umlaute, so that e.g.  Führung in Fuehrung
// otherwise the category list will not be found.
/*----------------------------------------------------------------*/
function ait_fs_sonderzeichen($string)
{
   $string = str_replace("ä", "ae", $string);
   $string = str_replace("ü", "ue", $string);
   $string = str_replace("ö", "oe", $string);
   $string = str_replace("Ä", "Ae", $string);
   $string = str_replace("Ü", "Ue", $string);
   $string = str_replace("Ö", "Oe", $string);
   $string = str_replace("ß", "ss", $string);
   $string = str_replace("´", "", $string);
return $string;
}

/*----------------------------------------------------------------*/
// Automatically adds http:// to a URL before the link,
// if that is missing at the time of entry
// 14.5.2019
/*----------------------------------------------------------------*/
function ait_check_http_https($string)
{
	if ((substr($string, 0, 7) != AIT_HTTP) && (substr($string, 0, 8) != AIT_HTTPS)){
		$string = 'http://' . $string;
	}
return $string;
}


// hooks and filters
$shortcodes = array( 'fuss_pi'); // add shortcode triggers to array
foreach( $shortcodes as $shortcode ) add_shortcode( $shortcode, 'ait_fs_beitrags_fuss_pi' ); // create shortcode for each item in $shortcodes



// -------------------------------------------------- //
// Start: Add new Dashboard-Widget
// -------------------------------------------------- //
/* not used yet
function ait_fs_add_dashboard_widget() {
  wp_add_dashboard_widget(
    'mein_dashboard_widget',
    __('Dashboard-Widget for "Add infos to the events calendar" - Plugin', 'add-infos-to-the-events-calendar'),
    'fs_dashboard_widget_html'
    );
  }

add_action(
  'wp_dashboard_setup',
  'ait_fs_add_dashboard_widget'
  );
  // Ausgabe des Inhaltes des Dashboard-Widgets
  function fs_dashboard_widget_html($post,$callback_args){
    esc_html_e(
    __('First Dashboard-Widget for "Add infos to the events calendar" - Plugin', 'add-infos-to-the-events-calendar'),
    'add-infos-to-the-events-calendar'
    );
  }
*/

// -------------------------------------------------- //
// Ende: Add new Dashboard-Widget
// -------------------------------------------------- //


// workaround to prevent the item from not being created (option checked()) - would create a notice, if WP_DEBUG is true
	function ait_test_array($ait_options) {
		if (empty( $ait_options['fs_alle_buttons'])) {
			 $ait_options['fs_alle_buttons'] = 0;
		}
		if (empty( $ait_options['fs_linie_oben'])) {
			 $ait_options['fs_linie_oben'] = 0;
		}
		if (empty( $ait_options['fs_linie_unten'])) {
			 $ait_options['fs_linie_unten'] = 0;
		}
		if (empty( $ait_options['fs_bezeichnung_externer_link'])) {
			 $ait_options['fs_bezeichnung_externer_link'] = 'Read More';
		}
		if (empty( $ait_options['fs_bezeichnung_events_link'])) {
			 $ait_options['fs_bezeichnung_events_link'] = 'More Events';
		}
		if (empty( $ait_options['fs_bezeichnung_interner_link'])) {
			 $ait_options['fs_bezeichnung_interner_link'] = 'Read More on this website ';
		}

		return $ait_options;
	}

	add_action('admin_menu', 'ait_add_infos_to_tec_create_menu');

// create custom plugin settings menu
	function ait_add_infos_to_tec_create_menu() {

		//create new top-level menu: add_menu_page
		add_submenu_page('Add Infos to TEC Plugin Settings',  __('Add Infos to TEC Settings', 'add-infos-to-the-events-calendar'), 'administrator', __FILE__, 'ait_add_infos_to_tec_settings_page' , plugins_url('/images/icon.png', __FILE__) );
		add_options_page( 'Add Infos to TEC Plugin Settings',  __('Add Infos to TEC Settings', 'add-infos-to-the-events-calendar'), 'manage_options', 'ait_add_infos_to_tec_settings_page', 'ait_add_infos_to_tec_settings_page');
		//call register settings function
		add_action( 'admin_init', 'ait_register_add_infos_to_tec_settings' );
		/*
		if (! isset( $_POST['ait_tec'] )	|| ! wp_verify_nonce( $_POST['ait_tec'],	'ait_plugin_settings_link' )) {
				print 'Sorry, Nonce ist nicht korrekt.';
				exit;
		}
		*/
}

// Settings in the Plugin List
	function ait_plugin_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=ait_add_infos_to_tec_settings_page">'	. __( 'Settings' ) . '</a>';
		// check_admin_referer( 'ait_plugin_settings_link', 'ait_tec' );
		array_push( $links, $settings_link );
		return $links;
	}
	add_filter(
		'plugin_action_links_' . plugin_basename( __FILE__ ),	'ait_plugin_settings_link'
	);

//register our settings
	function ait_register_add_infos_to_tec_settings() {
		register_setting( 'add_infos_to_tec_settings-group', 'add_infos_to_tec_settings' );
	}



	/* Integrate shortcode generator in tinymce editor */

	add_action( 'admin_init', 'ait_button' );

	function ait_button() {
	     if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
				 // check if WYSIWYG is enabled //
				 if ( get_user_option('rich_editing') == 'true') {
					 // https://codex.wordpress.org/Plugin_API/Filter_Reference/mce_buttons,_mce_buttons_2,_mce_buttons_3,_mce_buttons_4 //
					 // sobald ich das plugin "My TinyMCE Buttons" aktiviere, ist der JS-Fehler verschwunden, aber das Icon wird trotzdem nicht angezeigt.
	          add_filter( 'mce_buttons', 'ait_to_tec_register_tinymce_button' );
	          add_filter( 'mce_external_plugins', 'ait_to_tec_add_tinymce_button' );
					}
	     }
	}


	function ait_to_tec_register_tinymce_button( $buttons ) {
	     array_push( $buttons, "ait_button");
	     return $buttons;
	}

/* this function causes the error */
	function ait_to_tec_add_tinymce_button( $plugin_array ) {
	     $plugin_array['my_button_script'] = plugins_url( '/assets/js/ait_buttons.js', __FILE__ ) ;
	     return $plugin_array;
	}



	/* localization for ait_buttons.js */
	add_action( 'wp_enqueue_scripts', 'ait_load_scripts' );
	function ait_load_scripts() {
				global $ait_add_options;
				$ait_pfad = plugin_dir_url( __FILE__ ) . '/assets/js/ait_buttons.js';
				// Register the script
				wp_register_script('ait_js_script',	$ait_pfad );
				// Enqueued script with localized data.
				wp_enqueue_script( 'ait_js_script', $ait_pfad );
				// wp_enqueue_script( 'ait_js_script' ); //
				// array with new data
				$ait_add_options = array(
					'external_link' => __( 'Ext. Link', 'add-infos-to-the-events-calendar' ),
					'event_category' => __( 'Event Category', 'add-infos-to-the-events-calendar' ),
					'internal_link' => __( 'Int. Link', 'add-infos-to-the-events-calendar' ),
					'ackids' => 'here',
 			  );
				// Localize the script with new data
				wp_localize_script( 'ait_js_script', 'ait_php_var_js', $ait_add_options );
				// wird im Frontend gezeigt:
				var_dump ($ait_add_options);

			}
?>
