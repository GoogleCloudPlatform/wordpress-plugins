=== Google Cloud Storage plugin ===
Contributors: Google
Tags: google, Google Cloud Storage
Requires at least: 3
Stable tag: 0.1.2
Tested up to: 4.7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin for uploading media files to Google Cloud Storage.

== Description ==

Google Cloud Storage plugin allows you to upload media files to a
Google Cloud Storage bucket.

== Installation ==

This plugin should be downloaded and placed in your
`/wp-content/plugins/` directory.

Then enable this plugin on the WordPress admin UI, and configure your
Google Cloud Storage bucket in the plugin setting UI. You need to set
the default acl of the bucket where allUsers can read.

After the configuration, media files will be uploaded to Google Cloud
Storage and served from there.

== Changelog ==

= 0.1.2 =
* Added "Tested up to" field to the readme

= 0.1.1 =
* Bundle vendor dir in the zip file

= 0.1 =
* Initial release
