<?php
/**
 * Phillip Strang - automatic affiliate disclosure on blog posts.
 *
 * WHY
 *   Posts contain Amazon/geni.us affiliate links, so a disclosure is required
 *   by the FTC/ASA AND by Amazon's Associates Operating Agreement - which
 *   mandates the exact phrase "As an Amazon Associate I earn from qualifying
 *   purchases." This adds a compliant line automatically, so you never have to
 *   remember it per post.
 *
 * WHAT IT DOES
 *   - Runs on single posts AND pages (covers the ~14 reading-guide Pages and
 *     the book Pages, not just Posts).
 *   - Only adds the notice when the content actually contains an affiliate link
 *     (geni.us, amazon.*, amzn.to, or your Cloudflare redirector).
 *   - Inserts it at the BOTTOM of the content. Won't double-add if present.
 *
 * INSTALL
 *   Code Snippets -> Add New -> paste everything below the <?php line ->
 *   "Run everywhere" -> Save & Activate -> LiteSpeed -> Purge All.
 *
 * MOVE TO TOP instead? change the final line
 *   from:  return $content . $notice;
 *   to:    return $notice . $content;
 */

// Priority 20 = run AFTER page builders (Elementor injects content at 9), so
// $content contains the rendered article + affiliate links when we check.
add_filter( 'the_content', 'ps_affiliate_disclosure', 20 );

function ps_affiliate_disclosure( $content ) {

	// Posts AND pages, but never the admin, feeds, or the front page.
	if ( is_admin() || is_feed() || is_front_page() || ! is_singular() || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	// Site-wide: runs on every post; the affiliate-link check below means the
	// notice only appears where there's actually a link to disclose.

	// Only on posts that actually carry an affiliate link.
	if ( ! preg_match( '#geni\.us|amazon\.[a-z.]+|amzn\.to|amazon-redirector\.phillipstrang\.workers\.dev#i', $content ) ) {
		return $content;
	}

	// Don't add if ANY affiliate disclaimer is already on the page (many pages
	// have a hand-coded one in their footer) - avoids duplicates.
	if ( preg_match( '/affiliate link|As an Amazon Associate/i', $content ) ) {
		return $content;
	}

	// Centred block, constrained to the content width so it never sits far-left.
	$notice = '<p class="ps-affiliate-disclosure" style="max-width:860px;margin:6px auto 22px;font-size:0.82em;line-height:1.5;color:#777;font-style:italic;padding:10px 14px;background:#faf9f7;border-left:3px solid #c8a951;border-radius:3px;text-align:center;">'
		. 'As an Amazon Associate, Phillip Strang earns from qualifying purchases. This page contains affiliate links &mdash; buying through them costs you nothing extra.'
		. '</p>';

	return $content . $notice;
}
