=== Google App Engine for WordPress ===
Contributors: google, sennza
Tags: google, app engine, gae, mail, email, uploads, uploading, cloud storage
Requires at least: 3.5
Tested up to: 3.6
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Optimize your WordPress installation for the Google App Engine platform.


== Description ==

Google App Engine for WordPress enables seamless operation of your
WordPress-powered site on the App Engine PHP runtime.

This plugin adds overrides to core functionality in WordPress to use App Engine
infrastructure, such as the Mail functionality, and uploading media to Google
Cloud Storage


== Installation ==

This plugin should be downloaded and placed in your `/wp-content/plugins/`
directory. The App Engine infrastructure does not allow you to install plugins
and themes without deploying them via `appcfg.py`.

1. Unzip `appengine/` to the `/wp-content/plugins/` directory
2. Deploy your code to App Engine
3. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= Why can't I use the plugin installer/upgrader? =

For security reasons, all code on App Engine must be deployed via `appcfg.py`.
This includes WordPress plugins and themes, as well as editing files via the
administration panel.

= Is this plugin required to run WordPress on App Engine? =

We recommend running App Engine for WordPress to ensure that all WordPress
functionality works correctly. Without this plugin, you will not be able to send
email or upload files, and some UI may be broken.


== Caching ==

We recommend using the [Batcache][] and [Memcache][] drop-ins to cache your
WordPress site. Batcache integrates with Memcache to cache your site on the App
Engine memcache server and will ensure that CloudSQL is used as little as
possible, reducing your costs.

If you host a rarely updated site, we suggest adding the following configuration
to your `wp-config.php`:

	$batcache = [
		'seconds' => 0,
		'max_age' => 60 * 30,  // 30 minutes.
	];

This will cache pages on your site for a year and ensure that they do not expire
in memcache. With this setup and a fully primed cache, all anonymous users will
be served via Memcache with no hits to CloudSQL.

Note that using WordPress' built-in comments will enable commenters to bypass
the cache, so if you want to use comments on a fully-cached site, we recommend
[Intense Debate][] or [Disqus][] (with the synchronization disabled).

[Batcache]: http://wordpress.org/plugins/batcache/
[Memcache]: http://wordpress.org/plugins/memcache/
[Intense Debate]: http://wordpress.org/plugins/intensedebate/
[Disqus]: http://wordpress.org/plugins/disqus-comment-system/


== Changelog ==

= 1.0 =
* Initial release


== Upgrade Notice ==

= 1.0 =
This version is the initial release of Google App Engine for WordPress.
