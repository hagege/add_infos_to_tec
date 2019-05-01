<?php
/*
Plugin Name: Posts Shortcode
Plugin URI: https://haurand.com
Description: Creates a shortcode to display a footer in pages and posts (such as copyright image)
Version: 0.1
Author: hgg
Author URI: https://haurand.com
License: GPLv2
	Copyright 2019 haurand.com (info@haurand.com)

Shortcode:
	[fuss]

Optional Arguments:

Examples:
*/

/* planned: URL */

// variables
	$ausgabe = '
<!--
Plugin: posts Shortcode
Plugin URI: https://haurand.com
-->
';
/* Einbinden der CSS-Datei */
function enqueue_styles() {
	wp_enqueue_style( 'style_fuss', plugins_url('assets/css/style_fuss.css',__FILE__), array(), 1.0, 'screen' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_styles');


// shortcode echo function

// Zeigt bei einer Veranstaltung oder einem Beitrag automatisch den Text aus "Beschriftung" in kursiv
// Aufruf-Beispiele:
// [fuss link="https://aachen50plus.de" fm="ja" vl="ja"] --> zeigt immer Bildnachweis, dann Mehr Infos mit dem Link und bei fm="ja" den Link zu "Weitere Flohmärkte" und bei vl="ja" den Link zu "Weitere Veranstaltungen"
// [fuss vl="ja"] --> zeigt immer Bildnachweis, dann "keine Webseite angegeben" und bei vl="ja" den Link zu "Weitere Veranstaltungen"
// vl = Veranstaltungsliste
// [fuss] --> zeigt immer Bildnachweis, dann "keine Webseite angegeben" und keinen Link zu "Weitere Veranstaltungen"
// hgg, 23.2.2019
// erweitert: hgg, 29.3.2019: zusätzlich kann bei vl die Kategorie angeben werden, so dass bei Klick auf den Link sofort die Veranstaltungen der jeweiligen Kategorie angezeigt werden, z. B.
// [fuss link="http://www.melan.de/go/standort-detail/1-flohmarkt-troedelmarkt-in-aachen-altstadt.html" kfm="ja" vl="Feiern und Feste"]

function beitrags_fuss_pi($atts) {
  	$werte = shortcode_atts( array(
  	  'link' => '',
      'fm' => 'nein',
      'vl' => 'nein',
      'il' => '',
  	  ), $atts);
    $ausgabe = '';
    $veranstaltungen = 'https://aachen50plus.de/veranstaltungen/kategorie/';
    $kategorien = cliff_get_events_taxonomies();

    if ( trim($werte['link']) != '') {
      $ausgabe = '<br><a href=' . $werte['link'] . ' target="_blank">Mehr Infos</a>';
    }
    $ausgabe = $ausgabe . '<br><br><em>' . get_post(get_post_thumbnail_id())->post_excerpt . '</em>';
    if ( $werte['fm'] != 'nein' ) {
      $ausgabe = $ausgabe . '<br><br><p class="button-absatz"><a class="tribe-events-button-beitrag" href="https://aachen50plus.de/veranstaltungen/kategorie/flohmarkt/">Weitere Flohmärkte</a></p>';
    }
    if ( $werte['vl'] != 'nein' ) {
      if ( trim($werte['vl']) != '') {
        /* Leerzeichen werden ggfs. durch "-" ersetzt (Sicherheitsmaßnahme bei Eingabe von Kategorien, die Leerzeichen enthalten, z. B. "Feiern und Feste") */
        $vergleichswert = $werte['vl'];
        /* wenn der Vergleichswert im Array der Kategorien enthalten ist: */
        if (in_array($vergleichswert, $kategorien )){
          /* Sonderzeichen ersetzen */
          $werte['vl'] = sonderzeichen ($werte['vl']);
          $veranstaltungen = $veranstaltungen . str_replace(" ", "-", $werte['vl']);
          $vergleichswert = ': ' . $vergleichswert . '';
          }
        else {
          $vergleichswert = '';
          }
      }
      $ausgabe = $ausgabe . '<br><br><p class="button-absatz"><a class="tribe-events-button-beitrag" href=' . $veranstaltungen . ' target="_blank">Weitere Veranstaltungen</a></p>';
    }
    if ( trim($werte['il']) != '') {
       $ausgabe = $ausgabe . '<p class="button-absatz"><a class="tribe-events-button-beitrag" href=' . $werte['il'] . ' target="_blank">Mehr Infos auf dieser Seite</a></p><hr>';
    }
    $ausgabe = $ausgabe . '<hr>';
	return $ausgabe;
}
add_shortcode('fuss', 'beitrags_fuss_pi');



/*----------------------------------------------------------------*/
/* Ende: shortcodes für Anzahl Veranstaltungen und Beiträge
/* Datum: 18.12.2018
/* Autor: hgg
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
	$events_cats = get_terms( $tecmain::TAXONOMY, $cat_args );
	/* $events_cats_names = array(); */

	if( ! is_wp_error( $events_cats ) && ! empty( $events_cats ) && is_array( $events_cats) ) {
		$events_cats_names = array();
		foreach( $events_cats as $key => $value ) {
			$events_cats_names[] = $value->name;
		}

	   /* var_dump( $events_cats_names );  Anzeige der Kategorien */
	}
	return $events_cats_names;
}

/* Umlaute umwandeln, damit z. B. Führung in Fuehrung umgewandelt wird, weil sonst die Kategorieliste nicht gefunden wird. */
function sonderzeichen($string)
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
foreach( $shortcodes as $shortcode ) add_shortcode( $shortcode, 'beitrags_fuss_pi' ); // create shortcode for each item in $shortcodes


// -------------------------------------------------- //
// Adminbereich konfigurieren
// -------------------------------------------------- //
function po_sh_einstellungen_initialisieren() {
// Einstellungen registrieren
register_setting(
    'po_sh_einstellungen',
    'po_sh_einstellungen_allgemein',
    [
    'sanitize_callback' =>
    'po_sh_feld_pfad_anzeigen_sani_cb'
    ]
);
// Neue Sektion "Allgemeine Einstellungen" registrieren
add_settings_section(
    'po_sh_einstellungen_sektion_allgemein',
    __('Allgemeine Einstellungen', 'textdomaine'),
    'po_sh_einstellungen_sektion_allgemein_callback',
    'po_sh_einstellungen_seite'
);
// Neues Feld in der Sektion registrieren
add_settings_field(
    'po_sh_feld_pfad_anzeigen',
    __( 'Pfad für Veranstaltungen anzeigen', 'textdomaine' ),
    'po_sh_feld_pfad_anzeigen_callback',
    'po_sh_einstellungen_seite',
    'po_sh_einstellungen_sektion_allgemein',
    [
    'label_for' => 'po_sh_einstellung_pfad_anzeigen',
    ]
);
}
// Funktion an den "admin_init"-Hook hängen
add_action(
    'admin_init',
    'po_sh_einstellungen_initialisieren'
);

function po_sh_einstellungen_sektion_allgemein_callback($args) {
    ?>
    <p id="<?php echo esc_attr( $args['id'] ); ?>">
    <?php
    esc_html_e(
    'Allgemeine Einstellungen zum Posts-Shortcode-Plugin:',
    'textdomaine'
    );
    ?>
  </p>
  <?php
}

function po_sh_feld_pfad_anzeigen_callback( $args ) {
  // Registrierte Einstellungen laden
  $options = get_option( 'po_sh_einstellungen_allgemein' );
  // Feld ausgeben
  $id = esc_attr( $args['label_for'] );
  ?>
  <input type="text"
  id="<?php echo $id; ?>"
  name="po_sh_einstellungen_allgemein[<?php echo $id;
  ?>]"
  value="pfad" <?php isset($options[$id] );
  ?> />
  <label for="<?php echo $id; ?>">
  <?php esc_html_e(
  'Pfad für Veranstaltungen eingeben',
  'textdomaine' );
  ?>
  </label>
  <?php
  }

