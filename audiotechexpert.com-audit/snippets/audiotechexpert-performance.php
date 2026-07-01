<?php
/**
 * Plugin Name: Audio Tech Expert — Performance
 * Description: Safe, low-risk performance wins that LiteSpeed doesn't cover: preconnect to the Amazon image CDN (AAWP product images), drop the unused jQuery Migrate shim, and mark below-the-fold decoding async. Measured issues: render-blocking jQuery + cross-origin AAWP images with no preconnect.
 * Version:     1.0.0
 * Author:      SEO audit remediation
 *
 * INSTALL: wp-content/mu-plugins/ (auto-activates). Keep exactly ONE copy (delete any "(1)" duplicate).
 * Reversible: delete this file. Guarded against double-load so a duplicate can't fatal the site.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ATE_PERF_LOADED' ) ) {
	define( 'ATE_PERF_LOADED', true );

	// Toggle jQuery Migrate removal off by flipping this to false if any legacy script misbehaves.
	if ( ! defined( 'ATE_PERF_DROP_JQUERY_MIGRATE' ) ) {
		define( 'ATE_PERF_DROP_JQUERY_MIGRATE', true );
	}

	/* ---- 1. Preconnect + dns-prefetch to the Amazon image CDN (AAWP product images) ---- */
	add_filter(
		'wp_resource_hints',
		function ( $hints, $relation_type ) {
			if ( 'preconnect' === $relation_type ) {
				$hints[] = array(
					'href'        => 'https://m.media-amazon.com',
					'crossorigin' => 'anonymous',
				);
			}
			if ( 'dns-prefetch' === $relation_type ) {
				$hints[] = 'https://m.media-amazon.com';
			}
			return $hints;
		},
		10,
		2
	);

	/* ---- 2. Drop the jQuery Migrate shim (render-blocking, not needed on modern Astra/Elementor) ---- */
	if ( ATE_PERF_DROP_JQUERY_MIGRATE ) {
		add_action(
			'wp_default_scripts',
			function ( $scripts ) {
				if ( is_admin() ) {
					return;
				}
				$jq = isset( $scripts->registered['jquery'] ) ? $scripts->registered['jquery'] : null;
				if ( $jq && ! empty( $jq->deps ) ) {
					$jq->deps = array_diff( $jq->deps, array( 'jquery-migrate' ) );
				}
			}
		);
	}

	/* ---- 3. Mark content images decoding=async (safe; lets the browser paint text without waiting on images) ---- */
	add_filter(
		'the_content',
		function ( $content ) {
			if ( is_admin() || empty( $content ) ) {
				return $content;
			}
			return preg_replace_callback(
				'/<img\b(?![^>]*\bdecoding=)[^>]*>/i',
				function ( $m ) {
					return str_replace( '<img', '<img decoding="async"', $m[0] );
				},
				$content
			);
		},
		99
	);
}
