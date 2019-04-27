// JavaScript Document

(function() {
  var ect_cats = JSON.parse(ect_cat_obj.category);
  var categories = [];
  var ait_http = 'http://';
  var ait_https = 'https://';
  var ait_test_http = '';


    for( var cat in ect_cats){
      categories.push({"text":ect_cats[cat],"value":cat});
    }

     /* Register the buttons */
     tinymce.PluginManager.add( 'my_button_script', function( ed, url ) {
          ed.addButton( 'ait_button', {
            title : 'Add Infos to TEC Shortcode',
            type: 'menubutton',
            image : url + '/icons8-kalender-48.png',
            icon: 'icons8-kalender-48.png',
              menu:[{
                  text: 'Add Infos to TEC Shortcode',
                  value: 'Add Infos to TEC Shortcode',
                  onclick : function() {
                      ed.windowManager.open( {
                         title: 'Add Infos to TEC Shortcode Generator',
                         body: [

                              {
                                 type: 'textbox',
                                 name: 'link',
                                 label: 'External Link',
                                 value:""
                              },
                							{
                								type: 'listbox',
                				        name: 'vl',
                				        label: 'Event Categories',
                				        values:categories
                							},
                              {
                								type: 'textbox',
                				        name: 'il',
                				        label: 'Internal Link',
                				        values:""
                							},
        ],
        onsubmit: function( e ) {
          if (e.data.vl == "all") {
            e.data.vl = "";
          }
          /* test whether the link starts with http:// or https://, otherwise add http:// if necessary */
          if (e.data.link != '') {
            if (e.data.link.substring(0, 7) != ait_http && e.data.link.substring(0, 8) != ait_https){
              e.data.link = ait_http + e.data.link;
            }
          }
          if (e.data.il != '') {
            if (e.data.il.substring(0, 7) != ait_http && e.data.il.substring(0, 8) != ait_https){
              e.data.il = ait_http + e.data.il;
            }
          }
          ed.insertContent(
            '[fuss link="' + e.data.link + '" vl="' + e.data.vl + '" il="' + e.data.il+ '"]'
          );
        }
      });
          }
      }]
  });
});
})();