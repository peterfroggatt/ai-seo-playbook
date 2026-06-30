<?php
/**
 * Plugin Name: Audio Tech Expert — Security Hardening
 * Description: Closes REST user enumeration (login-slug exposure), removes the WordPress version fingerprint, and strips X-Powered-By at runtime. Complements the .htaccess block. Fixes audit item H-6 (and supports H-3).
 * Version:     1.0.0
 * Author:      SEO audit remediation
 *
 * INSTALL: drop into  wp-content/mu-plugins/  (auto-activates). Remove the file to revert.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Remove the WordPress version fingerprint from <head>, feeds, and the generator tag. */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/* Strip X-Powered-By at response time (backup to .htaccess / php expose_php). */
add_action(
	'send_headers',
	function () {
		if ( ! headers_sent() ) {
			@header_remove( 'X-Powered-By' );
		}
	}
);

/* Disable REST user enumeration for unauthenticated visitors (H-6).
 * Logged-in users (admins, editors, internal tools) keep full access. */
add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		if ( is_user_logged_in() ) {
			return $endpoints;
		}
		foreach ( array( '/wp/v2/users', '/wp/v2/users/(?P<id>[\d]+)' ) as $route ) {
			if ( isset( $endpoints[ $route ] ) ) {
				unset( $endpoints[ $route ] );
			}
		}
		return $endpoints;
	}
);
