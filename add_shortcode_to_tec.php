<?php
/**
 * Plugin Name: Add infos to the events calendar
 * Description: provides a shortcode block to single events (TEC)
 * Version:     0.5
 * Author:      https://haurand.com
 * Author URI:  https://haurand.com
 * Plugin URI:  https://haurand.com/plugins/add_infos_tec
 * Text Domain: add_infos_to_tec
 * Domain Path: /languages
 * License:     GPL2
 */


/*
Shortcode:
	[fuss]

Optional Arguments:

Examples:

*/

// Securing against unauthorized access //
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Load language files - currently still switched off
/*
function meine_textdomain_laden() {
	load_plugin_textdomain(
	'text-domain',
	false,
	basename( dirname( __FILE__ ) ) . '/languages'
	);
}
add_action('plugins_loaded','meine_textdomain_laden');
*/


// $retrieved_nonce = $_REQUEST['_wpnonce'];
// if (!wp_verify_nonce($retrieved_nonce, 'delete_my_action' ) ) die( 'Failed security check' );
// variables
	$ausgabe = '
<!--
Plugin: Add Infos to TEC
Plugin URI: https://haurand.com
-->
';
/*----------------------------------------------------------------*/
// Start: get the color settings from style_fuss.css
// for the design of the buttons
/*----------------------------------------------------------------*/

function fs_style_fuss_plugin_scripts() {
		// Include CSS file:
		$script = plugin_dir_url( __FILE__ ) . 'assets/css/style_fuss.css';
		wp_enqueue_style( 'custom_style',  $script);

		// Variables for button design
		$button_hintergrund = esc_attr( get_option('fs_hintergrundfarbe_button') );
		$button_vordergrund = esc_attr( get_option('fs_vordergrundfarbe_button') );
		$button_hover_hintergrund = esc_attr( get_option('fs_hover_hintergrundfarbe_button') );
		$button_hover_vordergrund = esc_attr( get_option('fs_hover_vordergrundfarbe_button') );
		$button_rund = esc_attr( get_option('fs_runder_button') );

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
    // wp_enqueue_script( 'style_fuss' ); //
}
add_action( 'wp_enqueue_scripts', 'fs_style_fuss_plugin_scripts' );

/*----------------------------------------------------------------*/
// Start: get the color settings from style_fuss.css
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

function fs_beitrags_fuss_pi($atts) {
  	$werte = shortcode_atts( array(
  	  'link' => '',
      'vl' => 'nein',
      'il' => '',
  	  ), $atts);
    $fs_ausgabe = '';
		//
		// Output line above //
		//
		$fs_l_o = esc_attr(get_option('fs_linie_oben '));
		if (esc_attr(get_option('fs_linie_oben ')) == '1') {
			  // echo 'Linie oben: ' . var_dump($l_o); //
				$fs_ausgabe = $fs_ausgabe . '<hr>';
				// echo 'Ausgabe: ' . var_dump($fs_ausgabe); //
		}
		//
		// linking
		//
		// Get path from the settings: //
		$veranstaltungen = esc_url_raw( get_option('fs_option_pfad') );
		// Save file path
		// Categories used by TEC
    $kategorien = cliff_get_events_taxonomies();
    if ( trim($werte['link']) != '') {
			// optionally also the link as button:
			if (esc_attr(get_option('fs_alle_buttons')) == 1){
				$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"> <a class="fuss_button-beitrag" href=' . $werte['link'] . ' target="_blank">Read more</a></p><br>';
				/* Example for language file:
			  $fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"> <a class="fuss_button-beitrag" href=' . $werte['link'] . ' target="_blank">' . __( 'Read more', 'text-domain' ) . '</a></p><br>';
				*/
			} else {
      	$fs_ausgabe = $fs_ausgabe . '<a href=' . $werte['link'] . ' target="_blank">Read more</a><br>';
			}
		}
		//
		// font
		//
		$fs_schriftart_kennzeichen =  esc_attr(get_option('fs_schriftart'));
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
		// Display of the copyright //
		//
    $fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz">' . $fs_schriftart_ein  . get_post(get_post_thumbnail_id())->post_excerpt . $fs_schriftart_aus . '</p><br>';
		//
		// Events with category
		//
		if ($veranstaltungen != 'Achtung') {
	    if ( $werte['vl'] != 'nein' ) {
	      if ( trim($werte['vl']) != '') {
	        /* Space characters are replaced by "-" if necessary (security measure when entering categories that contain space characters, e.g. "nature and wood"). */
	        $vergleichswert = $werte['vl'];
	        /* if the comparison value is contained in the array of categories: */
	        if (in_array($vergleichswert, $kategorien )){
	          /* Replace special characters */
	          $werte['vl'] = fs_sonderzeichen ($werte['vl']);
	          $veranstaltungen = $veranstaltungen . str_replace(" ", "-", $werte['vl']);
	          $vergleichswert = ': ' . $vergleichswert . '';
	          }
	        else {
	          $vergleichswert = '';
	          }
	      }
				$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"> <a class="fuss_button-beitrag" href=' . $veranstaltungen . ' target="_blank">More Events' . $vergleichswert . '</a></p>';
			}
		else {
			// URL is incorrect !
			$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz">' . $veranstaltungen . '</p>';
		}
	}
	//
	// Internal link (can also be an external link)
	//
  if ( trim($werte['il']) != '') {
     $fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . $werte['il'] . ' target="_blank">Read more on this website</a></p>';
  }
	//
	// Output line below //
	//
	if (esc_attr(get_option('fs_linie_unten')) == 1) {
			$fs_ausgabe = $fs_ausgabe . '<hr>';
	}
	return $fs_ausgabe;
}
add_shortcode('fuss', 'fs_beitrags_fuss_pi');



