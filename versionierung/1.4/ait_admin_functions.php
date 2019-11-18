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



// page with settings
	function ait_add_infos_to_tec_settings_page() {
	?>
	<div class="wrap">
	<h1>Add Infos to The Events Calendar - Version: <?php echo AIT_VERSION ?></h1>
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
			    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
			    $deprecated = null;
			    $autoload = 'no';
					$tec_path = ait_path_for_tec();
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
							'fs_linie_unten' => '0',
							'fs_bezeichnung_externer_link' => 'Read More',
							'fs_bezeichnung_events_link' => 'Read More',
							'fs_bezeichnung_interner_link' => 'Read More on this website',
						);
					add_option( 'add_infos_to_tec_settings', $add_infos_to_tec_options, $deprecated, $autoload);
			}
			$add_infos_to_tec_options = ait_test_array($add_infos_to_tec_options);
			?>
	    <table class="form-table">
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
					<th scope="row"><h3><?php echo __( 'Settings for Buttons:', 'add-infos-to-the-events-calendar' ); ?></h3></th>


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
					<th scope="row"><h3><?php echo __( 'Further settings:', 'add-infos-to-the-events-calendar' ); ?></h3></th>

					<tr valign="top">
					<th scope="row"><?php echo __( 'Rounded corners (values from 0 - 30):', 'add-infos-to-the-events-calendar' ); ?></th>
					<td><input type="number" min="0" max="30" step="1" name="add_infos_to_tec_settings[fs_runder_button]" size=2 value="<?php echo esc_attr( $add_infos_to_tec_options['fs_runder_button']); ?>" /></td>
					</tr>


					<tr valign="top">
	        <th scope="row"><?php echo __( 'All links as buttons:', 'add-infos-to-the-events-calendar' ); ?></th>
	        <td><input type="checkbox" name="add_infos_to_tec_settings[fs_alle_buttons]" value="1" <?php echo esc_attr(checked($add_infos_to_tec_options['fs_alle_buttons'], 1, true)); ?> />
	        </tr>

					<!-- Diverses -->
					<tr valign="top">
	        <th scope="row"><?php echo __( 'Font for Copyright:', 'add-infos-to-the-events-calendar' ); ?></th>
	        <td><input type="radio" name="add_infos_to_tec_settings[fs_schriftart]" value="1" <?php echo esc_attr(checked(1, $add_infos_to_tec_options['fs_schriftart'], true)); ?>><?php echo __( 'italic', 'add-infos-to-the-events-calendar' ); ?>
					<input type="radio" name="add_infos_to_tec_settings[fs_schriftart]" value="2" <?php echo esc_attr(checked(2, $add_infos_to_tec_options['fs_schriftart'], true)); ?>><?php echo __( 'bold', 'add-infos-to-the-events-calendar' ); ?>
					<input type="radio" name="add_infos_to_tec_settings[fs_schriftart]" value="3" <?php echo esc_attr(checked(3, $add_infos_to_tec_options['fs_schriftart'], true)); ?>><?php echo __( 'normal', 'add-infos-to-the-events-calendar' ); ?></td>
	        </tr>

					<tr valign="top">
	        <th scope="row"><?php echo __( 'Horizontal line above the block:', 'add-infos-to-the-events-calendar' ); ?></th>
	        <td><input type="checkbox" name="add_infos_to_tec_settings[fs_linie_oben]" value="1" <?php echo esc_attr(checked($add_infos_to_tec_options['fs_linie_oben'], 1, true)); ?> />
	        </tr>

					<tr valign="top">
	        <th scope="row"><?php echo __( 'Horizontal line below the block:', 'add-infos-to-the-events-calendar' ); ?></th>
	        <td><input type="checkbox" name="add_infos_to_tec_settings[fs_linie_unten]" value="1" <?php echo esc_attr(checked($add_infos_to_tec_options['fs_linie_unten'], 1, true)); ?> />
	        </tr>

	    </table>
			<?php
			submit_button();
 		 ?>
			</form>
	</div>
	<?php
}
	// -------------------------------------------------- //
	// End: admin area
	// -------------------------------------------------- //
?>
