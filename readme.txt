=== Add infos to the events calendar ===
Contributors: @hage
Tags: The Events Calendar, shortcodes, info, button
Donate link: https://haurand.com/plugins
Requires at least: 4.0
Tested up to: 5.1.1
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

“Add infos to the events calendar” provides a shortcode block (image copyright, button with link to events with a special category, link to the website of the organizer) to single events for The Events Calendar Free Plugin (by MODERN TRIBE)

== Description ==
“Add infos to the events calendar” provides a shortcode block (image copyright, button with link to events with a special category, link to the website of the organizer) to single events for The Events Calendar Free Plugin (by MODERN TRIBE)

As a rule, the short code (description and examples see below) should be one of the last lines of a single event. However, the short code can also be used in principle in posts.

Automatically displays the text from "Caption" (see Media, image details) in italics by default for an event or a post. A copyright notice should be entered in this field.

Call Examples:
[fuss link="https://externer_link.de" vl=""] --> always shows picture credits, then more info with the link to external website and at vl="" the link to "more events".
[fuss vl=""] --> always shows picture credits, but no link to external website and at vl="" the link to "more events".
vl = list of events
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

= Can I change the shortcode? =
Yes, just open `add_shortcode_to_tec.php` and add or change what text you want to trigger the shortcode in on of the arrays near the bottom. If this gets any kind of popularity, I'll make that easier by adding an options page.

= Can you add this feature I just thought of? =
Can I? Yes. Will I? Yes, if I think it would be a helpful addition. I'm trying to keep things clean and simple, but there's always room for improvement, so let me know if you think a feature is lacking!


== Screenshots ==
1. All options in the settings
2. Use in a single event (backend)
3. Representation of the event in the frontend

== Changelog ==
nothing

== Upgrade Notice ==
nothing
