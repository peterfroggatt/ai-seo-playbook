<?php
/**
 * Plugin Name: Audio Tech Expert — LiteSpeed Cache Vary by AAWP Country
 * Description: Registers AAWP's geotargeting cookie (aawp-country) as a LiteSpeed Cache "vary" cookie, so LiteSpeed caches a SEPARATE copy per country. This lets full-page caching and AAWP geotargeting (per-country prices/links) work together — fixing the uncached-HTML / slow-TTFB issue without disabling geotargeting.
 * Version:     1.0.0
 * Author:      SEO audit remediation
 *
 * Requires: LiteSpeed Cache plugin active.
 * INSTALL: drop into wp-content/mu-plugins/ (auto-activates). Then LiteSpeed Cache
 *          > Toolbox > Purge > Purge All. Remove this file to revert.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'litespeed_vary_cookies',
	function ( $cookies ) {
		$cookies[] = 'aawp-country';
		return array_values( array_unique( $cookies ) );
	}
);
