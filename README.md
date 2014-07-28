Memcached-Object-Cache-Tune-Up
==============================

Memcached Object Cache Tune Up is a fork of the Memcached Object Cache plugin (https://wordpress.org/plugins/memcached) by Ryan Boren &amp; Matt Martz with a few little tweaks to elimate PHP warnings and make setup a little cleaner

Updates

* Eliminate PHP errors and warnings that are generated when running with WP_DEBUG and stats are incremented
* Eliminate cache bucket warmings by initializing the global holding the list of memcached servers/ports.  Set to localhost and default port.
* Make object cache unique to instance of WordPress database by initializing cache key salt to match the WordPress database prefix if it is defined.



