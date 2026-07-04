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
 *   - Runs on single blog posts only.
 *   - Only adds the notice when the post actually contains an affiliate link
 *     (geni.us, amazon.*, amzn.to, or your Cloudflare redirector).
 *   - Inserts it at the BOTTOM of the post content. Won't double-add if present.
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

	if ( is_admin() || ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	// --- BLOG CATEGORY ONLY (remove this block later to go site-wide) ---------
	if ( ! has_category( 'blog-post', get_the_ID() ) ) {
		return $content;
	}
	// --------------------------------------------------------------------------

	// Only on posts that actually carry an affiliate link.
	if ( ! preg_match( '#geni\.us|amazon\.[a-z.]+|amzn\.to|amazon-redirector\.phillipstrang\.workers\.dev#i', $content ) ) {
		return $content;
	}

	// Don't add twice if it's somehow already there.
	if ( false !== stripos( $content, 'As an Amazon Associate' ) ) {
		return $content;
	}

	$notice = '<p class="ps-affiliate-disclosure" style="font-size:0.82em;line-height:1.5;color:#777;font-style:italic;margin:0 0 22px;padding:10px 14px;background:#faf9f7;border-left:3px solid #c8a951;border-radius:3px;">'
		. 'As an Amazon Associate, Phillip Strang earns from qualifying purchases. This page contains affiliate links &mdash; buying through them costs you nothing extra.'
		. '</p>';

	return $content . $notice;
}