function po_sh_settings_page() {
  // add top level menu page
  add_options_page(
  'Posts-Shortcodes',
  'Posts-Shortcodes',
  'manage_options',
  'po_sh_einstellungen_seite',
  'po_sh_einstellungen_seite_html'
  );
  }
  // Menü-Funktion an den Action-Hook "admin_menu" hängen
  add_action( 'admin_menu', 'po_sh_settings_page' );

function po_sh_einstellungen_seite_html() {
  // User-Zugang prüfen
  if ( ! current_user_can( 'manage_options' ) )
    return;
  ?>
  <div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form action="options.php" method="post">
    <?php
    // Ausgabe Zusatzdaten:
    settings_fields( 'po_sh_einstellungen' );
    // Ausgabe Sektionen und Felder
    do_settings_sections( 'po_sh_einstellungen_seite' );
    // Ausgabe des Submit-Buttons
    submit_button();
    ?>
    </form>
  </div>
  <?php
  }

// Callback: Einstellung absichern
function po_sh_feld_pfad_anzeigen_sani_cb($data) {
  if (isset($data['po_sh_feld_pfad_anzeigen']) && 1 != $data['po_sh_feld_pfad_anzeigen']) {
		echo 'hier bin ich';
    add_settings_error(
    'requiredpfad',
    'empty',
    'Pfad muss eingegeben werden (es wird nicht geprüft, ob der Pfad korrekt ist).',
    'error'
    );
    unset( $data['po_sh_feld_pfad_anzeigen'] );
    }
  return $data;
  }

