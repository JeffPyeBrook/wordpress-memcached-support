# MemcacheD Is Your Friend - Memcached Support for WordPress #
==============================


Adds memcached object cache support to WordPress.  Detects either the PECL Memecache or PECL Memcached (Memcached preferred)
class and uses the appropriate interface.  You don't need to figure out which class is available in your installation.

## Description ##
Memcached Object Cache provides a persistent backend for the WordPress object cache. A memcached server and either the
the PECL Memcached or PECL Memcache extension and class are required.

This plugin uses the WordPress database configuration to set the cache salt used to create unique keys for cache items. This
means that you can use a single memcached instance for multiple blogs without one blog stomping on the other blog 
object cache, and without creating a special configuration. If you wnat to share an object cache between blogs that can be 
enabled by adjsuting the settings in your blog wp-config.php files.

This plugin does not require manual copying of files between directories for installation, and it doesn't require the installer
to differentiate between the memcache and memcached PECL extensions if they know their host makes once available.

Also, because this plugin detects the PECL extension available, for those that develop on Windows where the
PECL Memcached extension is not generally available, you can maintain a common configuration with your Linux host
where that extension is common.

Plugin creates a dashboard menu option under "Tools" where you can view your memcached statistics.

Plugin is based laregly on previous works by Matt Martz, Ryan Boren (https://wordpress.org/plugins/memcached/) and
Scott Taylor, Ryan Boren, Matt Martz, Mike Schroder ( https://wordpress.org/plugins/memcached-redux/ )

## Installation ##
1. If not already available, install [memcached](http://danga.com/memcached) on at least one server. Note the connection info. The default is `127.0.0.1:11211`.

1. If not already available on your host, install the [PECL extension](http://pecl.php.net/package/memcached or http://pecl.php.net/package/memcached)

1. Activate the plugin

## Frequently Asked Questions ##

### How can I manually specify the memcached server(s)? ###

Add something similar like this near the top of the wp-config.php

```
$memcached_servers = array(
	'default' => array(
		'127.0.0.1:11211',
		'10.10.10.30:11211'
	)
);
```


## Changelog ##

### 2.0.0 ###
* Initial release public based on previous works
