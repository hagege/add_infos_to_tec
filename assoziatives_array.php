<?php

// folgende Zeilen ab 381:
if ( get_option('add_infos_to_tec_settings['fs_option_pfad']') == false ) {
    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
    $deprecated = null;
    $autoload = 'no';
    add_option( 'add_infos_to_tec_settings['fs_option_pfad']', 'http://beispielseite.de/events/category/', $deprecated, $autoload );
    add_option( 'add_infos_to_tec_settings['fs_hintergrundfarbe_button']', '#77BCC7', $deprecated, $autoload );
    add_option( 'add_infos_to_tec_settings['fs_vordergrundfarbe_button']', '#ffffff', $deprecated, $autoload );
    add_option( 'add_infos_to_tec_settings['fs_hover_hintergrundfarbe_button']', '#F9B81E', $deprecated, $autoload );
    add_option( 'add_infos_to_tec_settings['fs_hover_vordergrundfarbe_button']', '#ffffff', $deprecated, $autoload );
    add_option( 'add_infos_to_tec_settings['fs_runder_button']', '5', $deprecated, $autoload );
    add_option( 'add_infos_to_tec_settings['fs_alle_buttons']', '0', $deprecated, $autoload );
    add_option( 'add_infos_to_tec_settings['fs_schriftart']', '1', $deprecated, $autoload );
    add_option( 'add_infos_to_tec_settings['fs_linie_oben']', '1', $deprecated, $autoload );
    add_option( 'add_infos_to_tec_settings['fs_linie_unten']', '0', $deprecated, $autoload );
}



?>
