<?php
/**
 * Phillip Strang — normalise blog posts to exactly ONE H1.
 *
 * WHY
 *   The programmatic posts were built by several templates, so headings are
 *   inconsistent:
 *     - Some have TWO H1s  (theme .entry-title H1 + a second in-content H1).
 *     - Some have ZERO H1s (theme title disabled + content uses only H2s).
 *   Google wants exactly one H1 per page. This fixes every post at once, and
 *   any post published later, with no per-page editing.
 *
 * WHAT IT DOES  (on single posts only)
 *   1. If the page has 2+ <h1> tags, it keeps the FIRST (the theme's proper
 *      post-title H1) and demotes every later <h1> to <h2>.
 *   2. If the page has ZERO <h1> tags, it promotes the first real <h2> in the
 *      content to <h1> (skips tiny widget labels < 15 chars).
 *   Pages that already have exactly one H1 are left untouched.
 *
 * SAFE BY DESIGN
 *   - Runs on is_single() posts only — never pages, archives, home, or admin.
 *   - Only ever changes an <h1> to <h2> or one <h2> to <h1>; never deletes
 *     content or touches anything else.
 *
 * INSTALL
 *   Plugins -> Code Snippets -> Add New -> paste everything BELOW the opening
 *   <?php line -> "Run everywhere" -> Save & Activate.
 *   Then LiteSpeed -> Toolbox -> Purge All so cached pages rebuild.
 */

add_action( 'template_redirect', 'ps_h1_normalize_start' );

function ps_h1_normalize_start() {
	if ( is_admin() || is_feed() || ! is_single() ) {
		return;
	}
	ob_start( 'ps_h1_normalize' );
}

function ps_h1_normalize( $html ) {

	if ( ! is_string( $html ) || stripos( $html, '<h1' ) === false && stripos( $html, '<h2' ) === false ) {
		return $html;
	}

	// Count H1s.
	$h1_count = preg_match_all( '/<h1[\s>]/i', $html );

	if ( 1 === $h1_count ) {
		return $html; // already correct
	}

	if ( $h1_count > 1 ) {
		// Keep the first <h1>…</h1>; demote the rest to <h2>.
		$seen = 0;
		$html = preg_replace_callback(
			'/<h1(\b[^>]*)>(.*?)<\/h1>/is',
			function ( $m ) use ( &$seen ) {
				$seen++;
				if ( 1 === $seen ) {
					return $m[0]; // leave the first H1 alone
				}
				return '<h2' . $m[1] . '>' . $m[2] . '</h2>';
			},
			$html
		);
		return $html;
	}

	// h1_count === 0 : promote the first substantial <h2> to <h1>.
	$done = false;
	$html = preg_replace_callback(
		'/<h2(\b[^>]*)>(.*?)<\/h2>/is',
		function ( $m ) use ( &$done ) {
			if ( $done ) {
				return $m[0];
			}
			$text = trim( wp_strip_all_tags( $m[2] ) );
			if ( mb_strlen( $text ) < 15 ) {
				return $m[0]; // skip short widget labels ("Menu", "Social", etc.)
			}
			$done = true;
			return '<h1' . $m[1] . '>' . $m[2] . '</h1>';
		},
		$html
	);

	return $html;
}
