<?php
// -------------------------------------------------- //
// Start: admin area
// -------------------------------------------------- //

// WP Color Picker
	add_action( 'admin_enqueue_scripts', 'ait_farbwaehler_laden' );
	function ait_farbwaehler_laden( $hook ) {
	    wp_enqueue_style( 'wp-color-picker' );
	    wp_enqueue_script(
	        'color-script',
	        plugins_url( 'assets/js/ait_script.js', __FILE__ ),
	        array( 'wp-color-picker' ),
	        false,
	        true
	    );
	}



	add_action('admin_menu', 'ait_add_infos_to_tec_create_menu');

// create custom plugin settings menu
	function ait_add_infos_to_tec_create_menu() {

		//create new top-level menu: add_menu_page
		add_submenu_page('Add Infos to TEC Plugin Settings',  __('Add Infos to TEC Settings', 'add-infos-to-the-events-calendar'), 'administrator', __FILE__, 'ait_admin_settings_page' , plugins_url('/images/icon.png', __FILE__) );
		add_options_page('Add Infos to TEC Plugin Settings',  __('Add Infos to TEC Settings', 'add-infos-to-the-events-calendar'), 'manage_options', 'ait-settings', 'ait_admin_settings_page');
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
		$settings_link = '<a href="options-general.php?page=ait_admin_settings_page">'	. __( 'Settings' ) . '</a>';
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
		//
		register_setting( 'add_infos_to_tec_settings-group_tab_2', 'add_infos_to_tec_settings_tab_2' );
	}



	// Determine path for events and suggest as path if necessary
	function ait_path_for_tec(){
	if ( !ait_tec_installed() ) {
			// The Events Calendar is not installed, therefore:
			$ait_path = "http://beispielseite.de/events/category/";
		} else {
			$ait_path = esc_url( tribe_get_listview_link() );
			// delete last "/":
			$ait_path = substr($ait_path,0,strlen($ait_path)-1);
			$tec_category = __( 'category', 'the-events-calendar' );
			$tec_category = strtolower($tec_category);
			// show the path without the kind of view:
			$ait_path = substr($ait_path,0,strrpos($ait_path, '/')) . '/' . $tec_category . '/';
			// echo $ait_path .'<br>';
		}
		return $ait_path;
	}



// -------------------------------------------------- //
// Begin: admin area
// -------------------------------------------------- //

function ait_set_variablen_tab_1() {
	// The option hasn't been added yet. We'll add it with $autoload set to 'no'.
	$deprecated = null;
	$autoload = 'no';
	$tec_path = ait_path_for_tec();
	$add_infos_to_tec_options = array(
			'fs_option_pfad' => $tec_path,
			'fs_hintergrundfarbe_button' => '#1E73BE',
			'fs_vordergrundfarbe_button' => '#ffffff',
			'fs_hover_hintergrundfarbe_button' => '#F9B81E',
			'fs_hover_vordergrundfarbe_button' => '#ffffff',
			'fs_runder_button' => '5',
			'fs_bezeichnung_externer_link' => 'Read More',
			'fs_bezeichnung_events_link' => 'Read More',
			'fs_bezeichnung_interner_link' => 'Read More on this website',
		);
	add_option( 'add_infos_to_tec_settings', $add_infos_to_tec_options, $deprecated, $autoload);
	return ($add_infos_to_tec_options);
}


