<?php
/**
 * Plugin Name: Audio Tech Expert — Unsplash → Self-Hosted Rewrite
 * Description: Rewrites hotlinked images.unsplash.com/photo-* URLs to your self-hosted copies in wp-content/uploads (so LiteSpeed can WebP them and you drop the third-party dependency) — wherever they appear in rendered content: Elementor HTML widgets, inline CSS backgrounds, and <img> tags. Only rewrites photos you've actually uploaded; any not-yet-uploaded photo is left untouched (never broken). Avoids hand-editing every landing page.
 * Version:     1.0.0
 * Author:      SEO audit remediation
 *
 * INSTALL: wp-content/mu-plugins/ (auto-activates). Then LiteSpeed Cache > Toolbox > Purge All.
 * Uploaded files must be named after the Unsplash photo id (e.g. photo-1484704849700-f032a568e944.jpg).
 * Remove the file to revert to the original (hotlinked) behaviour.
 *
 * NOTE: this rewrites at render time; the page source (Elementor data) still holds the Unsplash
 * URLs. That's intentional and reversible. If you later bake the URLs into the pages by hand,
 * you can delete this plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return map of uploaded "photo-*" basenames -> URL, cached 12h.
 * Scans the uploads dir so it picks up whatever month WordPress filed them under.
 */
function ate_uploaded_unsplash_map() {
	$map = get_transient( 'ate_unsplash_map' );
	if ( is_array( $map ) ) {
		return $map;
	}
	$map = array();
	$up  = wp_upload_dir();
	// Look in the current and previous month folders (where these uploads land).
	foreach ( array( gmdate( 'Y/m' ), gmdate( 'Y/m', strtotime( '-1 month' ) ) ) as $ym ) {
		$dir = trailingslashit( $up['basedir'] ) . $ym . '/';
		$url = trailingslashit( $up['baseurl'] ) . $ym . '/';
		foreach ( glob( $dir . 'photo-*.jpg' ) ?: array() as $path ) {
			$base = basename( $path, '.jpg' ); // e.g. photo-1484704849700-f032a568e944
			$map[ $base ] = $url . basename( $path );
		}
	}
	set_transient( 'ate_unsplash_map', $map, 12 * HOUR_IN_SECONDS );
	return $map;
}

function ate_unsplash_rewrite( $html ) {
	if ( ! is_string( $html ) || false === strpos( $html, 'images.unsplash.com' ) ) {
		return $html;
	}
	$map = ate_uploaded_unsplash_map();
	if ( empty( $map ) ) {
		return $html;
	}
	return preg_replace_callback(
		'#https://images\.unsplash\.com/(photo-[a-z0-9-]+)\?[^"\'\s)]*#i',
		function ( $m ) use ( $map ) {
			return isset( $map[ $m[1] ] ) ? $map[ $m[1] ] : $m[0];
		},
		$html
	);
}

// Late on the_content so it runs after Elementor injects the builder output.
add_filter( 'the_content', 'ate_unsplash_rewrite', 999 );

// Refresh the map when media changes.
add_action( 'add_attachment', function () { delete_transient( 'ate_unsplash_map' ); } );
