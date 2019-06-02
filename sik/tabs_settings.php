<?php
  function ait_admin_settings_setup() {
	     add_options_page('My Plugin Settings', 'My Plugin Settings', 'manage_options', 'sd-settings', 'ait_admin_settings_page');
  }
  add_action('admin_menu', 'ait_admin_settings_setup');

  function ait_admin_settings_page(){
    	global $ait_active_tab;
    	$ait_active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'welcome'; ?>
     
    	<h2 class="nav-tab-wrapper">
    	<?php
    		do_action( 'ait_settings_tab' );
    	?>
    	</h2>
    	<?php	do_action( 'ait_settings_content' );
  }
  
  add_action( 'ait_settings_tab', 'ait_welcome_tab', 1 );

  function ait_welcome_tab(){
    	global $ait_active_tab; ?>
    	<a class="nav-tab <?php echo $ait_active_tab == 'welcome' || '' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'options-general.php?page=sd-settings&tab=welcome' ); ?>"><?php _e( 'Welcome', 'sd' ); ?> </a>
    	<?php
  }
  
  add_action( 'ait_settings_content', 'ait_welcome_render_options_page' );
 
  function ait_welcome_render_options_page() {
  	global $ait_active_tab;
  	if ( '' || 'welcome' != $ait_active_tab )
  		return;
  	?>
   
  	<h3><?php _e( 'Welcome', 'sd' ); ?></h3>
  	<!-- Put your content here -->
  	<?php
  }
  add_action( 'ait_settings_tab', 'ait_another_tab' );

  function ait_another_tab(){
  	global $ait_active_tab; ?>
  	<a class="nav-tab <?php echo $ait_active_tab == 'another-tab' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'options-general.php?page=sd-settings&tab=another-tab' ); ?>"><?php _e( 'Another Title', 'sd' ); ?> </a>
  	<?php
  }
 
  add_action( 'ait_settings_content', 'ait_another_render_options_page' );
 
  function ait_another_render_options_page() {
  	global $ait_active_tab;
  	if ( 'another-tab' != $ait_active_tab )
  		return;
  	?>
   
  	<h3><?php _e( 'Another Tab Content', 'sd' ); ?></h3>
  	<!-- Put your content here -->
  	<?php
  }
?>