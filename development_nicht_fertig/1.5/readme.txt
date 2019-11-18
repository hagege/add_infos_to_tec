=== Add infos to the events calendar ===
Contributors: hage
Tags: The Events Calendar, events, shortcode, button, lightweight
Donate link: https://haurand.com/wordpress-plugins/
Requires at least: 4.0
Tested up to: 5.2.1
Stable tag: 1.5
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

“Add infos to the events calendar” provides a shortcode block (image copyright, button with link to events with a special category, link to the website of the organizer) to single events for The Events Calendar Free Plugin (by MODERN TRIBE)

== Description ==
The lightweight plugin “Add infos to the events calendar” provides a shortcode block (image copyright, button with link to events with a special category, link to the website of the organizer) to single events for The Events Calendar Free Plugin (by MODERN TRIBE)
The path to the The Events Calendar (TEC) categories is automatically suggested during installation. To be on the safe side, however, you should check this by going to the relevant event after using the shortcut and checking that the links are executed correctly.
As a rule, the short code (description and examples see below) should be one of the last lines of a single event. However, the short code can also be used in principle in posts. In this case, you should usually not use the option "vl", because this option refers to events, unless you want to link to a specific category in a contribution on events, for example.
Automatically displays the text from "Caption" (see Media, image details) in italics by default for an event or a post. A copyright notice should be entered in this field.

= Attention =
If the plugin is deleted, the shortcode remains in the posts and events. In this case use a plugin to delete the shortcodes, e.g. Shortcode Cleaner Lite (see wordpress.org). However, there is no guarantee that all shortcodes will be deleted correctly.

= Shortcode - Options =
1. link = link e.g. to organizer
2. vl   = list of events
3. il   = e.g. used for internal link

= Call Examples =
* [fuss link="https://externer_link.de" vl=""] --> always shows picture credits, then more info with the link to external website and at vl="" the link to "more events".
* [fuss vl=""] --> always shows picture credits, but no link to external website and at vl="" the link to "more events".
* [fuss] --> always shows picture credits, but no link to external website.
* [fuss link="https://externer_link.de" vl="nature"] --> always shows picture credits, then more info with the link to external website and at vl="Nature" the link to "more events: nature".
(of course the category must exist in The Events Calendar (this is checked by a function). If the category does not exist, the event list will be shown.)
* [fuss vl="" il="http://internal_link.de/example"] --> always shows picture credits, but no link to external website and at vl="" the link to "more events" and at il="http://internal_link.de/example" the link to another external or internal webesite.


== Installation ==
1. Download "add_infos_to_tec.zip" to your local device.
2. Unzip the file.
3. Upload the folder "add_infos_to_tec" to "/wp-content/plugins/"
4. Activate the plugin through the "Plugins" menu in WordPress.

== Frequently Asked Questions ==
= Who will answer my support request? =
A webdevelopement staff member of haurand.com.

= Can this plugin also be used for other event plugins ? =
Basically, this plugin can only be used if The Events Calendar is also activated. Of course, you can check if the plugin can also be used if you use another plugin for events. In this case I would be very happy about your feedback, because I would like to include this information in the description here.
If no Events plugin is used, the plugin can only be used to display the copyright and external as well as internal links via the shortcode.

= Are the shortcodes deleted when the plugin is deleted ?
No, if the plugin is deleted, the shortcode remains in the posts and events. In this case use a plugin to delete the shortcodes, e.g. Shortcode Cleaner Lite (see wordpress.org). However, there is no guarantee that all shortcodes will be deleted correctly.

= Where can I find the path to the categories of The Events Calendar ?
When installing the plugin, the path to the categories of TEC is automatically recognized by the plugin. Under the heading you will see the following hint:
"This could be the path to the categories of The Events Calendar (TEC): (URL)
To be on the safe side, however, you should check this by going to the relevant event after using the shortcut and checking that the links are executed correctly."
If the suggested path is not correct, you can proceed as follows:
Select "Events" -> "Event Categories" from the menu. You will then see the categories on the right. Move the cursor to "View" of a category and copy the URL e.g. with CTRL C. Then you will get e.g. the following URL: http://beispielseite.de/events/category/lesungen/
Then copy the URL in the settings of "Add Infos to the events calendar" into the field with the path, but without the category (lesungen), so that you get the following URL for the example: http://beispielseite.de/events/category/

= Why did I develop this plugin ?
It is extremely important to note the copyright notice in the text of photos. I had always done this by copying and pasting the caption from the featured image, but I'd already forgotten it.
The Shortcodes gets this information automatically from this field. I also wanted to place some additional and more visually appealing buttons. This is now also possible with the plugin (see examples).

= What about the further development of the plugin ?
great question :-)
I already have some more ideas and can tell you that this plugin will definitely be further developed and get some additional features. An important aspect will be that the plugin remains lightweight. If you have any suggestions for further development, I would be very happy if you could tell me. Answer guaranteed, but I can't guarantee that your ideas will be implemented ;-)

= Where can I donate for this plugin?
Nowhere - but a donation to a charity that needs the money more urgently than I would be great. But I would be very happy about a positive review.

= Is it possible to customize the button labels ?
Yes, from version 1.0 on this option is new, see Settings and Changelog

== Screenshots ==
1. All options in the settings
2. Representation of the event in the frontend
3. Use in a single event (backend)
4. The reference to the photo must be entered in this field (caption).
5. Call via the icon at the top right of the menu bar. Then click on "Add Infos to the events Calendar".
6. These fields can currently be filled automatically via the shortcode generator and then appear in the description at the point where the cursor was positioned.
7. Executing the Shortcodes in Gutenberg

== Changelog ==
= [1.3] 2019-05-30 =
* Problem with the version number (1.2) so that no updates are performed automatically from Version 1.02 to 1.2.

= [1.2] 2019-05-28 =
* Added: Icon added to the editor tinycme (Classic Editor), so that now the entries for the internal and external link no longer only have to be entered manually directly as short code, but via an additional dialog. However, this icon cannot (yet) be used in the Gutenberg Editor.

= [1.02] 2019-05-18 =
* Fixed: If The Events Calendar is not installed, in some cases an error message appeared on the page with the short code
* Added: Automatically adds http:// to a URL before the link, if that is missing

= [1.01] 2019-05-14 =
* Fixed: With the option "vl" the event list was not displayed correctly if the category was wrong or vl="" was selected.

= [1.0] 2019-05-14 =
* Added feature: The name of the buttons can now be defined via the settings. It is no longer necessary to have translation files that contain translations of the button names.
* Added: Display the copyright only if the field is not empty
* Updated translations
* Updated design of the settings

= [0.66] 2019-05-10 =
* Add some infos and update language files

= [0.65] 2019-05-09 =
* Fixed a bug with the language files

= [0.62] 2019-05-07 =
* Initial release

== Upgrade Notice ==
= [1.0] =
* Please have a look at the settings, because from this version on there are settings for the <strong>labeling of the buttons</strong>. This allows the buttons to be labeled individually and you are no longer dependent on whether a translation is available in the respective language file.
* If the translations are not correct, you have to look into the folder wp-content\languages\plugins and delete the existing translation files add-infos-to-the-events-calendar*.* if necessary. <strong>Attention</strong>: Please always make a backup first.