/*----------------------------------------------------------------*/
/* Ende: shortcodes for footer at the single event
/*----------------------------------------------------------------*/


/**
  * The Events Calendar: See all Events Categories - var_dump at top of Events archive page
  * Screenshot: https://cl.ly/0Q0B1D0g2a43
  *
  * for https://theeventscalendar.com/support/forums/topic/getting-list-of-event-categories/
  *
  * From https://gist.github.com/cliffordp/36d2b1f5b4f03fc0c8484ef0d4e0bbbb
  */
add_action( 'tribe_events_before_template', 'cliff_get_events_taxonomies' );
function cliff_get_events_taxonomies(){
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
			$events_cats_names[] = $value->name;
		}
	}
	return $events_cats_names;
}

/*----------------------------------------------------------------*/
// Convert German Umlaute, so that e.g.  Führung in Fuehrung
// otherwise the category list will not be found.
/*----------------------------------------------------------------*/
function fs_sonderzeichen($string)
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


// hooks and filters
$shortcodes = array( 'fuss_pi'); // add shortcode triggers to array
foreach( $shortcodes as $shortcode ) add_shortcode( $shortcode, 'fs_beitrags_fuss_pi' ); // create shortcode for each item in $shortcodes



// -------------------------------------------------- //
// Start: Add new Dashboard-Widget
// -------------------------------------------------- //
function fs_add_dashboard_widget() {
  wp_add_dashboard_widget(
    'mein_dashboard_widget',
    __('Dashboard-Widget for "Add infos to the events calendar" - Plugin',
    'textdomaine'
    ),
    'fs_dashboard_widget_html'
    );
  }

add_action(
  'wp_dashboard_setup',
  'fs_add_dashboard_widget'
  );
  // Ausgabe des Inhaltes des Dashboard-Widgets
  function fs_dashboard_widget_html($post,$callback_args){
    esc_html_e(
    'First Dashboard-Widget for "Add infos to the events calendar" - Plugin :-)',
    'textdomaine'
    );
  }

// -------------------------------------------------- //
// Ende: Add new Dashboard-Widget
// -------------------------------------------------- //

// -------------------------------------------------- //
// Start: admin area
// -------------------------------------------------- //

	// create custom plugin settings menu

