=== Commenter Data ===
Contributors: ankitgadertcampcom, 5um17
Tags: comments, csv, commenter, commentmeta, export, comment, lead, marketing
Requires at least: 3.0
Tested up to: 4.1
Stable tag: 2.1
License: GPLv2 or later (of-course)
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Exports commenter's data in csv format for individual post.

== Description ==
This plugin exports data of commenter, which may include comment post id, comment date, name, email, website etc. The data will be exported in the form of csv file.

This plugin is compatible with Comment Attachment plugin ( http://wordpress.org/plugins/comment-attachment/ ) so exported csv file can also have attachment url in it.

It has extensive option in dashboard setting page, you can select which fields will be present in csv file. It also lists posts so that you can export csv file right from setting page.

Plugin follows all the codex guidelines in its code.

= Features =

* Easy to install and setup
* Very simplified and clean UI.
* Easily customizable
* Exports csv file containing information of commenter
* Set of various options
* Compatible with comment attachment plugin
* Strong support.
* For paid support and customization support contact http://sharethingz.com/contact/
* Very lighweight code.
* Translation ready code.

== Installation ==
Install Commenter Data from the 'Plugins' section in your dashboard (Plugins > Add New > Search for Commenter Data ).

Place the downloaded plugin directory into your wordpress plugin directory.

Activate it through the 'Plugins' section.

= Important Step to follow =

* If you want comment attachment link to export, comment attachment plugin must be activated.


== Screenshots ==

1. Commenter data setting page.

== Changelog ==

= 1.1 =
* Add comment content export option in csv.
* Delete temporary created csv file in uploads folder.

= 2.0 =
* commenter_add_field action to add more fields in backend on settings page.
* commenter_filter_setting_data filter to filter the data before firing the sql query.
* SQL query made more secure using $wpdb prepare method.

= 2.1 =
* cd_cap filter for filtering capability to view commenter data settings and download the csv file.
* Assigned separate menu page for Commenter Data plugin settings.
* Removed option page under Settings menu page.