function ait_set_variablen_tab_2() {
	// The option hasn't been added yet. We'll add it with $autoload set to 'no'.
	$deprecated = null;
	$autoload = 'no';
	$tec_path = ait_path_for_tec();
	$add_infos_to_tec_options_tab_2 = array(
			'fs_alle_buttons' => '0',
			'fs_schriftart' => '1',
			'fs_linie_oben' => '1',
			'fs_linie_unten' => '0',
		);
	add_option( 'add_infos_to_tec_settings_tab_2', $add_infos_to_tec_options_tab_2, $deprecated, $autoload);
	return ($add_infos_to_tec_options_tab_2);
}

  function ait_admin_settings_page(){
    	global $ait_active_tab;
    	$ait_active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'settings'; ?>

    	<h2 class="nav-tab-wrapper">
    	<?php
    		do_action( 'ait_settings_tab' );
    	?>
    	</h2>
    	<?php	do_action( 'ait_settings_content' );
  }

  add_action( 'ait_settings_tab', 'ait_settings_tab', 1 );

  function ait_settings_tab(){
    	global $ait_active_tab; ?>
    	<a class="nav-tab <?php echo $ait_active_tab == 'settings' || '' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'options-general.php?page=ait-settings&tab=settings' ); ?>"><?php _e( 'Settings for Buttons', 'sd' ); ?> </a>
    	<?php
  }

  add_action( 'ait_settings_content', 'ait_settings_render_options_page' );

  function ait_settings_render_options_page() {
  	global $ait_active_tab;
  	if ( '' || 'settings' != $ait_active_tab )
  		return;
  	?>

  	<!-- Put your content here -->
		<div class="wrap">
		<h1>Add Infos to The Events Calendar - Version: <?php echo AIT_VERSION ?></h1>
		<hr>
		<h3><?php _e( 'Settings for Buttons', 'add-infos-to-the-events-calendar' ); ?></h3>
		<hr>
		<form method="post" action="options.php">
				<?php
				settings_fields( 'add_infos_to_tec_settings-group' );
				do_settings_sections( 'add_infos_to_tec_settings-group' );
				// get plugin options from the database
				$add_infos_to_tec_options = get_option( 'add_infos_to_tec_settings' );
				// Check that user has proper security level
				if ( !current_user_can( 'manage_options' ) ){
					 wp_die( __('You do not have permissions to perform this action', 'add-infos-to-the-events-calendar') );
				}
				// security (nonce) //
				wp_nonce_field('ait_plugin_settings_link', 'ait_tec');
				// $nonce_field = wp_nonce_field('ait_plugin_settings_link', 'ait_tec');
				// Set options if the options do not yet exist
				if (empty( $add_infos_to_tec_options)) {
					$add_infos_to_tec_options = ait_set_variablen_tab_1();
				}
				$add_infos_to_tec_options = ait_test_array($add_infos_to_tec_options);
				?>
				<table class="form-table">
						<!-- <?php var_dump ($add_infos_to_tec_options); ?> -->
						<!-- path-->
						<tr valign="top">
						<?php
						$tec_path= ait_path_for_tec();
						echo __( 'This could be the path to the categories of The Events Calendar (TEC): ', 'add-infos-to-the-events-calendar' ) . '<font color="#FF0000"><strong>' . $tec_path . '</strong></font><br />';
						echo __( 'To be on the safe side, however, you should check this by going to the relevant event after using the shortcut and checking that the links are executed correctly.', 'add-infos-to-the-events-calendar' );
						?>
						<!-- here I want to check if a folder exists in further versions of plugin -->
						<hr>
						<th scope="row"><?php echo __( 'Path e.g. categories to The Events Calendar (e.g. http://example.com/events/category/):', 'add-infos-to-the-events-calendar' ); ?></th>

						<td><input type="text" name="add_infos_to_tec_settings[fs_option_pfad]" size=50 value="<?php echo esc_url_raw( $add_infos_to_tec_options['fs_option_pfad']); ?>" /></td>
						</tr>

						<!-- Buttons -->

						<tr valign="top">
						<th scope="row"><?php echo __( 'Background color:', 'add-infos-to-the-events-calendar' ); ?></th>
						<td><input type="text" name="add_infos_to_tec_settings[fs_hintergrundfarbe_button]" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_hintergrundfarbe_button']); ?>" class="color" /></td>
						</tr>


						<tr valign="top">
						<th scope="row"><?php echo __( 'Font color:', 'add-infos-to-the-events-calendar' ); ?></th>
						<td><input type="text" name="add_infos_to_tec_settings[fs_vordergrundfarbe_button]" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_vordergrundfarbe_button']); ?>" class="color" /></td>
						</tr>

						<tr valign="top">
						<th scope="row"><?php echo __( 'Background color when driving over the button (Hover):', 'add-infos-to-the-events-calendar' ); ?></th>
						<td><input type="text" name="add_infos_to_tec_settings[fs_hover_hintergrundfarbe_button]" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_hover_hintergrundfarbe_button']); ?>" class="color" /></td>
						</tr>

						<tr valign="top">
						<th scope="row"><?php echo __( 'Font color when driving over the button (Hover):', 'add-infos-to-the-events-calendar' ); ?></th>
						<td><input type="text" name="add_infos_to_tec_settings[fs_hover_vordergrundfarbe_button]" value="<?php echo esc_attr( $add_infos_to_tec_options['fs_hover_vordergrundfarbe_button']); ?>" class="color" /></td>
						</tr>

						<!--  caption for buttons - 12.05.2019: -->
						<tr valign="top">
						<th scope="row"><?php echo __( 'Description for external link:', 'add-infos-to-the-events-calendar' ); ?></th>
						<td><input type="text" name="add_infos_to_tec_settings[fs_bezeichnung_externer_link]" size=30 value="<?php echo esc_attr( $add_infos_to_tec_options['fs_bezeichnung_externer_link']); ?>" /></td>
						</tr>

						<tr valign="top">
						<th scope="row"><?php echo __( 'Description for events:', 'add-infos-to-the-events-calendar' ); ?></th>
						<td><input type="text" name="add_infos_to_tec_settings[fs_bezeichnung_events_link]" size=30 value="<?php echo esc_attr( $add_infos_to_tec_options['fs_bezeichnung_events_link']); ?>" /></td>
						</tr>

						<tr valign="top">
						<th scope="row"><?php echo __( 'Description for internal link:', 'add-infos-to-the-events-calendar' ); ?></th>
						<td><input type="text" name="add_infos_to_tec_settings[fs_bezeichnung_interner_link]" size=30 value="<?php echo esc_attr( $add_infos_to_tec_options['fs_bezeichnung_interner_link']); ?>" /></td>
						</tr>

						<tr valign="top">
						<th scope="row"><?php echo __( 'Rounded corners (values from 0 - 30):', 'add-infos-to-the-events-calendar' ); ?></th>
						<td><input type="number" min="0" max="30" step="1" name="add_infos_to_tec_settings[fs_runder_button]" size=2 value="<?php echo esc_attr( $add_infos_to_tec_options['fs_runder_button']); ?>" /></td>
						</tr>

				</table>
				<?php
				// Saving only array $add_infos_to_tec_options //
				$ait_save = __( 'Save Settings', 'add-infos-to-the-events-calendar' );
				submit_button($ait_save, 'primary', 'ait_settings', true, $add_infos_to_tec_options);
			 ?>
				</form>
		</div>
		<?php
  }

	// -------------------------------------------------- //
	// Admin Area: Tab2 //
	// -------------------------------------------------- //

  add_action( 'ait_settings_tab', 'ait_further_settings' );

  function ait_further_settings(){
  	global $ait_active_tab; ?>
  	<a class="nav-tab <?php echo $ait_active_tab == 'further_settings' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'options-general.php?page=ait-settings&tab=further_settings' ); ?>"><?php _e( 'Further settings', 'add-infos-to-the-events-calendar' ); ?> </a>
  	<?php
  }

  add_action( 'ait_settings_content', 'ait_options_page_tab_2' );

  function ait_options_page_tab_2() {
  	global $ait_active_tab;
  	if ( 'further_settings' != $ait_active_tab )
  		return;
  	?>

  	<h3><?php _e( 'Further settings', 'add-infos-to-the-events-calendar' ); ?></h3>
		<div class="wrap">
		<hr>

		<form method="post" action="options.php">
				<?php
				settings_fields( 'add_infos_to_tec_settings-group_tab_2' );
				do_settings_sections( 'add_infos_to_tec_settings-group_tab_2' );
				// get plugin options from the database
				$add_infos_to_tec_options_tab_2 = get_option( 'add_infos_to_tec_settings_tab_2' );
				// save array
				// Check that user has proper security level
				if ( !current_user_can( 'manage_options' ) ){
					 wp_die( __('You do not have permissions to perform this action', 'add-infos-to-the-events-calendar') );
				}
				// security (nonce) //
				wp_nonce_field('ait_plugin_settings_link', 'ait_tec');
				// $nonce_field = wp_nonce_field('ait_plugin_settings_link', 'ait_tec');
				// Set options if the options do not yet exist
				if (empty( $add_infos_to_tec_options_tab_2)) {
						$add_infos_to_tec_options_tab_2 = ait_set_variablen_tab_2();
				}
				$add_infos_to_tec_options_tab_2 = ait_test_array_tab_2($add_infos_to_tec_options_tab_2);
				?>

				<table class="form-table">
						<!-- <?php var_dump ($add_infos_to_tec_options_tab_2); ?> -->

						<tr valign="top">
						<th scope="row"><?php echo __( 'All links as buttons:', 'add-infos-to-the-events-calendar' ); ?></th>
						<td><input type="checkbox" name="add_infos_to_tec_settings_tab_2[fs_alle_buttons]" value="1" <?php echo esc_attr(checked($add_infos_to_tec_options_tab_2['fs_alle_buttons'], 1, true)); ?> />
						</tr>

						<!-- Diverses -->
						<tr valign="top">
						<th scope="row"><?php echo __( 'Font for Copyright:', 'add-infos-to-the-events-calendar' ); ?></th>
						<td><input type="radio" name="add_infos_to_tec_settings_tab_2[fs_schriftart]" value="1" <?php echo esc_attr(checked(1, $add_infos_to_tec_options_tab_2['fs_schriftart'], true)); ?>><?php echo __( 'italic', 'add-infos-to-the-events-calendar' ); ?>
						<input type="radio" name="add_infos_to_tec_settings_tab_2[fs_schriftart]" value="2" <?php echo esc_attr(checked(2, $add_infos_to_tec_options_tab_2['fs_schriftart'], true)); ?>><?php echo __( 'bold', 'add-infos-to-the-events-calendar' ); ?>
						<input type="radio" name="add_infos_to_tec_settings_tab_2[fs_schriftart]" value="3" <?php echo esc_attr(checked(3, $add_infos_to_tec_options_tab_2['fs_schriftart'], true)); ?>><?php echo __( 'normal', 'add-infos-to-the-events-calendar' ); ?></td>
						</tr>

						<tr valign="top">
						<th scope="row"><?php echo __( 'Horizontal line above the block:', 'add-infos-to-the-events-calendar' ); ?></th>
						<td><input type="checkbox" name="add_infos_to_tec_settings_tab_2[fs_linie_oben]" value="1" <?php echo esc_attr(checked($add_infos_to_tec_options_tab_2['fs_linie_oben'], 1, true)); ?> />
						</tr>

						<tr valign="top">
						<th scope="row"><?php echo __( 'Horizontal line below the block:', 'add-infos-to-the-events-calendar' ); ?></th>
						<td><input type="checkbox" name="add_infos_to_tec_settings_tab_2[fs_linie_unten]" value="1" <?php echo esc_attr(checked($add_infos_to_tec_options_tab_2['fs_linie_unten'], 1, true)); ?> />
						</tr>

				</table>
				<?php
				$ait_save = __( 'Save Settings', 'add-infos-to-the-events-calendar' );
				// Saving only array $add_infos_to_tec_options_tab_2 //
				submit_button($ait_save, 'primary','ait_settings_tab_2', true, $add_infos_to_tec_options_tab_2);
			 ?>
 			 </form>
		</div>

  	<?php
  }
	// -------------------------------------------------- //
	// End: admin area
	// -------------------------------------------------- //
?>
