=== Add infos to the events calendar ===
Contributors: hage
Tags: The Events Calendar, shortcodes, info, button
Donate link: https://haurand.com/plugins
Requires at least: 4.0
Tested up to: 5.2
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

“Add infos to the events calendar” provides a shortcode block (image copyright, button with link to events with a special category, link to the website of the organizer) to single events for The Events Calendar Free Plugin (by MODERN TRIBE)

== Description ==
“Add infos to the events calendar” provides a shortcode block (image copyright, button with link to events with a special category, link to the website of the organizer) to single events for The Events Calendar Free Plugin (by MODERN TRIBE)
The path to the The Events Calendar (TEC) categories is automatically suggested during installation. To be on the safe side, however, you should check this by going to the relevant event after using the shortcut and checking that the links are executed correctly.
As a rule, the short code (description and examples see below) should be one of the last lines of a single event. However, the short code can also be used in principle in posts. In this case, you should usually not use the option "vl", because this option refers to events, unless you want to link to a specific category in a contribution on events, for example.
Automatically displays the text from "Caption" (see Media, image details) in italics by default for an event or a post. A copyright notice should be entered in this field.

Attention: if the plugin is deleted, the shortcode remains in the posts and events. In this case use a plugin to delete the shortcodes, e.g. Shortcode Cleaner Lite (see wordpress.org). However, there is no guarantee that all shortcodes will be deleted correctly.

Shortcode - Options:
link = link e.g. to organizer
vl   = list of events
il   = e.g. used for internal link

Call Examples:
[fuss link="https://externer_link.de" vl=""] --> always shows picture credits, then more info with the link to external website and at vl="" the link to "more events".
[fuss vl=""] --> always shows picture credits, but no link to external website and at vl="" the link to "more events".
[fuss] --> always shows picture credits, but no link to external website.
[fuss link="https://externer_link.de" vl="nature"] --> always shows picture credits, then more info with the link to external website and at vl="Nature" the link to "more events: nature".
(of course the category must exist in The Events Calendar (this is checked by a function). If the category does not exist, the event list will be shown.)
[fuss vl="" il="http://internal_link.de/example"] --> always shows picture credits, but no link to external website and at vl="" the link to "more events" and at il="http://internal_link.de/example" the link to another external or internal webesite.


== Installation ==
1. Download "add_infos_to_tec.zip" to your local device.
2. Unzip the file.
3. Upload the folder "add_infos_to_tec" to "/wp-content/plugins/"
4. Activate the plugin through the "Plugins" menu in WordPress.

== Frequently Asked Questions ==
= Who will answer my support request? =
A webdevelopement staff member of haurand.com.

= Can this plugin also be used for other event plugins ? =
Basically yes, but it cannot be checked automatically whether the selected category exists for the "vl" option.

= Are the shortcodes deleted when the plugin is deleted ?
No, if the plugin is deleted, the shortcode remains in the posts and events. In this case use a plugin to delete the shortcodes, e.g. Shortcode Cleaner Lite (see wordpress.org). However, there is no guarantee that all shortcodes will be deleted correctly.

= Where can I find the path to the categories of The Events Calendar ?
When installing the plugin, the path to the categories is automatically determined by TEC. Under the heading you will see the following hint:
"This could be the path to the categories of The Events Calendar (TEC): (URL)
To be on the safe side, however, you should check this by going to the relevant event after using the shortcut and checking that the links are executed correctly."
If the suggested path is not correct, you can proceed as follows:
Select "Events" -> "Event Categories" from the menu. You will then see the categories on the right. Move the cursor to "View" of a category and copy the URL e.g. with CTRL C. Then you will get e.g. the following URL: http://beispielseite.de/events/category/lesungen/
Then copy the URL in the settings of "Add Infos to the events calendar" into the field with the path, but without the category (lesungen), so that you get the following URL for the example: http://localhost/leer/events/category/

= Why did I develop this plugin ?
It is extremely important to note the copyright notice in the text of photos. I had always done this by copying and pasting the caption from the featured image, but I'd already forgotten it.
The Shortcodes gets this information automatically from this field. I also wanted to place some additional and more visually appealing buttons. This is now also possible with the plugin (see examples).

== Screenshots ==
1. All options in the settings
2. Use in a single event (backend)
3. Representation of the event in the frontend

== Changelog ==
nothing

== Upgrade Notice ==
nothing
