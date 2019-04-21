<?php
    header("Content-type: text/css; charset: UTF-8");

    $button_hintergrund = esc_attr( get_option('hintergrundfarbe_button') );
		$button_vordergrund = esc_attr( get_option('vordergrundfarbe_button') );
		$button_hover_hintergrund = esc_attr( get_option('hover_hintergrundfarbe_button') );
		$button_hover_vordergrund = esc_attr( get_option('hover_vordergrundfarbe_button') );
    echo 'hintergrund (eingelesen):' . var_dump($button_hintergrund)
?>

/* Absatz für Button im shortcode fuss*/
p.fuss_button-absatz {
    margin:30px 10px 30px 0px !important;
    display:inline-flex;
}

a.fuss_button-beitrag {
    padding: 10px;
    color: <?php echo $button_vordergrund; ?>!important;
    font-size: 1.0em;
    background-color: <?php echo $button_hintergrund; ?>!important;
    /* keine runden Ecken */
    /* border-radius: 4px; */
    text-decoration: none!important;
}

@media only screen and (min-width: 768px) and (max-width: 840px) {
  .fuss_button-beitrag {
      font-size: 0.8em;
  }
}


a.fuss_button-beitrag:focus{
  color: #fff!important;
  text-decoration: none;
}

a.fuss_button-beitrag:visited{
  color: #fff!important;
  text-decoration: none;
}


/* Hintergrund: gelb */
a.fuss_button-beitrag:hover{
  color: <?php echo $button_hover_vordergrund; ?>!important;
  background-color: <?php echo $button_hover_hintergrund; ?>!important;
}


/* Tabelle für Einstellungen */
table.einstellungen {
  width:100%;
}
td.einstellungen {
  border:1px solid;
  overflow:hidden;
  vertical-align:top;
}
