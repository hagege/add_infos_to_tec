<?php
/**
 * Plugin Name: Add infos to the events calendar
 * Description: Provides a shortcode block (image copyright, button with link to events with a special category, link to the website of the organizer) in particular to single events for The Events Calendar Free Plugin (by MODERN TRIBE)
 * Version:     0.66
 * Author:      Hans-Gerd Gerhards (haurand.com)
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


// Load language files
function meine_textdomain_laden() {
	load_plugin_textdomain(
	'add_infos_to_tec',
	false,
	basename( dirname( __FILE__ ) ) . '/languages'
	);
}
add_action('plugins_loaded','meine_textdomain_laden');



/*----------------------------------------------------------------*/
// Start: get the color settings from style_fuss.css
// for the design of the buttons
/*----------------------------------------------------------------*/

function fs_style_fuss_plugin_scripts() {
		// Include CSS file:
		$script = plugin_dir_url( __FILE__ ) . 'assets/css/style_fuss.css';
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
add_action( 'wp_enqueue_scripts', 'fs_style_fuss_plugin_scripts' );

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
		$veranstaltungen = esc_url_raw( $add_infos_to_tec_options['fs_option_pfad']);
		// Save file path
		// Categories used by TEC
    $kategorien = cliff_get_events_taxonomies();
    if ( trim($werte['link']) != '') {
			// optionally also the link as button:
			if (esc_attr($add_infos_to_tec_options['fs_alle_buttons']) == 1){
			  $fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"> <a class="fuss_button-beitrag" href=' . $werte['link'] . ' target="_blank">' . __( 'Read more', 'add_infos_to_tec' ) . '</a></p><br>';
			} else {
      	$fs_ausgabe = $fs_ausgabe . '<a href=' . $werte['link'] . ' target="_blank">'. __( 'Read more', 'add_infos_to_tec' ) . '</a><br>';
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
		// Display of the copyright //
		//
    $fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz">' . $fs_schriftart_ein  . get_post(get_post_thumbnail_id())->post_excerpt . $fs_schriftart_aus . '</p><br>';
		//
		// Events with category
		//

		//
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
				$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"> <a class="fuss_button-beitrag" href=' . $veranstaltungen . ' target="_blank">'. __( 'More Events', 'add_infos_to_tec' ) . $vergleichswert . '</a></p>';
			}
	//
	// Internal link (can also be an external link)
	//
  if ( trim($werte['il']) != '') {
     $fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . $werte['il'] . ' target="_blank">' . __( 'Read more on this website', 'add_infos_to_tec' ) . '</a></p>';
  }
	//
	// Output line below //
	//
	if (esc_attr($add_infos_to_tec_options['fs_linie_unten']) == 1) {
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
/* not used yet
function fs_add_dashboard_widget() {
  wp_add_dashboard_widget(
    'mein_dashboard_widget',
    __('Dashboard-Widget for "Add infos to the events calendar" - Plugin', 'add_infos_to_tec'),
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
    __('First Dashboard-Widget for "Add infos to the events calendar" - Plugin', 'add_infos_to_tec'),
    'add_infos_to_tec'
    );
  }
*/

// -------------------------------------------------- //
// Ende: Add new Dashboard-Widget
// -------------------------------------------------- //

// -------------------------------------------------- //
// Start: admin area
// -------------------------------------------------- //

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

// create custom plugin settings menu
	function add_infos_to_tec_create_menu() {

		//create new top-level menu: add_menu_page
		add_submenu_page('Add Infos to TEC Plugin Settings',  __('Add Infos to TEC Settings', 'add_infos_to_tec'), 'administrator', __FILE__, 'add_infos_to_tec_settings_page' , plugins_url('/images/icon.png', __FILE__) );
		add_options_page( 'Add Infos to TEC Plugin Settings',  __('Add Infos to TEC Settings', 'add_infos_to_tec'), 'manage_options', 'add_infos_to_tec_settings_page', 'add_infos_to_tec_settings_page');
		//call register settings function
		add_action( 'admin_init', 'register_add_infos_to_tec_settings' );
}

// Settings in the Plugin List
	function plugin_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=add_infos_to_tec_settings_page">'	. __( 'Settings' ) . '</a>';
		// check_admin_referer( 'plugin_settings_link', 'ait_tec' );
		array_push( $links, $settings_link );
		return $links;
	}
	add_filter(
		'plugin_action_links_' . plugin_basename( __FILE__ ),	'plugin_settings_link'
	);

//register our settings
	function register_add_infos_to_tec_settings() {
		register_setting( 'add_infos_to_tec_settings-group', 'add_infos_to_tec_settings' );
	}


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
		return $ait_options;
	}

// Determine path for events and suggest as path if necessary
function path_for_tec(){
	$ait_path = esc_url( tribe_get_listview_link() );
	// delete last "/":
	$ait_path = substr($ait_path,0,strlen($ait_path)-1);
	$tec_category = __( 'category', 'the-events-calendar' );
	$tec_category = strtolower($tec_category);
	// show the path without the kind of view:
	$ait_path = substr($ait_path,0,strrpos($ait_path, '/')) . '/' . $tec_category . '/';
	// echo $ait_path .'<br>';
	return $ait_path;
}



// page with settings
	function add_infos_to_tec_settings_page() {
	?>
	<div class="wrap">
	<h1>Add Infos to TEC</h1>
	<hr>

	<form method="post" action="options.php">
	    <?php
			settings_fields( 'add_infos_to_tec_settings-group' );
	    do_settings_sections( 'add_infos_to_tec_settings-group' );
			// get plugin options from the database
			$add_infos_to_tec_options = get_option( 'add_infos_to_tec_settings' );
			// Check that user has proper security level
			if ( !current_user_can( 'manage_options' ) ){
				 wp_die( __('You do not have permissions to perform this action') );
			}
			// absichern (nonce) //
			$nonce_field = wp_nonce_field('plugin_settings_link', 'ait_tec');
			// Set options if the options do not yet exist
			if (empty( $add_infos_to_tec_options)) {
			    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
			    $deprecated = null;
			    $autoload = 'no';
					$tec_path = path_for_tec();
					$add_infos_to_tec_options = array(
							'fs_option_pfad' => $tec_path,
							'fs_hintergrundfarbe_button' => '#77BCC7',
							'fs_vordergrundfarbe_button' => '#000000',
							'fs_hover_hintergrundfarbe_button' => '#F9B81E',
							'fs_hover_vordergrundfarbe_button' => '#ffffff',
							'fs_runder_button' => '5',
							'fs_alle_buttons' => '0',
							'fs_schriftart' => '1',
							'fs_linie_oben' => '1',
							'fs_linie_unten' => '0'
						);
					add_option( 'add_infos_to_tec_settings', $add_infos_to_tec_options, $deprecated, $autoload);
			}
			$add_infos_to_tec_options = ait_test_array($add_infos_to_tec_options);
			?>
	    <table class="form-table">
					<!-- path-->
	        <tr valign="top">
					<?php
					$tec_path= path_for_tec();
					echo __( 'This could be the path to the categories of The Events Calendar (TEC): ', 'add_infos_to_tec' ) . $tec_path . '<br />';
					echo __( 'To be on the safe side, however, you should check this by going to the relevant event after using the shortcut and checking that the links are executed correctly.', 'add_infos_to_tec' );
					?>
					<!-- here I want to check if a folder exists in further versions of plugin -->
	        <th scope="row"><?php echo __( 'Path e.g. categories to The Events Calendar (e.g. http://example.com/events/category/):', 'add_infos_to_tec' ); ?></th>

	        <td><input type="text" name="add_infos_to_tec_settings[fs_option_pfad]" size=50 value="<?php echo esc_url_raw( $add_infos_to_tec_options['fs_option_pfad']); ?>" /></td>
	        </tr>

					<!-- Buttons -->
	        <tr valign="top">
					<th scope="row"><?php echo __( 'Button - Background color:', 'add_infos_to_tec' ); ?></th>
	        <td><input type="text" name="add_infos_to_tec_settings[fs_hintergrundfarbe_button]" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_hintergrundfarbe_button']); ?>" class="color" /></td>
	        </tr>


	        <tr valign="top">
	        <th scope="row"><?php echo __( 'Button - font color:', 'add_infos_to_tec' ); ?></th>
	        <td><input type="text" name="add_infos_to_tec_settings[fs_vordergrundfarbe_button]" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_vordergrundfarbe_button']); ?>" class="color" /></td>
	        </tr>

					<tr valign="top">
	        <th scope="row"><?php echo __( 'Button - Background color when driving over the button (Hover):', 'add_infos_to_tec' ); ?></th>
	        <td><input type="text" name="add_infos_to_tec_settings[fs_hover_hintergrundfarbe_button]" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_hover_hintergrundfarbe_button']); ?>" class="color" /></td>
	        </tr>

					<tr valign="top">
	        <th scope="row"><?php echo __( 'Button - font color when driving over the button (Hover):', 'add_infos_to_tec' ); ?></th>
	        <td><input type="text" name="add_infos_to_tec_settings[fs_hover_vordergrundfarbe_button]" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_hover_vordergrundfarbe_button']); ?>" class="color" /></td>
	        </tr>

					<tr valign="top">
					<th scope="row"><?php echo __( 'Rounded corners (values from 0 - 30):', 'add_infos_to_tec' ); ?></th>
					<td><input type="number" min="0" max="30" step="1" name="add_infos_to_tec_settings[fs_runder_button]" size=2 value="<?php echo esc_attr( $add_infos_to_tec_options['fs_runder_button']); ?>" /></td>
					</tr>


					<tr valign="top">
	        <th scope="row"><?php echo __( 'All links as buttons:', 'add_infos_to_tec' ); ?></th>
	        <td><input type="checkbox" name="add_infos_to_tec_settings[fs_alle_buttons]" value="1" <?php echo esc_attr(checked($add_infos_to_tec_options['fs_alle_buttons'], 1, true)); ?> />
	        </tr>

					<!-- Diverses -->
					<tr valign="top">
	        <th scope="row"><?php echo __( 'Font for Copyright:', 'add_infos_to_tec' ); ?></th>
	        <td><input type="radio" name="add_infos_to_tec_settings[fs_schriftart]" value="1" <?php echo esc_attr(checked(1, $add_infos_to_tec_options['fs_schriftart'], true)); ?>><?php echo __( 'italic', 'add_infos_to_tec' ); ?>
					<input type="radio" name="add_infos_to_tec_settings[fs_schriftart]" value="2" <?php echo esc_attr(checked(2, $add_infos_to_tec_options['fs_schriftart'], true)); ?>><?php echo __( 'bold', 'add_infos_to_tec' ); ?>
					<input type="radio" name="add_infos_to_tec_settings[fs_schriftart]" value="3" <?php echo esc_attr(checked(3, $add_infos_to_tec_options['fs_schriftart'], true)); ?>><?php echo __( 'normal', 'add_infos_to_tec' ); ?></td>
	        </tr>

					<tr valign="top">
	        <th scope="row"><?php echo __( 'Horizontal line above the block:', 'add_infos_to_tec' ); ?></th>
	        <td><input type="checkbox" name="add_infos_to_tec_settings[fs_linie_oben]" value="1" <?php echo esc_attr(checked($add_infos_to_tec_options['fs_linie_oben'], 1, true)); ?> />
	        </tr>

					<tr valign="top">
	        <th scope="row"><?php echo __( 'Horizontal line below the block:', 'add_infos_to_tec' ); ?></th>
	        <td><input type="checkbox" name="add_infos_to_tec_settings[fs_linie_unten]" value="1" <?php echo esc_attr(checked($add_infos_to_tec_options['fs_linie_unten'], 1, true)); ?> />
	        </tr>

	    </table>
			<?php
			submit_button();
 		 ?>
			</form>
	</div>
	<?php
	// -------------------------------------------------- //
	// End: admin area
	// -------------------------------------------------- //


	// das würde benötigt - von hier
	/* klappt noch nicht
	function ait_hsh_plugin_scripts($plugin_array)
	{
	    //enqueue TinyMCE plugin script with its ID.
	    $plugin_array["ait_btn_cmd"] =  plugins_url('assets/js',__FILE__) . '/index.js';
	    return $plugin_array;
	}

	add_filter("mce_external_plugins", "ait_hsh_plugin_scripts");

	function ait_hsh_register_buttons_editor($buttons)
	{
	    //register buttons with their id.
	    array_push($buttons, "yellow");
	    return $buttons;
	}

	add_filter("mce_buttons", "ait_hsh_register_buttons_editor");
	*/
	// das würde benötigt - bis hier

}
?>
