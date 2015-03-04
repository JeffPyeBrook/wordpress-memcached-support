<?php
/*
Plugin Name: Memcached Object Cache for WordPress
Description: Memcached via PHP Memcache or Memcached Class Support for WordPress
Version: 2.0.0
Plugin URI: http://wordpress.org/extend/plugins/wordpress-memcached-support/
Author: Jeffrey Schutzman - - uses code from Ryan Boren, Denis de Bernardy, Matt Martz, Mike Schroder, Scott Taylor
*/


/*
** Copyright 2010-2015, Pye Brook Company, Inc.
**
**
** This software is provided under the GNU General Public License, version
** 2 (GPLv2), that covers its  copying, distribution and modification. The 
** GPLv2 license specifically states that it only covers only copying,
** distribution and modification activities. The GPLv2 further states that 
** all other activities are outside of the scope of the GPLv2.
**
** All activities outside the scope of the GPLv2 are covered by the Pye Brook
** Company, Inc. License. Any right not explicitly granted by the GPLv2, and 
** not explicitly granted by the Pye Brook Company, Inc. License are reserved
** by the Pye Brook Company, Inc.
**
** This software is copyrighted and the property of Pye Brook Company, Inc.
**
** Contact Pye Brook Company, Inc. at info@pyebrook.com for more information.
**
** This program is distributed in the hope that it will be useful, but WITHOUT ANY 
** WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR 
** A PARTICULAR PURPOSE. 
**
*/

define ( 'WORDPRESS_MEMCACHED_SUPPORT_VERSION', '2.0' );

function wordpress_memcached_support_activate() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		// do not do this work while processing AJAX
		return;
	}

	// Activation code here...
	$last_activated_version = get_option( 'wordpress_memcached_support_version', '0.0' );

	if ( WORDPRESS_MEMCACHED_SUPPORT_VERSION == $last_activated_version ) {
		// no work to do we already activated at least once
		return;
	}

	$template_object_cache_file_path = plugin_dir_path( __FILE__ ) . 'object-cache-template.php';

	// make sure we have an object-cache.php with full paths to plugin directory to install to WordPress
	// content directory.
	$distribution_object_cache_file_path = plugin_dir_path( __FILE__ ) . 'object-cache.php';
	if ( ! file_exists( $distribution_object_cache_file_path ) ) {
		// build an object-cache.php for this installation
		$file_source                 = file_get_contents( $template_object_cache_file_path );
		$this_installation_directory = plugin_dir_path( __FILE__ );
		$file_source                 = str_replace( '%PLUGININSTALLDIRECTORY%', $this_installation_directory, $file_source );

		file_put_contents( $distribution_object_cache_file_path, $file_source );
		chmod( $distribution_object_cache_file_path, 0644 );
	}

	if ( ! file_exists( $distribution_object_cache_file_path ) ) {
		wordpress_memcached_support_set_admin_notice( 'ERROR: could not create configured object-cache.php for your site, aborting' );

		return;
	}

	$distribution_object_cache_file_unique_id = sha1_file( $distribution_object_cache_file_path );

	$operational_object_cache_file_path = wp_content_dir() . 'object-cache.php';
	if ( file_exists( $operational_object_cache_file_path ) ) {
		$operational_object_cache_file_unique_id = sha1_file( $operational_object_cache_file_path );
	} else {
		$operational_object_cache_file_unique_id = '';
	}

	$backup_distribution_object_cache_file_path = plugin_dir_path( __FILE__ ) . 'object-cache.php.backup';
	if ( file_exists( $backup_distribution_object_cache_file_path ) ) {
		$backup_distribution_object_cache_file_unique_id = sha1_file( $backup_distribution_object_cache_file_path );
	} else {
		$backup_distribution_object_cache_file_unique_id = '';
	}

	// if there is an operational object cache, and it has not already been backed up, and it is not the file from
	// our plugin directory we backup the file, then remove the operational file
	if ( ! empty( $operational_object_cache_file_unique_id )
	     && ( $operational_object_cache_file_unique_id != $distribution_object_cache_file_unique_id )
	     && ( $operational_object_cache_file_unique_id != $backup_distribution_object_cache_file_unique_id )
	) {
		$result = copy( $operational_object_cache_file_path, $backup_distribution_object_cache_file_path );
		if ( ! $result ) {
			wordpress_memcached_support_set_admin_notice( 'ERROR: could not backup operational object-cache.php, aborting' );

			return;
		}

		$result = unlink( $operational_object_cache_file_path );
		if ( ! $result ) {
			wordpress_memcached_support_set_admin_notice( 'ERROR: could not remove existing operational object-cache.php, aborting' );

			return;
		}

		$operational_object_cache_file_unique_id = '';
	}

	if (
		! file_exists( $operational_object_cache_file_path )
		&& file_exists( $distribution_object_cache_file_path )
	) {
		$result = copy( $distribution_object_cache_file_path, $operational_object_cache_file_path );
		if ( ! $result ) {
			wordpress_memcached_support_set_admin_notice( 'ERROR: could not copy new object-cache.php from plugin directory to WordPress content directory, aborting' );

			return;
		}
	}

	wordpress_memcached_support_set_admin_notice( "WordPress Memcached Support Setup Complete" );
	update_option( 'wordpress_memcached_support_version', WORDPRESS_MEMCACHED_SUPPORT_VERSION );

}

