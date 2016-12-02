=== Google Cloud Storage plugin ===
Contributors: Google
Tags: google, Google Cloud Storage
Requires at least: 3
Tested up to: 4.6.1
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin for uploading media files to Google Cloud Storage.

== Description ==

Google Cloud Storage plugin allows you to upload media files to a
Google Cloud Storage bucket.

== Installation ==

This plugin should be downloaded and placed in your
`/wp-content/plugins/` directory. Also this plugin depends on
google/cloud packagist package, and you need to install the gs://
stream wrapper.

First install the Google Cloud library by the following command:

```sh
$ composer require google/cloud
```

Add the following lines to your `wp-config.php`:

```php
require_once __DIR__ . '/vendor/autoload.php';

stream_wrapper_register(
    'gs',
    '\Google\Cloud\Storage\CloudStorageStreamWrapper',
    0);
```

Then enable this plugin on the WordPress admin UI, and configure your
Google Cloud Storage bucket in the plugin setting UI.

After the configuration, media files will be uploaded to Google Cloud
Storage and served from there.

== Changelog ==

= 0.1 =
* Initial release
