// JavaScript Document
// alert( ait_php_var.ackids );

(function() {
  // const { __, } = wp.i18n;
  // var ait_cats = JSON.parse(ait_cat_obj.category);
  // var categories = [];
  var ait_http = 'http://';
  var ait_https = 'https://';
  var ait_test_http = '';
  // console.log(ait_php_var.ackids);


    /*
    for( var cat in ait_cats){
      categories.push({"text":ait_cats[cat],"value":cat});
    }
    */
     /* Register the buttons */
     tinymce.PluginManager.add( 'my_button_script', function( ed, url ) {
          ed.addButton( 'ait_button', {
            title : 'Add Infos to the events calendar',
            type: 'menubutton',
            image : url + '/icons8-kalender-48.png',
            icon: 'icons8-kalender-48.png',
              menu:[{
                  text: 'Add Infos to the events calendar',
                  value: 'Add Infos to the events calendar',
                  onclick : function() {
                      ed.windowManager.open( {
                         title: 'Add Infos to the events calendar Shortcode Generator',
                         body: [

                              {
                                 type: 'textbox',
                                 name: 'link',
                                 label: 'Ext. Link',
                                 // label: ait_php_var.external_link, //
                                 value:""
                              },
                							{
                								type: 'textbox',
                				        name: 'vl',
                                label: 'Event Category',
                				        // label: ait_php_var.event_category, //
                                values: ""
                				        // values:categories
                							},
                              {
                								type: 'textbox',
                				        name: 'il',
                                label: 'Int. Link',
                				        // label: ait_php_var.internal_link, //
                				        values:""
                							},
                              /* hier sollte eine Abfrage hin, ob eine Datei vorhanden ist, damit die folgenden Optionen angeboten werden */
                              {
                                type: 'checkbox',
                                name: 'kfm',
                                label: 'Kinderflohmärkte',
                                values: ""
                              },
                              {
                                type: 'checkbox',
                                name: 'fm',
                                label: 'Flohmärkte',
                                values: ""
                              },
                              {
                                type: 'checkbox',
                                name: 'ferien',
                                label: 'Ferien',
                                values: ""
                              },

        ],
        onsubmit: function( e ) {
          if (e.data.vl == "all") {
            e.data.vl = "";
          }
          e.data.vl = 'vl="' + e.data.vl + '" ';

          /* test whether the link starts with http:// or https://, otherwise add http:// if necessary */
          if (e.data.link != '') {
            if (e.data.link.substring(0, 7) != ait_http && e.data.link.substring(0, 8) != ait_https){
              e.data.link = ait_http + e.data.link;
            }
            e.data.link = 'link="' + e.data.link + '" ';
          }
          else {
            e.data.link ="";
          }
          if (e.data.il != '') {
            if (e.data.il.substring(0, 7) != ait_http && e.data.il.substring(0, 8) != ait_https){
              e.data.il = ait_http + e.data.il;
            }
            e.data.il = 'il="' + e.data.il + '" ';
          }
          else {
            e.data.il ="";
          }

          /* only for internal use */
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
            /* '[fuss link="' + e.data.link + '" vl="' + e.data.vl + '" il="' + e.data.il+ '"]' */
            '[fuss ' + e.data.link + e.data.vl + e.data.il + e.data.kfm_var + e.data.fm_var + e.data.ferien_var + ']'
          );
        }
      });
          }
      }]

    });
  });
  })();