function plugin_settings_link( $links ) {
  $settings_link ='<a href="options-general.php?page=einstellungen_seite">' . __( 'Settings' ) . '</a>';
  array_push( $links, $settings_link );
  return $links;
  }
  add_filter(
  'plugin_action_links_' . plugin_basename( __FILE__ ),
  'plugin_settings_link'
  );

// -------------------------------------------------- //
// Neues Dashboard-Widget hinzufügen
// -------------------------------------------------- //
function po_sh_add_dashboard_widget() {
  wp_add_dashboard_widget(
    'mein_dashboard_widget',
    __('Dashboard-Widget für das Posts-Shortcode-Plugin',
    'textdomaine'
    ),
    'po_sh_dashboard_widget_html'
    );
  }

add_action(
  'wp_dashboard_setup',
  'po_sh_add_dashboard_widget'
  );
  // Ausgabe des Inhaltes des Dashboard-Widgets
  function po_sh_dashboard_widget_html($post,$callback_args){
    esc_html_e(
    'Mein erstes Dashboard-Widget für das Posts-Shortcode-Plugin :-)',
    'textdomaine'
    );
  }

// -------------------------------------------------- //
// Alternativen Adminbereich hinzufügen
// -------------------------------------------------- //

	// create custom plugin settings menu
	add_action('admin_menu', 'post_shortcode_create_menu');

	function post_shortcode_create_menu() {

		//create new top-level menu
		add_menu_page('Post Shortcode Plugin Settings', 'Post Shortcode Settings', 'administrator', __FILE__, 'post_shortcode_settings_page' , plugins_url('/images/icon.png', __FILE__) );

		//call register settings function
		add_action( 'admin_init', 'register_post_shortcode_settings' );
	}


	function register_post_shortcode_settings() {
		//register our settings
		register_setting( 'post-shortcode-settings-group', 'option_pfad' );
		register_setting( 'post-shortcode-settings-group', 'hintergrundfarbe_button' );
		register_setting( 'post-shortcode-settings-group', 'vordergrundfarbe_button' );
		register_setting( 'post-shortcode-settings-group', 'schriftart' );
	}

	function post_shortcode_settings_page() {
	?>
	<div class="wrap">
	<h1>Post Shortcode</h1>

	<form method="post" action="options.php">
	    <?php settings_fields( 'post-shortcode-settings-group' ); ?>
	    <?php do_settings_sections( 'post-shortcode-settings-group' ); ?>
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">Pfad:</th>
	        <td><input type="text" name="option_pfad" value="<?php echo esc_attr( get_option('option_pfad') ); ?>" /></td>
	        </tr>

	        <tr valign="top">
	        <th scope="row">Button - Hintergrundfarbe</th>
	        <td><input type="text" name="hintergrundfarbe_button" value="<?php echo esc_attr( get_option('hintergrundfarbe_button') ); ?>" /></td>
	        </tr>

	        <tr valign="top">
	        <th scope="row">Button - Vordergrundfarbe</th>
	        <td><input type="text" name="vordergrundfarbe_button" value="<?php echo esc_attr( get_option('vordergrundfarbe_button') ); ?>" /></td>
	        </tr>

					<tr valign="top">
	        <th scope="row">Schriftart für Copyright</th>
	        <td><input type="radio" name="schriftart" value="1" <?php checked(1, get_option('schriftart'), true); ?>>kursiv
					<input type="radio" name="schriftart" value="2" <?php checked(2, get_option('schriftart'), true); ?>>fett
					<input type="radio" name="schriftart" value="3" <?php checked(3, get_option('schriftart'), true); ?>>normal</td>
	        </tr>
	    </table>

	    <?php submit_button(); ?>

	</form>
	</div>
<?php
}
?>
