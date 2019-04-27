// JavaScript Document

(function() {
     /* Register the buttons */
     tinymce.create('tinymce.plugins.MyButtons', {
          init : function(ed, url) {
               /**
               * Inserts shortcode content
               image : '../wp-includes/images/smilies/icon_eek.gif',
               */
               ed.addButton( 'ait_button', {
                    title : 'Add Infos to TEC Shortcode',
                    image : url + '/icons8-kalender-48.png',
                    onclick : function() {
                         ed.selection.setContent('[fuss]');
                    }
               });
               /**
               * Adds HTML tag to selected content

               ed.addButton( 'button_green', {
                    title : 'Add span',
                    image : '../wp-includes/images/smilies/icon_mrgreen.gif',
                    cmd: 'button_green_cmd'
               });
               ed.addCommand( 'button_green_cmd', function() {
                    var selected_text = ed.selection.getContent();
                    var return_text = '';
                    return_text = '<h1>' + selected_text + '</h1>';
                    ed.execCommand('mceInsertContent', 0, return_text);
               });
               */
          },
          createControl : function(n, cm) {
               return null;
          },
     });
     /* Start the buttons */
     tinymce.PluginManager.add( 'my_button_script', tinymce.plugins.MyButtons );
})();