// WP Color Picker
	add_action( 'admin_enqueue_scripts', 'farbwaehler_laden' );
	function farbwaehler_laden( $hook ) {
	    wp_enqueue_style( 'wp-color-picker' );
	    wp_enqueue_script(
	        'color-script',
	        plugins_url( 'assets/js/script.js', __FILE__ ),
	        array( 'wp-color-picker' ),
	        false,
	        true
	    );
	}


	add_action('admin_menu', 'add_infos_to_tec_create_menu');

	function add_infos_to_tec_create_menu() {

		//create new top-level menu: add_menu_page
		add_submenu_page('Add Infos to TEC Plugin Settings', 'Add Infos to TEC Settings', 'administrator', __FILE__, 'add_infos_to_tec_settings_page' , plugins_url('/images/icon.png', __FILE__) );
		add_options_page( 'Add Infos to TEC Plugin Settings', 'Add Infos to TEC Settings', 'manage_options', 'add_infos_to_tec_settings_page', 'add_infos_to_tec_settings_page');
		//call register settings function
		add_action( 'admin_init', 'register_add_infos_to_tec_settings' );
		// check nonce - not ready
		/*
		if ( ! isset( $_POST['name_of_nonce_field'] ) || ! wp_verify_nonce( $_POST['ps_feld'], 'add_infos_to_tec_formular' )
		) {
   		print 'Sorry, your nonce did not verify.';
   		exit;
		} else {
			print 'Anything ok.';
		}
		*/
		// if ( ! empty( $_POST ) &&  check_admin_referer(  'add_infos_to_tec_formular', 'ps_feld' ) ) {
		// 		echo  'Fehler !!!';
		// }

}

	// Settings in the Plugin List
	function plugin_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=add_infos_to_tec_settings_page">'	. __( 'Settings' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}
	add_filter(
		'plugin_action_links_' . plugin_basename( __FILE__ ),	'plugin_settings_link'
	);

	function register_add_infos_to_tec_settings() {
		//register our settings
		register_setting( 'event-shortcode-settings-group', 'fs_option_pfad' );
		register_setting( 'event-shortcode-settings-group', 'fs_hintergrundfarbe_button' );
		register_setting( 'event-shortcode-settings-group', 'fs_vordergrundfarbe_button' );
		register_setting( 'event-shortcode-settings-group', 'fs_hover_hintergrundfarbe_button' );
		register_setting( 'event-shortcode-settings-group', 'fs_hover_vordergrundfarbe_button' );
		register_setting( 'event-shortcode-settings-group', 'fs_schriftart' );
		register_setting( 'event-shortcode-settings-group', 'fs_linie_oben' );
		register_setting( 'event-shortcode-settings-group', 'fs_linie_unten' );
		register_setting( 'event-shortcode-settings-group', 'fs_alle_buttons' );
		register_setting( 'event-shortcode-settings-group', 'fs_runder_button' );
	}

	function add_infos_to_tec_settings_page() {
	?>
	<div class="wrap">
	<h1>Add Infos to TEC</h1>
	<hr>

	<form method="post" action="options.php">
	    <?php settings_fields( 'event-shortcode-settings-group' ); ?>
	    <?php do_settings_sections( 'event-shortcode-settings-group' );
			// Check that user has proper security level
			if ( !current_user_can( 'manage_options' ) ){
				 wp_die( __('You do not have permissions to perform this action', 'ps_feld') );
			}
			// absichern (nonce) //
			// wp_nonce_field('add_infos_to_tec_formular', 'ps_feld');

			// Optionen setzen, falls die Optionen noch nicht existieren
			if ( get_option('fs_option_pfad') == false ) {
			    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
			    $deprecated = null;
			    $autoload = 'no';
			    add_option( 'fs_option_pfad', 'http://beispielseite.de/events/category/', $deprecated, $autoload );
					add_option( 'fs_hintergrundfarbe_button', '#77BCC7', $deprecated, $autoload );
					add_option( 'fs_vordergrundfarbe_button', '#ffffff', $deprecated, $autoload );
					add_option( 'fs_hover_hintergrundfarbe_button', '#F9B81E', $deprecated, $autoload );
					add_option( 'fs_hover_vordergrundfarbe_button', '#ffffff', $deprecated, $autoload );
					add_option( 'fs_runder_button', '5', $deprecated, $autoload );
					add_option( 'fs_alle_buttons', '0', $deprecated, $autoload );
					add_option( 'fs_schriftart', '1', $deprecated, $autoload );
					add_option( 'fs_linie_oben', '1', $deprecated, $autoload );
					add_option( 'fs_linie_unten', '0', $deprecated, $autoload );
			}
			?>
	    <table class="form-table">

					<!-- Pfad -->
	        <tr valign="top">
					<th scope="row">Path e.g. Categories to "The Events Calendar" (e.g. http://beispielseite.de/events/category/):</th>
					<!--
	        <th scope="row"><?php echo __( 'Path e.g. categories to "The Events Calendar" (e.g. http://example.com/events/category/):', 'text-domain' ); ?></th>
					-->
	        <td><input type="text" name="fs_option_pfad" size=50 value="<?php echo esc_url_raw( get_option('fs_option_pfad') ); ?>" /></td>
	        </tr>

					<!-- Buttons -->
	        <tr valign="top">
					<th scope="row">Button - Background color:</th>
	        <td><input type="text" name="fs_hintergrundfarbe_button" value="<?php echo esc_attr( get_option('fs_hintergrundfarbe_button') ); ?>" class="color" /></td>
	        </tr>


	        <tr valign="top">
	        <th scope="row">Button - font color:</th>
	        <td><input type="text" name="fs_vordergrundfarbe_button" value="<?php echo esc_attr( get_option('fs_vordergrundfarbe_button') ); ?>" class="color" /></td>
	        </tr>

					<tr valign="top">
	        <th scope="row">Button - Background color when driving over the button (Hover):</th>
	        <td><input type="text" name="fs_hover_hintergrundfarbe_button" value="<?php echo esc_attr( get_option('fs_hover_hintergrundfarbe_button') ); ?>" class="color" /></td>
	        </tr>

					<tr valign="top">
	        <th scope="row">Button - font color when driving over the button (Hover):</th>
	        <td><input type="text" name="fs_hover_vordergrundfarbe_button" value="<?php echo esc_attr( get_option('fs_hover_vordergrundfarbe_button') ); ?>" class="color" /></td>
	        </tr>

					<tr valign="top">
					<th scope="row">Rounded corners (values from 0 - 30):</th>
					<td><input type="number" min="0" max="30" step="1" name="fs_runder_button" size=2 value="<?php echo esc_attr( get_option('fs_runder_button') ); ?>" /></td>
					</tr>


					<tr valign="top">
	        <th scope="row">All links as buttons:</th>
	        <td><input type="checkbox" name="fs_alle_buttons" value='1' <?php checked(get_option('fs_alle_buttons'), 1); ?> />
	        </tr>

					<!-- Diverses -->
					<tr valign="top">
	        <th scope="row">font for Copyright:</th>
	        <td><input type="radio" name="fs_schriftart" value="1" <?php checked(1, get_option('fs_schriftart'), true); ?>>italic
					<input type="radio" name="fs_schriftart" value="2" <?php checked(2, get_option('fs_schriftart'), true); ?>>bold
					<input type="radio" name="fs_schriftart" value="3" <?php checked(3, get_option('fs_schriftart'), true); ?>>normal</td>
	        </tr>

					<tr valign="top">
	        <th scope="row">Horizontal line above the block:</th>
	        <td><input type="checkbox" name="fs_linie_oben" value='1' <?php checked(get_option('fs_linie_oben'), 1); ?> />
	        </tr>

					<tr valign="top">
	        <th scope="row">Horizontal line below the block:</th>
	        <td><input type="checkbox" name="fs_linie_unten" value='1' <?php checked(get_option('fs_linie_unten'), 1); ?> />
	        </tr>

	    </table>
			<?php
			submit_button();
			// Überprüfung klappt noch nicht - fehlerhafte Ausführung
			/*
			if ( ! empty( $_POST ) &&  check_admin_referer(  'add_infos_to_tec_formular', 'ps_feld' ) ) {
   				submit_button();
				} else {
					submit_button();
					echo  'Fehler !!!';
				}
				*/
 		 ?>
			</form>
	</div>
	<?php
	// -------------------------------------------------- //
	// End: admin area
	// -------------------------------------------------- //
}
?>
