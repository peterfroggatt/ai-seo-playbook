<?php
/**
 * Phillip Strang - fix book-page TITLE tags and missing H1s.
 *
 * PROBLEM (found live on e.g. /tremayne/burial-mound/)
 *   1. <title>Burial Mound -</title>  -> dangling separator, no brand. Every book
 *      page's SERP snippet looks broken. og:title is wrong too.
 *   2. The page has NO <h1>. The book title renders as an <h2> ("BURIAL MOUND"),
 *      so Google can't see the page's main heading.
 *
 * WHAT THIS DOES
 *   A. Title: strips a trailing dangling separator and appends "— Phillip Strang"
 *      to any title that doesn't already contain the brand. Leaves already-correct
 *      titles (home, About, Blog "... - Phillip Strang") untouched.
 *   B. H1: on any single page/post that has NO <h1>, promotes the first heading
 *      whose text equals the page title (the book title) from <h2>/<h3> to <h1>.
 *      Pages that already have an <h1> are never touched.
 *
 * INSTALL
 *   Code Snippets -> Add New -> paste below the <?php line -> "Run everywhere"
 *   -> Save & Activate -> LiteSpeed Purge All. Re-check a book page's <title>
 *   and view-source for <h1>.
 *
 * NOTE (the "proper" underlying title fix, optional)
 *   Yoast -> Search Appearance -> Content Types -> Pages -> "SEO title" template
 *   should read:  %%title%% %%sep%% %%sitename%%
 *   If it's missing %%sitename%% (or the site title is blank) that's the root
 *   cause. This snippet fixes the output either way, so it's safe to run now and
 *   tidy the template later.
 */

/* ---------- A. TITLE FIX (meta title + OpenGraph + Twitter) ---------- */

function ps_fix_dangling_title( $title ) {
	$brand = 'Phillip Strang';
	$t = trim( (string) $title );
	if ( $t === '' ) {
		return $title;
	}
	// Remove a trailing dangling separator: " -", " |", " –", " —".
	$t = preg_replace( '/\s*[-|–—]+\s*$/u', '', $t );
	// Append the brand only if it's not already present.
	if ( stripos( $t, $brand ) === false ) {
		$t .= ' — ' . $brand;
	}
	return $t;
}
add_filter( 'wpseo_title',            'ps_fix_dangling_title', 20 );
add_filter( 'wpseo_opengraph_title',  'ps_fix_dangling_title', 20 );
add_filter( 'wpseo_twitter_title',    'ps_fix_dangling_title', 20 );
// Fallback for the raw <title> if Yoast isn't filtering a given view.
add_filter( 'document_title_parts', function( $parts ) {
	if ( ! empty( $parts['title'] ) && empty( $parts['site'] ) ) {
		$parts['title'] = ps_fix_dangling_title( $parts['title'] );
	}
	return $parts;
}, 20 );

/* ---------- B. H1 FIX (promote the book-title heading) ---------- */

add_action( 'template_redirect', function () {
	if ( is_admin() || is_feed() || is_robots() || ! is_singular() ) {
		return;
	}
	ob_start( 'ps_promote_title_to_h1' );
} );

function ps_promote_title_to_h1( $html ) {
	if ( ! is_string( $html ) || $html === '' ) {
		return $html;
	}
	// Already has an H1 anywhere? Leave the page alone.
	if ( preg_match( '/<h1[\s>]/i', $html ) ) {
		return $html;
	}
	$obj = get_queried_object();
	if ( ! $obj || empty( $obj->post_title ) ) {
		return $html;
	}
	$title = trim( html_entity_decode( wp_strip_all_tags( $obj->post_title ), ENT_QUOTES, 'UTF-8' ) );
	if ( $title === '' ) {
		return $html;
	}
	$done = false;
	$html = preg_replace_callback(
		'/<(h2|h3)\b([^>]*)>(.*?)<\/\1>/is',
		function ( $m ) use ( $title, &$done ) {
			if ( $done ) {
				return $m[0];
			}
			$text = trim( html_entity_decode( wp_strip_all_tags( $m[3] ), ENT_QUOTES, 'UTF-8' ) );
			if ( function_exists( 'mb_strtolower' ) ) {
				$match = mb_strtolower( $text ) === mb_strtolower( $title );
			} else {
				$match = strcasecmp( $text, $title ) === 0;
			}
			if ( $match ) {
				$done = true;
				return '<h1' . $m[2] . '>' . $m[3] . '</h1>';
			}
			return $m[0];
		},
		$html
	);
	return $html;
}
