<?php
/*
Plugin Name: Post Shortcode
Plugin URI: https://haurand.com
Description: Creates a shortcode to display a footer in pages and posts (such as copyright image)
Version: 0.3
Author: hgg
Author URI: https://haurand.com
License: GPLv2

Shortcode:
	[fuss]

Optional Arguments:

Examples:

*/

// Absichern vor unbefugten Aufruf //
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// variables
	$ausgabe = '
<!--
Plugin: post Shortcode
Plugin URI: https://haurand.com
-->
';
// holt die Farbeinstellungen aus der style_fuss.css: //
function fs_style_fuss_plugin_scripts() {
		$script = plugin_dir_url( __FILE__ ) . 'assets/css/style_fuss.css';
		wp_enqueue_style( 'custom_style',  $script);
		$button_hintergrund = esc_attr( get_option(ps_opt['ps_button_hintergrundfarbe']) );
		echo "Hintergrund: " . $button_hintergrund;
		$button_vordergrund = esc_attr( get_option(ps_opt['ps_button_textfarbe']) );
		$button_hover_hintergrund = esc_attr( get_option(ps_opt['ps_button_hintergrundfarbe_hover']) );
		$button_hover_vordergrund = esc_attr( get_option(ps_opt['ps_button_textfarbe_hover']) );
		$custom_css= "
			a.fuss_button-beitrag {
			    color: {$button_vordergrund}!important;
			    background-color: {$button_hintergrund}!important;
					text-decoration: none!important;
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
/* Start: shortcodes für Fuss
/* Datum: 7.4.2019
/* Autor: hgg
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
		// Ausgabe Linie oberhalb //
		$fs_l_o = esc_attr(get_option($ps_opt['7']));
		if ($fs_l_o == '1') {
			  // echo 'Linie oben: ' . var_dump($l_o); //
				$fs_ausgabe = $fs_ausgabe . '<hr>';
				// echo 'Ausgabe: ' . var_dump($fs_ausgabe); //
		}
		// Pfad aus den Einstellungen holen: //
		$veranstaltungen = esc_url_raw( get_option($ps_opt['6']) );

		// Verwendete Kategorien von TEC
    $kategorien = cliff_get_events_taxonomies();
    // echo 'Kategorien: ' . var_dump($kategorien); //
    if ( trim($werte['link']) != '') {
      $fs_ausgabe = $fs_ausgabe . '<a href=' . $werte['link'] . ' target="_blank">Mehr Infos</a><br>';
    }
		$fs_schriftart_kennzeichen =  esc_attr(get_option($ps_opt['5']));
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
		// Ausgabe des Copyrights //
    $fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz">' . $fs_schriftart_ein  . get_post(get_post_thumbnail_id())->post_excerpt . $fs_schriftart_aus . '</p><br>';
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
    if ( trim($werte['il']) != '') {
       $fs_ausgabe = $fs_ausgabe . '<p class="fuss_button-absatz"><a class="fuss_button-beitrag" href=' . $werte['il'] . ' target="_blank">Mehr Infos auf dieser Seite</a></p>';
    }
		// Ausgabe Linie unterhalb //
		if (esc_attr(get_option($ps_opt['8'])) == 1) {
				$fs_ausgabe = $fs_ausgabe . '<hr>';
		}
	return $fs_ausgabe;
}
add_shortcode('fuss', 'fs_beitrags_fuss_pi');



/*----------------------------------------------------------------*/
/* Ende: shortcodes für Fuss
/* Datum: 7.4.2019
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
	// siehe hier: https://theeventscalendar.com/support/forums/topic/get_terms-with-tribe_events_cat-returning-wp_error-in-functions-php/
	$events_cats = get_terms(array(
		'taxonomy' => 'tribe_events_cat',
		'parent' => 0,
		'hide_empty' => false
	));
	// $events_cats = get_terms( $tecmain::TAXONOMY, $cat_args ); // hat hier nicht funktioniert
	/* $events_cats_names = array(); */
  // echo 'In Funktion: ' . var_dump( $events_cats );  // Anzeige der Kategorien //
	if( ! is_wp_error( $events_cats ) && ! empty( $events_cats ) && is_array( $events_cats) ) {
		$events_cats_names = array();
		foreach( $events_cats as $key => $value ) {
			$events_cats_names[] = $value->name;
		}

	   // echo 'Innerhalb Funktion: ' . var_dump( $events_cats_names );  // Anzeige der Kategorien // //
	}
	return $events_cats_names;
}

/* Umlaute umwandeln, damit z. B. Führung in Fuehrung umgewandelt wird, weil sonst die Kategorieliste nicht gefunden wird. */
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
// Adminbereich mit color-picker konfigurieren
// -------------------------------------------------- //
add_action( 'admin_init', 'einstellungen' );
function einstellungen(){

    register_setting( 'ps_optionen_gruppe', 'ps_opt' );


    add_settings_section(
        'schriftfarben',
        'Schriftfarben',
        'schriftfarben_render',
        'ps_optionen_gruppe'
    );

		add_settings_section(
        'divers',
        'Diverse Angaben',
        'diverse_render',
        'ps_optionen_gruppe'
    );


// Diverse Angaben //

// Auswahl Schriftart für Copyright //
    add_settings_field(
        'ps_sa_ueberschrift',
        'Schriftart für Copyright:',
        'ps_sa_feld_render',
        'ps_optionen_gruppe',
        'divers',
        array( 'id' => 'ps_sa_ueberschrift' )
    );

// Eingabe Pfad
    add_settings_field(
        'ps_pfad',
        'Pfad für Kategorien:',
        'ps_text_feld_render',
        'ps_optionen_gruppe',
        'divers',
        array( 'id' => 'ps_pfad' )
    );

// Checkbox Linie oberhalb
		add_settings_field(
        'ps_linie_oben',
        'Linie oberhalb des Blocks:',
        'ps_checkbox_feld_render',
        'ps_optionen_gruppe',
        'divers',
        array( 'id' => 'ps_linie_oben' )
    );

// Checkbox Linie unterhalb
		add_settings_field(
        'ps_linie_unten',
        'Linie unterhalb des Blocks:',
        'ps_checkbox_feld_render',
        'ps_optionen_gruppe',
        'divers',
        array( 'id' => 'ps_linie_unten' )
    );



// Farben //
    add_settings_field(
        'ps_button_hintergrund',
        'Button Hintergrundfarbe:',
        'ps_sf_feld_render',
        'ps_optionen_gruppe',
        'schriftfarben',
        array( 'id' => 'ps_button_hintergrundfarbe' )
    );
    add_settings_field(
        'ps_button_text',
        'Button Textfarbe:',
        'ps_sf_feld_render',
        'ps_optionen_gruppe',
        'schriftfarben',
        array( 'id' => 'ps_button_textfarbe' )
    );
		add_settings_field(
        'ps_button_hintergrund_hover',
        'Button - Hintergrundfarbe beim Überfahren des Buttons (Hover):',
        'ps_sf_feld_render',
        'ps_optionen_gruppe',
        'schriftfarben',
        array( 'id' => 'ps_button_hintergrundfarbe_hover' )
    );
    add_settings_field(
        'ps_button_text_hover',
        'Button - Textfarbe beim Überfahren des Buttons (Hover):',
        'ps_sf_feld_render',
        'ps_optionen_gruppe',
        'schriftfarben',
        array( 'id' => 'ps_button_textfarbe_hover' )
    );
}


function diverse_render(){
    ?>
    <p>Angaben für Pfad, Schriftart beim Copyright und Linienwahl</p>
    <?php
}

function schriftfarben_render(){
    ?>
    <p>Wählen Sie bitte die Schriftfarben aus.</p>
    <?php
}


// Funktionen für Eingabefelder

// Funktion für Eingabe Textfeld //
function ps_text_feld_render( $args ){
		$option = get_option( 'ps_opt' );
    ?>
    <input type="text" name="ps_opt[<?php echo $args['id']; ?>]" value="<?php echo $option[ $args['id'] ]; ?>" />
    <?php
}


// Funktion für Checkboxen //
function ps_checkbox_feld_render( $args ){
		$option = get_option( 'ps_opt' );
    ?>
		<input type="checkbox" name="ps_opt[<?php echo $args['id']; ?>]" value="1" <?php checked( isset($option[ $args['id']]), 1 ); ?>" />
    <?php
}


// Funktion für Auswahl Schriftart //
function ps_sa_feld_render( $args ){
    $option = get_option( 'ps_opt' );
    $schriftarten = array(
        'kursiv',
        'fett',
        'normal'
    );
    ?>
    <select name="ps_opt[<?php echo $args['id']; ?>]">
        <?php foreach( $schriftarten as $s ): ?>
            <option <?php
            selected( $option[ $args['id'] ], $s );
            ?>><?php echo $s; ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}


// Funktion für Auswahl Farbe //
function ps_sf_feld_render( $args ){
    $option = get_option( 'ps_opt' );
    ?>
    <input name="ps_opt[<?php echo $args['id']; ?>]" value="<?php
    echo $option[ $args['id'] ];
    ?>" class="color" />
    <?php
}

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


// Menü einrichten
add_action( 'admin_menu', 'options_menu' );
function options_menu() {
    add_submenu_page(
        'options-general.php',
        'Post-Shortcode-Einstellungen',
        'Post-Shortcode-Einstellungen',
        'manage_options',
        'optionsseite',
        'options_page'
    );
}

function options_page(){
    ?>
    <form action='options.php' method='post'>
        <h1>Post-Shortcode-Einstellungen</h1>
        <?php
        settings_fields( 'ps_optionen_gruppe' );
        do_settings_sections( 'ps_optionen_gruppe' );
        submit_button();
        ?>
    </form>
    <?php
}


/*
add_action( 'admin_menu', 'fs1_wpadminpage' );
function fs1_wpadminpage() {
    add_menu_page(
        'Neuer Punkt',
        'Neuer Punkt',
        'edit_posts',
        'np',
        'fs1_np_output'
    );
}

function fs1_np_output(){
    ?>
    <div class="wrap">
        <h1>Einstellungen</h1>
    </div>
		<?php
}



add_action( 'fs1_customize_register', 'fs1_customizer' );

function fs1_customizer( $wp_customize ){
    $wp_customize->add_section(
        'fs1_einstellungen',
        array(
            'title' => 'Einstellungen',
            'priority' => 30,
            'description' => 'Einstellungen für das plugin',
        )
    );

		$wp_customize->add_setting(
        'fs1_hintergrundfarbe',
        array(
            'default' => '#000'
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'fs1_hintergrundfarbe',
            array(
                'label'    => 'Hintergrundfarbe für den Button',
                'section'  => 'fs1_einstellungen',
                'settings' => 'fs1_hintergrundfarbe',
            )
        )
    );
}
*/



/*
// -------------------------------------------------- //
// Adminbereich konfigurieren
// -------------------------------------------------- //
function fs_einstellungen_initialisieren() {
// Einstellungen registrieren
register_setting(
    'fs_einstellungen',
    'fs_einstellungen_allgemein',
    [
    'sanitize_callback' =>
    'fs_feld_pfad_anzeigen_sani_cb'
    ]
);
// Neue Sektion "Allgemeine Einstellungen" registrieren
add_settings_section(
    'fs_einstellungen_sektion_allgemein',
    __('Allgemeine Einstellungen', 'textdomaine'),
    'fs_einstellungen_sektion_allgemein_callback',
    'fs_einstellungen_seite'
);
// Neues Feld in der Sektion registrieren
add_settings_field(
    'fs_feld_pfad_anzeigen',
    __( 'Pfad für Veranstaltungen angeben', 'textdomaine' ),
    'fs_feld_pfad_anzeigen_callback',
    'fs_einstellungen_seite',
    'fs_einstellungen_sektion_allgemein',
    [
    'label_for' => 'fs_einstellung_pfad_anzeigen',
    ]
);
}
// Funktion an den "admin_init"-Hook hängen
add_action(
    'admin_init',
    'fs_einstellungen_initialisieren'
);

function fs_einstellungen_sektion_allgemein_callback($args) {
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

function fs_feld_pfad_anzeigen_callback( $args ) {
  // Registrierte Einstellungen laden
  $options = get_option( 'fs_einstellungen_allgemein' );
  // Feld ausgeben
  $id = esc_attr( $args['label_for'] );
  ?>
  <input type="text"
  id="<?php echo $id; ?>"
  name="fs_einstellungen_allgemein[<?php echo $id;
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

function fs_settings_page() {
  // add top level menu page
  add_options_page(
  'Posts-Shortcodes',
  'Posts-Shortcodes',
  'manage_options',
  'fs_einstellungen_seite',
  'fs_einstellungen_seite_html'
  );
  }
  // Menü-Funktion an den Action-Hook "admin_menu" hängen
  add_action( 'admin_menu', 'fs_settings_page' );

function fs_einstellungen_seite_html() {
  // User-Zugang prüfen
  if ( ! current_user_can( 'manage_options' ) )
    return;
  ?>
  <div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form action="options.php" method="post">
    <?php
    // Ausgabe Zusatzdaten:
    settings_fields( 'fs_einstellungen' );
    // Ausgabe Sektionen und Felder
    do_settings_sections( 'fs_einstellungen_seite' );
    // Ausgabe des Submit-Buttons
    submit_button();
    ?>
    </form>
  </div>
  <?php
  }

// Callback: Einstellung absichern
function fs_feld_pfad_anzeigen_sani_cb($data) {
  if (isset($data['fs_feld_pfad_anzeigen']) && 1 != $data['fs_feld_pfad_anzeigen']) {
		echo 'hier bin ich';
    add_settings_error(
    'requiredpfad',
    'empty',
    'Pfad muss eingegeben werden (es wird nicht geprüft, ob der Pfad korrekt ist).',
    'error'
    );
    unset( $data['fs_feld_pfad_anzeigen'] );
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

*/
// -------------------------------------------------- //
// Neues Dashboard-Widget hinzufügen
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
// Adminbereich hinzugefügt
// -------------------------------------------------- //

	// create custom plugin settings menu
	// aktivieren, um die Einstellungen zu zeigen:
	// add_action('admin_menu', 'post_shortcode_create_menu');

	function post_shortcode_create_menu() {

		//create new top-level menu: add_menu_page
		// add_submenu_page('Post Shortcode Plugin Settings', 'Post Shortcode Settings', 'administrator', __FILE__, 'post_shortcode_settings_page' , plugins_url('/images/icon.png', __FILE__) );
		// funktioniert jetzt:
		add_options_page( 'Post Shortcode Plugin Settings', 'Post Shortcode Settings', 'manage_options', 'post_shortcode_settings_page', 'post_shortcode_settings_page');
		//call register settings function
		add_action( 'admin_init', 'register_post_shortcode_settings' );
	}


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
	        <td><input type="text" name="fs_option_pfad" value="<?php echo esc_url_raw( get_option('fs_option_pfad') ); ?>" /></td>
	        </tr>

	        <tr valign="top">

					<?php
					// Control ID fs_hintergrundfarbe_button //
					/*
						$wp_customize->add_setting(
							'fs_hintergrundfarbe_button', array ('default' => '#000')
						);
						$wp_customize->add_control(
								new WP_Customize_Color_Control( $wp_customize, 'fs_hintergrundfarbe_button_control', array(
										'label'    => __( 'Button - Hintergrundfarbe', 'text-domain' ),
										'settings' => 'fs_hintergrundfarbe_button',
										'section'  => 'post-shortcode-settings-group',
									) )
								);
					*/
					?>

					<th scope="row">Button - Hintergrundfarbe</th>
	        <td><input type="text" name="fs_hintergrundfarbe_button" value="<?php echo esc_attr( get_option('fs_hintergrundfarbe_button') ); ?>" /></td>
	        </tr>


	        <tr valign="top">
	        <th scope="row">Button - Schriftfarbe</th>
	        <td><input type="text" name="fs_vordergrundfarbe_button" value="<?php echo esc_attr( get_option('fs_vordergrundfarbe_button') ); ?>" /></td>
	        </tr>

					<tr valign="top">
	        <th scope="row">Button - Hintergrundfarbe beim Überfahren des Buttons (Hover)</th>
	        <td><input type="text" name="fs_hover_hintergrundfarbe_button" value="<?php echo esc_attr( get_option('fs_hover_hintergrundfarbe_button') ); ?>" /></td>
	        </tr>

					<tr valign="top">
	        <th scope="row">Button - Schriftfarbe beim Überfahren des Buttons (Hover)</th>
	        <td><input type="text" name="fs_hover_vordergrundfarbe_button" value="<?php echo esc_attr( get_option('fs_hover_vordergrundfarbe_button') ); ?>" /></td>
	        </tr>

					<tr valign="top">
	        <th scope="row">Schriftart für Copyright</th>
	        <td><input type="radio" name="fs_schriftart" value="1" <?php checked(1, get_option('fs_schriftart'), true); ?>>kursiv
					<input type="radio" name="fs_schriftart" value="2" <?php checked(2, get_option('fs_schriftart'), true); ?>>fett
					<input type="radio" name="fs_schriftart" value="3" <?php checked(3, get_option('fs_schriftart'), true); ?>>normal</td>
	        </tr>

					<tr valign="top">
	        <th scope="row">Horizontale Linie oberhalb des Blocks</th>
	        <td><input type="checkbox" name="fs_linie_oben" value='1' <?php checked(get_option('fs_linie_oben'), 1); ?> />
	        </tr>

					<tr valign="top">
	        <th scope="row">Horizontale Linie unterhalb des Blocks</th>
	        <td><input type="checkbox" name="fs_linie_unten" value='1' <?php checked(get_option('fs_linie_unten'), 1); ?> />
	        </tr>

	    </table>

	    <?php submit_button(); ?>

	</form>
	</div>
<?php
}
?>
