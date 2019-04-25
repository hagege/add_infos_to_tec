(function() {
    tinymce.create("tinymce.plugins.ait_btn_cmd", {

        //url argument holds the absolute url of our plugin directory
        init : function(ed, url) {

            //add new button
            ed.addButton("yellow", {
                title : "Add infos to the events calendar",
                cmd : "ait_command",
                image : url + "/event-20.png"
            });

            //button functionality.
            ed.addCommand("ait_command", function() {

                var return_text = "[fuss]";
                ed.execCommand("mceInsertContent", 0, return_text);
            });

        },

    });

    tinymce.PluginManager.add("ait_btn_cmd", tinymce.plugins.ait_btn_cmd);
})();