register_activation_hook( __FILE__, 'wordpress_memcached_support_activate' );


function wordpress_memcached_support_deactivate() {

	// Deactivation code here...
	delete_option( 'wordpress_memcached_support_version' );

	$distribution_object_cache_file_path = plugin_dir_path( __FILE__ ) . 'object-cache.php';
	if ( file_exists( $distribution_object_cache_file_path ) ) {
		unlink( $distribution_object_cache_file_path );
		if ( file_exists( $distribution_object_cache_file_path ) ) {
			wordpress_memcached_support_set_admin_notice( 'ERROR DEACTIVATING: could remove object-cache.php from WordPress plugin directory. Uninstall may have failed, aborting' );

			return;
		}
	}

	$operational_object_cache_file_path = wp_content_dir() . 'object-cache.php';
	if ( file_exists( $operational_object_cache_file_path ) ) {
		unlink( $operational_object_cache_file_path );
		if ( file_exists( $operational_object_cache_file_path ) ) {
			wordpress_memcached_support_set_admin_notice( 'ERROR DEACTIVATING: could remove object-cache.php from WordPress content directory. Uninstall may have failed, aborting' );

			return;
		}
	}

	$backup_distribution_object_cache_file_path = plugin_dir_path( __FILE__ ) . 'object-cache.php.backup';
	if ( file_exists( $backup_distribution_object_cache_file_path ) ) {
		$result = copy( $backup_distribution_object_cache_file_path, $operational_object_cache_file_path );
		if ( ! $result ) {
			wordpress_memcached_support_set_admin_notice( 'ERROR DEACTIVATING: could restore backup object-cache.php to WordPress content directory. Uninstall may have failed.' );

			return;
		}
	}

	wordpress_memcached_support_set_admin_notice( 'WordPress Memcached Support object-cache.php removed from WordPress content directory. Memcached support deactivated.' );

	// sadly we cant show an admin notice after we deactivate, we need to clear it so it doesn't show on future activation
	wordpress_memcached_support_set_admin_notice();

	return;
}

register_deactivation_hook( __FILE__, 'wordpress_memcached_support_deactivate' );


if ( ! function_exists( 'wp_content_dir' ) ) {
	function wp_content_dir() {
		// plugins are guided to to not use the WP_CONTENT_DIR constant, but no alternative is provided in the API :(
		return trailingslashit( WP_CONTENT_DIR );
	}
}


function wordpress_memcached_support_check_for_update() {

	if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		// plugins can be updated without the activation hook firing, this is a check for that
		$last_activated_version = get_option( 'wordpress_memcached_support_version', '0.0' );
		if ( WORDPRESS_MEMCACHED_SUPPORT_VERSION == $last_activated_version ) {
			// no work to do we already activated at least once
			return;
		}

		wordpress_memcached_support_activate();
	}
}

if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
	add_action( 'admin_init', 'wordpress_memcached_support_check_for_update' );
}


function wordpress_memcached_support_set_admin_notice( $notice = false ) {
	if ( empty( $notice ) ) {
		delete_option( 'wordpress_memcached_support_notice' );
	} else {
		$notice = update_option( 'wordpress_memcached_support_notice', $notice );
		error_log( __FILE__ . ': ' . $notice );
	}
}

function wordpress_memcached_support_show_admin_notice() {
	$wordpress_memcached_support_notice = get_option( 'wordpress_memcached_support_notice', '' );
	if ( ! empty( $wordpress_memcached_support_notice ) ) {
		$class = ( strpos( $wordpress_memcached_support_notice, 'ERROR' ) === false ) ? 'updated' : 'error';
		?>
		<div class="<?php echo $class; ?>">
			<p><?php echo $wordpress_memcached_support_notice; ?></p>
		</div>
		<?php

		wordpress_memcached_support_set_admin_notice( false );
	}
}

$wordpress_memcached_support_notice = get_option( 'wordpress_memcached_support_notice', '' );
if ( ! empty( $wordpress_memcached_support_notice ) ) {
	add_action( 'admin_notices', 'wordpress_memcached_support_show_admin_notice' );
}


if ( is_admin() ) {
	add_action( 'admin_menu', 'wordpress_memcached_support_admin_menu' );
}

function wordpress_memcached_support_admin_menu() {

	add_management_page(
		__( 'Memcached', 'wordpress-memcached' ),
		__( 'Memcached', 'wordpress-memcached' ),
		'manage_options',
		'wordpress_memcached_support_admin_page',
		'wordpress_memcached_support_admin_page'
	);

}

function wordpress_memcached_support_admin_page() {
	?>
	<div class="wrap">
		<h2>WordPress Memcached Status</h2>
			<div>
				<h3>
				<?php
				if ( class_exists( 'Memcached' ) ) {
					_e( 'Using the PHP Memcached class to interact with Memcached', 'wordpress-memcached' );
				} else if ( class_exists( 'Memcache' ) ) {
					_e( 'Using the PHP Memcache class to interact with Memcached', 'wordpress-memcached' );
				} else {
					_e( 'No PHP Memcached or Memcache class present, this is really bad!', 'wordpress-memcached' );
				}
				?>
				</h3>
			</div>
			<br>
			<div>
			<?php
				if ( function_exists( 'wordpress_memcached_get_stats' ) ) {
					$stats_text = wordpress_memcached_get_stats();
					echo nl2br( $stats_text );
				}
			?>
			</div>
	</div>
	<?php
}