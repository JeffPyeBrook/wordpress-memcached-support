===  MemcacheD Is Your Friend  ===
Contributors: pyebrook
Tags: cache, memcached, memcache
Requires at least: 3.0
Tested up to: 4.1.1
Stable tag: 2.0.0
License: GPVv2
Donate Link: http://www.pyebrook.com

Adds MemcacheD object cache support to WordPress and auto-configures your cache setup.

== Description ==
Memcached Object Cache provides a persistent backend for the WordPress object cache. A memcached server and either the
the PECL Memcached or PECL Memcache extension and class are required.  Detects either the PECL Memecache or PECL
Memcached (Memcached preferred) class and uses the appropriate interface. That means you don't need to figure out
which PHP class is available in your installation, the plugin will detect the proper configuration.

This plugin does not require manual copying of files between directories for installation, and it doesn't require the installer
to differentiate between the memcache and memcached PECL extensions if they know their host makes once available.

Also, because this plugin autodetects the PECL extension available, for those that develop on Windows where the
PECL Memcached extension is not generally available, you can maintain a common configuration with your Linux host
where that extension is common.

Plugin creates a dashboard menu option under "Tools" where you can view your memcached statistics.

Plugin is based laregly on previous works by Matt Martz, Ryan Boren (https://wordpress.org/plugins/memcached/) and
Scott Taylor, Ryan Boren, Matt Martz, Mike Schroder ( https://wordpress.org/plugins/memcached-redux/ )

== Installation ==
1. If not already available, install [memcached](http://danga.com/memcached) on at least one server. Note the connection info. The default is `127.0.0.1:11211`.

1. If not already available on your host, install the [PECL extension](http://pecl.php.net/package/memcached or http://pecl.php.net/package/memcached)

1. Activate the plugin

== Frequently Asked Questions ==

= How can I manually specify the memcached server(s)? =

Add something similar to the following to wp-config.php above `/* That's all, stop editing! Happy blogging. */`:

`
$memcached_servers = array(
	'default' => array(
		'127.0.0.1:11211',
		'10.10.10.30:11211'
	)
);
`

== Screenshots ==
= No user or administrator options, so no screenshots :)


== Changelog ==

= 2.0.0 =
* Initial release public based on previous works

== Upgrade Notice ==
= 2.0.0 =
* Initial release public based on previous works

