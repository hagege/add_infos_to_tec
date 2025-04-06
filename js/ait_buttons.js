document.addEventListener("DOMContentLoaded", function() {
  let ait_http = 'http://';
  let ait_https = 'https://';
  let tec_installed = ait_php_var.ait_tec_installed;
  let cat_type = 'textbox';

  // TEC is installed, so more options in menu
  if (tec_installed === "true") {
    cat_type = 'listbox';
     /* Register the buttons */
     tinymce.PluginManager.add( 'ait_button_script', function( ed, url ) {
          ed.addButton( 'ait_button', {
            title : ait_php_var.mouseover_title,
            type: 'menubutton',
            image : url + '/icons8-kalender-48.png',
            icon: 'icons8-kalender-48.png',
              menu:[{
                  text: ait_php_var.mouseover_title,
                  value: ait_php_var.mouseover_title,
                  onclick : function() {
                      ed.windowManager.open( {
                         title: ait_php_var.window_title,
                         body: [
                              {
                                type: 'textbox',
                                name: 'link',
                                label: ait_php_var.external_link,
                                value:""
                              },
                              {
                                type: cat_type,
                                name: 'vl',
                                label: ait_php_var.event_category,
                                values: ait_php_var.ait_categories
                              },
                              {
                                type: 'textbox',
                                name: 'il',
                                label: ait_php_var.internal_link,
                                values:""
                              },
                              // -------------------------------------------------- //
                              /* for internal use only */
                              // -------------------------------------------------- //
                              /*
                              {
                                type: 'checkbox',
                                name: 'kfm',
                                label: 'Kinderflohmärkte:',
                                values: ""
                              },
                              {
                                type: 'checkbox',
                                name: 'fm',
                                label: 'Flohmärkte:',
                                values: ""
                              },
                              {
                                type: 'checkbox',
                                name: 'ferien',
                                label: 'Ferien:',
                                values: ""
                              },
                              */

        ],
        onsubmit: function( e ) {
          if (e.data.vl === "all") {
            e.data.vl = "";
          }
          e.data.vl = 'vl="' + e.data.vl + '" ';

          /* test whether the link starts with http:// or https://, otherwise add http:// if necessary */
          if (e.data.link !== '') {
            if (e.data.link.substring(0, 7) !== ait_http && e.data.link.substring(0, 8) !== ait_https){
              e.data.link = ait_http + e.data.link;
            }
            e.data.link = 'link="' + e.data.link + '" ';
          }
          else {
            e.data.link ="";
          }
          if (e.data.il !== '') {
            if (e.data.il.substring(0, 7) !== ait_http && e.data.il.substring(0, 8) !== ait_https){
              e.data.il = ait_http + e.data.il;
            }
            e.data.il = 'il="' + e.data.il + '" ';
          }
          else {
            e.data.il ="";
          }

          /* for internal use only */
          e.data.kfm_var = '';
          if (e.data.kfm === true) {
              e.data.kfm_var = ' kfm="" ';
          }
          e.data.fm_var = '';
          if (e.data.fm === true) {
              e.data.fm_var = ' fm="" ';
          }
          e.data.ferien_var = '';
          if (e.data.ferien === true) {
              e.data.ferien_var = ' ferien="" ';
          }
          /* only for internal use */

          ed.insertContent(
            /* build shortcode */
            /* '[fuss link="' + e.data.link + '" vl="' + e.data.vl + '" il="' + e.data.il+ '"]' */
            '[fuss ' + e.data.link + e.data.vl + e.data.il + e.data.kfm_var + e.data.fm_var + e.data.ferien_var + ']'
          );
        }
      });
          }
      }]

    });
  });
}
// TEC is NOT installed, so less options in menu
else {
  /* Register the buttons */
  tinymce.PluginManager.add( 'ait_button_script', function( ed, url ) {
       ed.addButton( 'ait_button', {
         title : ait_php_var.mouseover_title,
         type: 'menubutton',
         image : url + '/icons8-kalender-48.png',
         icon: 'icons8-kalender-48.png',
           menu:[{
               text: ait_php_var.mouseover_title,
               value: ait_php_var.mouseover_title,
               onclick : function() {
                   ed.windowManager.open( {
                      title: ait_php_var.window_title,
                      body: [
                           {
                             type: 'textbox',
                             name: 'link',
                             //label: 'Ext. Link',
                             label: ait_php_var.external_link,
                             value:""
                           },
                           {
                             type: 'textbox',
                             name: 'il',
                             //label: 'Int. Link',
                             label: ait_php_var.internal_link,
                             values:""
                           },


                    ],
     onsubmit: function( e ) {
       if (e.data.vl === "all") {
         e.data.vl = "";
       }
       e.data.vl = 'vl="' + e.data.vl + '" ';

       /* test whether the link starts with http:// or https://, otherwise add http:// if necessary */
       if (e.data.link !== '') {
         if (e.data.link.substring(0, 7) !== ait_http && e.data.link.substring(0, 8) !== ait_https){
           e.data.link = ait_http + e.data.link;
         }
         e.data.link = 'link="' + e.data.link + '" ';
       }
       else {
         e.data.link ="";
       }
       if (e.data.il !== '') {
         if (e.data.il.substring(0, 7) !== ait_http && e.data.il.substring(0, 8) !== ait_https){
           e.data.il = ait_http + e.data.il;
         }
         e.data.il = 'il="' + e.data.il + '" ';
       }
       else {
         e.data.il ="";
       }

       ed.insertContent(
         /* build shortcode */
         /* '[fuss link="' + e.data.link + '" vl="' + e.data.vl + '" il="' + e.data.il+ '"]' */
         '[fuss ' + e.data.link + e.data.il + ']'
       );
     }
     });
         }
     }]

   });
  });
  }
});
