<?php
/**
 * Plugin Name: Add infos to the events calendar
 * Plugin URI:  https://haurand.com/plugins/add_infos_tec
 * Description: provides a shortcode block to single events (TEC)
 * Version:     0.5
 * Author:      gerhards@haurand.com
 * Author URI:  https://haurand.com/plugins/add_infos_tec
 * Text Domain: wporg
 * Domain Path: /languages
 * License:     GPL2
 */



Shortcode:
	[fuss]

Optional Arguments:

Examples:

*/

// Absichern vor unbefugten Aufruf //
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Sprachdateien laden - aktuell noch ausgeschaltet
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
Plugin: post Shortcode
Plugin URI: https://haurand.com
-->
';
/*----------------------------------------------------------------*/
// Start: holt die Farbeinstellungen aus der style_fuss.css
// zur Gestaltung der Buttons //
/*----------------------------------------------------------------*/

function fs_style_fuss_plugin_scripts() {
		// CSS-Datei einbinden:
		$script = plugin_dir_url( __FILE__ ) . 'assets/css/style_fuss.css';
		wp_enqueue_style( 'custom_style',  $script);

		// Variablen für die Gestaltung der Buttons
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
// Ende: holt die Farbeinstellungen aus der style_fuss.css
// zur Gestaltung der Buttons //
/*----------------------------------------------------------------*/


/*----------------------------------------------------------------*/
/* Start: shortcodes für Fuss
/*----------------------------------------------------------------*/

// Zeigt bei einer Veranstaltung oder einem Beitrag automatisch den Text aus "Beschriftung" in kursiv
// Aufruf-Beispiele:
// [fuss link="https://aachen50plus.de" vl=""] --> zeigt immer Bildnachweis, dann Mehr Infos mit dem Link und bei vl="" den Link zu "Weitere Veranstaltungen"
// [fuss vl=""] --> zeigt immer Bildnachweis, dann "keine Webseite angegeben" und bei vl="ja" den Link zu "Weitere Veranstaltungen"
// vl = Veranstaltungsliste
// [fuss] --> zeigt immer Bildnachweis, dann "keine Webseite angegeben" und keinen Link zu "Weitere Veranstaltungen"
// hgg, 23.2.2019
// erweitert: hgg, 29.3.2019: zusätzlich kann bei vl die Kategorie angeben werden, so dass bei Klick auf den Link sofort die Veranstaltungen der jeweiligen Kategorie angezeigt werden, z. B.
// [fuss link="http://www.melan.de/go/standort-detail/1-flohmarkt-troedelmarkt-in-aachen-altstadt.html" vl="Feiern und Feste"]

function fs_beitrags_fuss_pi($atts) {
  	$werte = shortcode_atts( array(
  	  'link' => '',
      'vl' => 'nein',
      'il' => '',
  	  ), $atts);
    $fs_ausgabe = '';
		//
		// Ausgabe Linie oberhalb //
		//
		$fs_l_o = esc_attr(get_option('fs_linie_oben '));
		if (esc_attr(get_option('fs_linie_oben ')) == '1') {
			  // echo 'Linie oben: ' . var_dump($l_o); //
				$fs_ausgabe = $fs_ausgabe . '<hr>';
				// echo 'Ausgabe: ' . var_dump($fs_ausgabe); //
		}
		//
		// Verlinkung
		//
		// Pfad aus den Einstellungen holen: //
		$veranstaltungen = esc_url_raw( get_option('fs_option_pfad') );
		// Dateipfad absichern
		// Verwendete Kategorien von TEC
    $kategorien = cliff_get_events_taxonomies();
    if ( trim($werte['link']) != '') {
			// wahlweise auch den Link als Button:
			if (esc_attr(get_option('fs_alle_buttons')) == 1){
				$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"> <a class="fuss_button-beitrag" href=' . $werte['link'] . ' target="_blank">Mehr Infos</a></p><br>';
				/* Beispiel für Sprachdatei:
			  $fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"> <a class="fuss_button-beitrag" href=' . $werte['link'] . ' target="_blank">' . __( 'Mehr Infos', 'text-domain' ) . '</a></p><br>';
				*/
			} else {
      	$fs_ausgabe = $fs_ausgabe . '<a href=' . $werte['link'] . ' target="_blank">Mehr Infos</a><br>';
			}
		}
		//
		// Schriftart
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
		// Ausgabe des Copyrights //
		//
    $fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz">' . $fs_schriftart_ein  . get_post(get_post_thumbnail_id())->post_excerpt . $fs_schriftart_aus . '</p><br>';
		//
		// Veranstaltungen mit Kategorie
		//
		if ($veranstaltungen != 'Achtung') {
	    if ( $werte['vl'] != 'nein' ) {
	      if ( trim($werte['vl']) != '') {
	        /* Leerzeichen werden ggfs. durch "-" ersetzt (Sicherheitsmaßnahme bei Eingabe von Kategorien, die Leerzeichen enthalten, z. B. "Feiern und Feste") */
	        $vergleichswert = $werte['vl'];
	        /* wenn der Vergleichswert im Array der Kategorien enthalten ist: */
	        if (in_array($vergleichswert, $kategorien )){
	          /* Sonderzeichen ersetzen */
	          $werte['vl'] = fs_sonderzeichen ($werte['vl']);
	          $veranstaltungen = $veranstaltungen . str_replace(" ", "-", $werte['vl']);
	          $vergleichswert = ': ' . $vergleichswert . '';
	          }
	        else {
	          $vergleichswert = '';
	          }
	      }
				$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"> <a class="fuss_button-beitrag" href=' . $veranstaltungen . ' target="_blank">Weitere Veranstaltungen' . $vergleichswert . '</a></p>';
			}
		else {
			// URL ist fehlerhaft !
			$fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz">' . $veranstaltungen . '</p>';
		}
	}
	//
	// Interner Link (kann aber auch ein externer Link sein)
	//
  if ( trim($werte['il']) != '') {
     $fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . $werte['il'] . ' target="_blank">Mehr Infos auf dieser Seite</a></p>';
  }
	//
	// Ausgabe Linie unterhalb //
	//
	if (esc_attr(get_option('fs_linie_unten')) == 1) {
			$fs_ausgabe = $fs_ausgabe . '<hr>';
	}
	return $fs_ausgabe;
}
add_shortcode('fuss', 'fs_beitrags_fuss_pi');



/*----------------------------------------------------------------*/
/* Ende: shortcodes für Fuss
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
	// siehe hier: https://theeventscalendar.com/support/forums/topic/get_terms-with-tribe_events_cat-returning-wp_error-in-functions-php/
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
/* Umlaute umwandeln, damit z. B. Führung in Fuehrung
/* umgewandelt wird, weil sonst die Kategorieliste nicht gefunden wird.
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
// Start: Neues Dashboard-Widget hinzufügen
// -------------------------------------------------- //
function fs_add_dashboard_widget() {
  wp_add_dashboard_widget(
    'mein_dashboard_widget',
    __('Dashboard-Widget für das Post-Shortcode-Plugin',
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
    'Mein erstes Dashboard-Widget für das Post-Shortcode-Plugin :-)',
    'textdomaine'
    );
  }

// -------------------------------------------------- //
// Ende: Neues Dashboard-Widget hinzufügen
// -------------------------------------------------- //

// -------------------------------------------------- //
// Start: Adminbereich
// -------------------------------------------------- //

	// create custom plugin settings menu

// Farbwähler
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


	add_action('admin_menu', 'post_shortcode_create_menu');

	function post_shortcode_create_menu() {

		//create new top-level menu: add_menu_page
		add_submenu_page('Post Shortcode Plugin Settings', 'Post Shortcode Settings', 'administrator', __FILE__, 'post_shortcode_settings_page' , plugins_url('/images/icon.png', __FILE__) );
		// funktioniert jetzt:
		add_options_page( 'Post Shortcode Plugin Settings', 'Post Shortcode Settings', 'manage_options', 'post_shortcode_settings_page', 'post_shortcode_settings_page');
		//call register settings function
		add_action( 'admin_init', 'register_post_shortcode_settings' );
		// Überprüfung nonce - noch mal prüfen
		if ( ! empty( $_POST ) &&  check_admin_referer(  'ps_formular', 'ps_feld' ) ) {
				echo  'Fehler !!!';
		}

	}

	// Einstellungen in der Plugin-Liste
	function plugin_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=post_shortcode_settings_page">'	. __( 'Einstellungen' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}
	add_filter(
		'plugin_action_links_' . plugin_basename( __FILE__ ),	'plugin_settings_link'
	);

	function register_post_shortcode_settings() {
		//register our settings
		register_setting( 'post-shortcode-settings-group', 'fs_option_pfad' );
		register_setting( 'post-shortcode-settings-group', 'fs_hintergrundfarbe_button' );
		register_setting( 'post-shortcode-settings-group', 'fs_vordergrundfarbe_button' );
		register_setting( 'post-shortcode-settings-group', 'fs_hover_hintergrundfarbe_button' );
		register_setting( 'post-shortcode-settings-group', 'fs_hover_vordergrundfarbe_button' );
		register_setting( 'post-shortcode-settings-group', 'fs_schriftart' );
		register_setting( 'post-shortcode-settings-group', 'fs_linie_oben' );
		register_setting( 'post-shortcode-settings-group', 'fs_linie_unten' );
		register_setting( 'post-shortcode-settings-group', 'fs_alle_buttons' );
		register_setting( 'post-shortcode-settings-group', 'fs_runder_button' );
	}

	function post_shortcode_settings_page() {
	?>
	<div class="wrap">
	<h1>Post Shortcode</h1>
	<hr>

	<form method="post" action="options.php">
	    <?php settings_fields( 'post-shortcode-settings-group' ); ?>
	    <?php do_settings_sections( 'post-shortcode-settings-group' );
			// Check that user has proper security level
			if ( !current_user_can( 'manage_options' ) ){
				 wp_die( __('You do not have permissions to perform this action', 'ps_feld') );
			}
			// absichern (nonce) //
			wp_nonce_field('ps_formular', 'ps_feld');

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
					<th scope="row">Pfad z. B. Kategorien zu "The Events Calendar" (z. B. http://beispielseite.de/events/kategorie/):</th>
					<!--
	        <th scope="row"><?php echo __( 'Pfad z. B. categories zu "The Events Calendar" (z. B. http://beispielseite.de/events/kategorie/):', 'text-domain' ); ?></th>
					-->
	        <td><input type="text" name="fs_option_pfad" size=50 value="<?php echo esc_url_raw( get_option('fs_option_pfad') ); ?>" /></td>
	        </tr>

					<!-- Buttons -->
	        <tr valign="top">
					<th scope="row">Button - Hintergrundfarbe:</th>
	        <td><input type="text" name="fs_hintergrundfarbe_button" value="<?php echo esc_attr( get_option('fs_hintergrundfarbe_button') ); ?>" class="color" /></td>
	        </tr>


	        <tr valign="top">
	        <th scope="row">Button - Schriftfarbe:</th>
	        <td><input type="text" name="fs_vordergrundfarbe_button" value="<?php echo esc_attr( get_option('fs_vordergrundfarbe_button') ); ?>" class="color" /></td>
	        </tr>

					<tr valign="top">
	        <th scope="row">Button - Hintergrundfarbe beim Überfahren des Buttons (Hover):</th>
	        <td><input type="text" name="fs_hover_hintergrundfarbe_button" value="<?php echo esc_attr( get_option('fs_hover_hintergrundfarbe_button') ); ?>" class="color" /></td>
	        </tr>

					<tr valign="top">
	        <th scope="row">Button - Schriftfarbe beim Überfahren des Buttons (Hover):</th>
	        <td><input type="text" name="fs_hover_vordergrundfarbe_button" value="<?php echo esc_attr( get_option('fs_hover_vordergrundfarbe_button') ); ?>" class="color" /></td>
	        </tr>

					<tr valign="top">
					<th scope="row">Abgerundete Ecken (Werte von 0 - 30):</th>
					<td><input type="number" min="0" max="30" step="1" name="fs_runder_button" size=2 value="<?php echo esc_attr( get_option('fs_runder_button') ); ?>" /></td>
					</tr>


					<tr valign="top">
	        <th scope="row">Alle Links als Buttons:</th>
	        <td><input type="checkbox" name="fs_alle_buttons" value='1' <?php checked(get_option('fs_alle_buttons'), 1); ?> />
	        </tr>

					<!-- Diverses -->
					<tr valign="top">
	        <th scope="row">Schriftart für Copyright:</th>
	        <td><input type="radio" name="fs_schriftart" value="1" <?php checked(1, get_option('fs_schriftart'), true); ?>>kursiv
					<input type="radio" name="fs_schriftart" value="2" <?php checked(2, get_option('fs_schriftart'), true); ?>>fett
					<input type="radio" name="fs_schriftart" value="3" <?php checked(3, get_option('fs_schriftart'), true); ?>>normal</td>
	        </tr>

					<tr valign="top">
	        <th scope="row">Horizontale Linie oberhalb des Blocks:</th>
	        <td><input type="checkbox" name="fs_linie_oben" value='1' <?php checked(get_option('fs_linie_oben'), 1); ?> />
	        </tr>

					<tr valign="top">
	        <th scope="row">Horizontale Linie unterhalb des Blocks:</th>
	        <td><input type="checkbox" name="fs_linie_unten" value='1' <?php checked(get_option('fs_linie_unten'), 1); ?> />
	        </tr>

	    </table>
			<?php
			submit_button();
			// Überprüfung klappt noch nicht - fehlerhafte Ausführung
			/*
			if ( ! empty( $_POST ) &&  check_admin_referer(  'ps_formular', 'ps_feld' ) ) {
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
	// Ende: Adminbereich
	// -------------------------------------------------- //
}
?>
