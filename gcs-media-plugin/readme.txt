=== Google Cloud Storage plugin ===
Contributors: google
Tags: google, Google Cloud Storage
Requires at least: 3
Stable tag: 0.1.3
Tested up to: 4.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin for uploading media files to Google Cloud Storage.

== Description ==

Google Cloud Storage plugin allows you to upload media files to a
Google Cloud Storage bucket.

== Installation ==

This plugin should be downloaded and placed in your `/wp-content/plugins/` directory.

Then enable this plugin on the WordPress admin UI, and configure your
Google Cloud Storage bucket in the plugin setting UI. You need to set
the default acl of the bucket where `allUsers` can read.

After the configuration, media files will be uploaded to Google Cloud
Storage and served from there.

If you want to run this plugin outside of Google Cloud Platform, you
need to configure your service account as follows:

* Visit Cloud Console, go to `IAM & Admin` -> `Service accounts` and
  create a service account with `Storage Object Admin` permission and
  download the json key file.

* Upload the json key file to the hosting server. Don't put it in a
  public serving area.

* Add the following line to wp-config.php (replace the file path with
  the real one).

  putenv('GOOGLE_APPLICATION_CREDENTIALS=/secure-place/my-service-account.json');

== Frequently Asked Questions ==

Q. The plugin crashes with `No project ID was provided, and we were
unable to detect a default project ID`, what's wrong?

A. See the section about configuring the service account in the
`Installation` section.

Q. How to configure the default ACL on my Google Cloud Storage bucket?

A. See: https://wordpress.org/support/topic/google-storage-not-work/page/2/#post-8897852

== Changelog ==

= 0.1.3 =
* Added a section for configuring service account to the readme
* Added Frequently Asked Questions section to the readme
* Updated dependencies

= 0.1.2 =
* Added "Tested up to" field to the readme

= 0.1.1 =
* Bundle vendor dir in the zip file

= 0.1 =
* Initial release
