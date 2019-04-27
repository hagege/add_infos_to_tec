// JavaScript Document

(function() {
  var ect_cats=JSON.parse(ect_cat_obj.category);
  var categories=[];

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
                                 label: 'Link',
                                 value:""
                                   },
                							{
                								type: 'listbox',
                				        name: 'vl',
                				        label: 'Events Categories',
                				        values:categories
                							},

        ],
        onsubmit: function( e ) {
            ed.insertContent(
              '[fuss + link="' + e.data.link + '"vl="' + e.data.category + '"]'
          );
        }
      });
          }
      }]
  });
});
})();
