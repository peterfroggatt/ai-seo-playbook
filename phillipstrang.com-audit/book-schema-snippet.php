<?php
/**
 * Phillip Strang — automatic Book schema (JSON-LD) for every book page.
 *
 * WHAT IT DOES
 *   Outputs one schema.org/Book block in the <head> of each individual book
 *   page, built entirely from data already on the post:
 *     - name          <- post title (the " - Phillip Strang" suffix is stripped)
 *     - url / @id      <- permalink
 *     - image         <- featured image (the cover)
 *     - datePublished <- the post's own published date
 *     - isPartOf       <- the parent series page (title + URL)
 *     - ReadAction     <- the single-book geni.us buy link found in the content
 *   No price, ISBN, or rating is invented, so it validates cleanly and cannot
 *   trigger a review-snippet policy issue. It links into Yoast's existing
 *   entity graph via {"@id": ".../#organization"} instead of duplicating it.
 *
 * WHERE IT FIRES (all three must be true — this targets leaf book pages only)
 *   1. The page has a featured image (the cover).
 *   2. The content contains a geni.us link (the book's universal buy link).
 *   3. The page has NO child pages (series/landing pages have children — skipped).
 *
 * HOW TO INSTALL
 *   Plugins -> Code Snippets -> Add New -> paste EVERYTHING BELOW the opening
 *   <?php line (Code Snippets supplies its own opening tag), "Run everywhere",
 *   Save & Activate. (Or drop the function into the child theme's functions.php,
 *   keeping the <?php tag.)
 *
 * VERIFY
 *   Open any book page -> View Source -> confirm the <script type="application/
 *   ld+json"> Book block is present, then run the URL through
 *   https://validator.schema.org/ and Google's Rich Results Test.
 */

add_action( 'wp_head', 'ps_output_book_schema', 20 );

function ps_output_book_schema() {

	if ( ! is_page() || is_front_page() ) {
		return;
	}

	global $post;
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	// --- Gate 1: must have a single-book geni.us buy link in the content ------
	// Ignore the generic ".../stores/..." author-storefront Amazon link.
	if ( ! preg_match( '#https?://geni\.us/[A-Za-z0-9._~/-]+#', $post->post_content, $m ) ) {
		return;
	}
	$buy_link = $m[0];

	// --- Gate 2: must have a cover (featured image) --------------------------
	$image = get_the_post_thumbnail_url( $post, 'full' );
	if ( ! $image ) {
		return;
	}

	// --- Gate 3: leaf pages only (series/landing pages have children) --------
	$has_children = get_children( array(
		'post_parent' => $post->ID,
		'post_type'   => 'page',
		'numberposts' => 1,
		'post_status' => 'publish',
	) );
	if ( ! empty( $has_children ) ) {
		return;
	}

	// --- Build the Book node -------------------------------------------------
	$url   = get_permalink( $post );
	$title = trim( preg_replace( '/\s*[-–|]\s*Phillip Strang\s*$/iu', '', get_the_title( $post ) ) );

	$book = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'Book',
		'@id'        => $url . '#book',
		'name'       => $title,
		'url'        => $url,
		'image'      => $image,
		'inLanguage' => 'en-US',
		'genre'      => array( 'Crime fiction', 'Mystery', 'Thriller' ),
		'bookFormat' => 'https://schema.org/EBook',
		'datePublished' => get_the_date( 'Y-m-d', $post ),
		'author'     => array(
			'@type' => 'Person',
			'name'  => 'Phillip Strang',
			'url'   => home_url( '/about/' ),
		),
		'publisher'  => array( '@id' => home_url( '/#organization' ) ),
	);

	// Description: prefer a hand-written excerpt, else the Yoast meta description.
	$desc = has_excerpt( $post ) ? get_the_excerpt( $post ) : '';
	if ( ! $desc ) {
		$desc = get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true );
	}
	$desc = trim( wp_strip_all_tags( (string) $desc ) );
	if ( $desc ) {
		$book['description'] = $desc;
	}

	// Series: taken straight from the parent page (name + URL). Auto-covers
	// every series — Cook, Tremayne, Campbell, Reid Harper, etc. — with no map.
	if ( $post->post_parent ) {
		$parent = get_post( $post->post_parent );
		if ( $parent instanceof WP_Post ) {
			$book['isPartOf'] = array(
				'@type' => 'BookSeries',
				'name'  => get_the_title( $parent ),
				'url'   => get_permalink( $parent ),
			);
		}
	}

	// Buy action — Google Book-Actions pattern (no price/ISBN required).
	$book['potentialAction'] = array(
		'@type'  => 'ReadAction',
		'target' => array(
			'@type'          => 'EntryPoint',
			'urlTemplate'    => $buy_link,
			'actionPlatform' => array(
				'https://schema.org/DesktopWebPlatform',
				'https://schema.org/MobileWebPlatform',
			),
		),
		'expectsAcceptanceOf' => array(
			'@type'        => 'Offer',
			'category'     => 'purchase',
			'availability' => 'https://schema.org/InStock',
		),
	);

	echo "\n<script type=\"application/ld+json\">\n";
	echo wp_json_encode( $book, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
	echo "\n</script>\n";
}
