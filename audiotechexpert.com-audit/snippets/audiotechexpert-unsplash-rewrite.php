<?php
/**
 * Plugin Name: Audio Tech Expert — Unsplash → Self-Hosted Rewrite
 * Description: Rewrites hotlinked images.unsplash.com/photo-* URLs to your self-hosted copies — in rendered content (Elementor HTML widgets, inline CSS, <img>) AND in Yoast's og:image / Twitter image / schema ImageObject. Only rewrites photos you've actually uploaded; others are left untouched (never broken). Avoids hand-editing pages. Fixes audit image-hotlinking + category schema-image findings.
 * Version:     1.1.0
 * Author:      SEO audit remediation
 *
 * INSTALL: wp-content/mu-plugins/ (auto-activates). Then LiteSpeed Cache > Toolbox > Purge All.
 * Uploaded files must be named after the Unsplash photo id (e.g. photo-1484704849700-f032a568e944.jpg).
 * Remove the file to revert.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Map of uploaded "photo-*" basenames -> URL, cached 12h. */
function ate_uploaded_unsplash_map() {
	$map = get_transient( 'ate_unsplash_map' );
	if ( is_array( $map ) ) {
		return $map;
	}
	$map = array();
	$up  = wp_upload_dir();
	foreach ( array( gmdate( 'Y/m' ), gmdate( 'Y/m', strtotime( '-1 month' ) ) ) as $ym ) {
		$dir = trailingslashit( $up['basedir'] ) . $ym . '/';
		$url = trailingslashit( $up['baseurl'] ) . $ym . '/';
		foreach ( glob( $dir . 'photo-*.jpg' ) ?: array() as $path ) {
			$map[ basename( $path, '.jpg' ) ] = $url . basename( $path );
		}
	}
	set_transient( 'ate_unsplash_map', $map, 12 * HOUR_IN_SECONDS );
	return $map;
}

/** Rewrite a single URL if it's an uploaded Unsplash photo; else return unchanged. */
function ate_unsplash_map_url( $url ) {
	if ( ! is_string( $url ) || false === strpos( $url, 'images.unsplash.com' ) ) {
		return $url;
	}
	$map = ate_uploaded_unsplash_map();
	return preg_replace_callback(
		'#https?:(?:\\\\/\\\\/|//)images\.unsplash\.com(?:\\\\/|/)(photo-[a-z0-9-]+)\?[^"\'\s)]*#i',
		function ( $m ) use ( $map ) {
			return isset( $map[ $m[1] ] ) ? $map[ $m[1] ] : $m[0];
		},
		$url
	);
}

/** Rewrite all occurrences inside a blob of HTML/JSON. */
function ate_unsplash_rewrite( $html ) {
	if ( ! is_string( $html ) || false === strpos( $html, 'images.unsplash.com' ) ) {
		return $html;
	}
	if ( empty( ate_uploaded_unsplash_map() ) ) {
		return $html;
	}
	return ate_unsplash_map_url( $html );
}

/** Recursively rewrite image URLs inside Yoast's schema graph. */
function ate_unsplash_rewrite_graph( $data ) {
	if ( is_array( $data ) ) {
		foreach ( $data as $k => $v ) {
			$data[ $k ] = ate_unsplash_rewrite_graph( $v );
		}
		return $data;
	}
	return is_string( $data ) ? ate_unsplash_map_url( $data ) : $data;
}

// 1) Rendered content (Elementor builder output runs at priority 9).
add_filter( 'the_content', 'ate_unsplash_rewrite', 999 );

// 2) Yoast Open Graph + Twitter images (head meta tags).
add_filter( 'wpseo_opengraph_image', 'ate_unsplash_map_url', 20 );
add_filter( 'wpseo_twitter_image', 'ate_unsplash_map_url', 20 );

// 3) Yoast schema graph ImageObject (primaryImageOfPage / thumbnailUrl / contentUrl).
add_filter( 'wpseo_schema_graph', function ( $graph ) { return ate_unsplash_rewrite_graph( $graph ); }, 999 );

// Refresh the map when media changes.
add_action( 'add_attachment', function () { delete_transient( 'ate_unsplash_map' ); } );
