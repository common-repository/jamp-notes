=== JAMP Notes (Just Another Memo Plugin) ===
Contributors: andreaporotti
Tags: notes, note, memo, dashboard
Requires at least: 4.9
Tested up to: 6.6
Requires PHP: 5.6
Stable tag: 1.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to attach notes to some WordPress elements like posts, pages, dashboard sections and more.

== Description ==

Using this plugin you can attach notes to some elements in the WordPress dashboard, such as:

- posts
- pages
- custom post types from other plugins (except the notes from this plugin)
- users
- plugins
- dashboard sections
- the whole dashboard

It can be helpfull if you manage a site with other people or just to take notes for yourself.

**Features**

- manage notes like the standard posts by opening the Notes page from the admin menu.
- while editing a note, use the meta box on the right to set note properties (position, color,...)
- manage section and global notes from the admin bar.
- manage item notes (eg. posts and pages) from the custom column in the admin pages.
- get notes details by hovering the mouse on the "Info" links or clicking on the "I" icons.
- add text, images and links in the note content.
- deleted notes go to the trash, so they can be restored.
- automatically discovers custom post types added by other plugins (eg. events, books...).
- creates a list of the dashboard sections based on the admin menu items.

**Configuration**

Settings for the plugin are available on the *Settings* -> *JAMP Notes* page.

Please note:

- by default the plugin data is kept after uninstall. You can choose to delete notes and settings enabling the data removal option.
- after activation, the plugin enables notes for all the existing public post types. If you then install other plugins which create new post types, you have to manually enable them in the JAMP settings.

**Permissions**

The notes are currently available only for the users with the "Administrator" role.
Each Administrator can manage all notes.

**Support**

If you find any errors or compatibility issues with other plugins, please let me know in the support forum. Thanks!

**Privacy**

This plugin does not collect or store any user data. It does not set any cookies and it does not connect to any third-party services.

== Installation ==

**Installing**

1. Download the plugin zip file.
2. Go to *Plugins* -> *Add New* in the WordPress dashboard.
3. Click on the *Upload Plugin* button.
4. Browse for the plugin zip file and click on *Install Now*.
5. Activate the plugin.

**Uninstalling**

1. Go to *Plugins* in the WordPress dashboard.
2. Look for the plugin in the list.
3. Click on *Deactivate*.
4. Click on *Delete*.

Please note: by default the plugin data is kept after uninstall. You can choose to delete all data going to *Settings* -> *JAMP Notes* and enabling data removal on uninstall.

== Screenshots ==

1. The admin page to manage the notes.
2. The note editing page with the settings meta box.
3. The admin bar menu shows global and section notes.
4. An example of note attached to a page.
5. The confirmation popup when moving a note to the trash.
6. The plugin settings page.

== Changelog ==

**1.5.1 [2024-07-14]**

- Tested on WordPress 6.6.
- Fixed "deprecated" messages on PHP >= 8.1.

**1.5.0 [2023-07-31]**

- Column notes can now be collapsed! This prevents from having lot of vertical space taken by notes. By default notes are open but can be set as closed in the plugin settings. (thanks to @cebuss for the feature suggestion)
- Added a column to the notes admin page to display the note color.
- Fixed an accessibility issue in the plugin settings page (multiple labels for the same fields)
- Performance improvements.
- Tested on WordPress 6.3.
- Tested on PHP 8.0.x.

**1.4.0 [2023-01-01]**

Happy New Year! After a long time I'm back with a new release.

- Now you can set the note color! While creating or editing a note choose a color in the note settings. The default color is yellow: existing notes without a color will be shown in yellow.
- Changed the style of column notes to support the new color setting.
- Small fixes and optimizations.
- Tested on WordPress 6.1.

**1.3.2 [2021-12-14]**

- If an item type is disabled in the plugin settings, a message will be displayed while editing notes of that type.
- After an element with attached notes is deleted, the Notes list will show the name of the deleted item on the previously attached notes.
- Tested on WordPress 5.9.

**1.3.1 [2020-11-25]**

- Fixed a bug that caused the loading of wrong notes in some conditions.
- Improved menu items url generation (should support more plugins).
- Tested on WordPress 5.6.
- WP 5.6 compatibility: fixed a visual bug on the "Trash Note" dialog.
- WP 5.6 compatibility: notes restored from trash will be published again (so we skip the new "trash to draft" WP default behavior)

**1.3.0 [2020-08-15]**

- NEW: notes can now be added to the users! Go to *Settings* -> *JAMP Notes* to enable it.
- Fixed a problem preventing settings pages to be recognized as supported sections after settings save.
- Improved user permissions check in the settings page.

**1.2.0 [2020-07-13]**

- NEW: notes can now be added to the plugins!
- The Notes column has now a specific width to prevent random space usage.
- Showing a placeholder for missing note title in admin bar and columns.
- Showing a placeholder for missing titles when selecting items in the note editing page.
- Fixed a bug in the Location column when a note is attached to a post with no title.
- Fixed admin bar notes not showing the bold text style.
- A few changes for performance improvements.
- Tested on WordPress 5.5.

**1.1.0 [2020-06-18]**

- Added global and section notes counters on the admin bar.
- Added validation to the Note Setting meta box.
- Added a meta box to view notes attached to a post while editing it.
- Improved content generation of Location column in the Notes page.

**1.0.1 [2020-06-10]**

- Managed the admin bar panel max height.
- Replaced the tooltip with a hidden section for the admin bar notes details. Click on the "I" icon to show it.
- Fixed an issue with long titles in admin bar notes.
- Fixed an issue in the admin menu names parsing.
- Fixed an issue with the tooltip not showing correctly in the custom column in mobile view.
- Fixed metabox style in mobile view.
- Fixed settings page style in mobile view.

**1.0.0 [2020-06-03]**

- First release.