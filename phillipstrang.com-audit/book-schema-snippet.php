<?php
/**
 * Phillip Strang — automatic Book schema (JSON-LD) for every book page.
 * ELEMENTOR-SAFE VERSION.
 *
 * WHY THIS VERSION
 *   The book pages are built with Elementor, which stores content in its own
 *   "_elementor_data" field, NOT in WordPress's post_content. So reading the
 *   post text finds nothing. Instead this reads the FINAL RENDERED HTML (the
 *   same output the schema validator sees) and pulls the cover + buy link from
 *   there — guaranteed to exist because they're on the visible page.
 *
 * WHAT IT DOES
 *   Injects one schema.org/Book block into the <head> of each leaf book page:
 *     - name          <- og:title (the " - Phillip Strang" suffix is stripped)
 *     - image         <- og:image (the cover)
 *     - datePublished <- the post's own published date
 *     - isPartOf       <- the parent series page (title + URL)
 *     - ReadAction     <- the geni.us buy link found on the page
 *   No price, ISBN, or rating is invented, so it validates clean. It references
 *   Yoast's existing entity via {"@id": ".../#organization"}.
 *
 * WHERE IT FIRES (leaf book pages only)
 *   - It's a Page (not the front page) with NO child pages.
 *   - The rendered page contains a geni.us link (the book's buy link).
 *   - The rendered page has an og:image (the cover).
 *   Series/landing pages have children, so they're skipped.
 *
 * INSTALL
 *   Plugins -> Code Snippets -> Add New -> paste everything BELOW the opening
 *   <?php line -> "Run everywhere" -> Save & Activate.
 *
 * VERIFY
 *   Book page -> View Source -> find <script type="application/ld+json"> with
 *   "@type":"Book", then run the URL through https://validator.schema.org/.
 */

add_action( 'template_redirect', 'ps_book_schema_start_buffer' );

function ps_book_schema_start_buffer() {

	if ( is_admin() || is_feed() || is_front_page() || ! is_page() ) {
		return;
	}

	global $post;
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	// Leaf pages only — series/landing pages have children and are skipped.
	$children = get_children( array(
		'post_parent' => $post->ID,
		'post_type'   => 'page',
		'numberposts' => 1,
		'post_status' => 'publish',
	) );
	if ( ! empty( $children ) ) {
		return;
	}

	// Capture the reliable server-side facts now; the rest comes from the HTML.
	$GLOBALS['ps_book_ctx'] = array(
		'url'  => get_permalink( $post ),
		'date' => get_the_date( 'Y-m-d', $post ),
	);
	if ( $post->post_parent ) {
		$parent = get_post( $post->post_parent );
		if ( $parent instanceof WP_Post ) {
			$GLOBALS['ps_book_ctx']['series'] = array(
				'name' => get_the_title( $parent ),
				'url'  => get_permalink( $parent ),
			);
		}
	}

	ob_start( 'ps_book_schema_inject' );
}

function ps_book_schema_inject( $html ) {

	if ( empty( $GLOBALS['ps_book_ctx'] ) || ! is_string( $html ) || stripos( $html, '</head>' ) === false ) {
		return $html;
	}
	$ctx = $GLOBALS['ps_book_ctx'];

	// Buy link — the book's geni.us universal link (first match on the page).
	if ( ! preg_match( '#https?://geni\.us/[A-Za-z0-9._~/-]+#', $html, $mBuy ) ) {
		return $html; // not a book page
	}
	$buy_link = $mBuy[0];

	// Cover — from og:image (handles content="..." in either attribute order).
	if ( ! preg_match( '#<meta[^>]+property=["\']og:image["\'][^>]*content=["\']([^"\']+)["\']#i', $html, $mImg )
	  && ! preg_match( '#<meta[^>]+content=["\']([^"\']+)["\'][^>]*property=["\']og:image["\']#i', $html, $mImg ) ) {
		return $html;
	}
	$image = $mImg[1];

	// Title — prefer og:title, fall back to <title>. Strip the author suffix.
	$title = '';
	if ( preg_match( '#<meta[^>]+property=["\']og:title["\'][^>]*content=["\']([^"\']+)["\']#i', $html, $mT )
	  || preg_match( '#<title>(.*?)</title>#is', $html, $mT ) ) {
		$title = $mT[1];
	}
	$title = trim( preg_replace( '/\s*[-–|]\s*Phillip Strang\s*$/iu', '', html_entity_decode( $title, ENT_QUOTES ) ) );
	if ( '' === $title ) {
		return $html;
	}

	$book = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'Book',
		'@id'        => $ctx['url'] . '#book',
		'name'       => $title,
		'url'        => $ctx['url'],
		'image'      => $image,
		'inLanguage' => 'en-US',
		'genre'      => array( 'Crime fiction', 'Mystery', 'Thriller' ),
		'bookFormat' => 'https://schema.org/EBook',
		'datePublished' => $ctx['date'],
		'author'     => array(
			'@type' => 'Person',
			'name'  => 'Phillip Strang',
			'url'   => home_url( '/about/' ),
		),
		'publisher'  => array( '@id' => home_url( '/#organization' ) ),
	);

	if ( ! empty( $ctx['series'] ) ) {
		$book['isPartOf'] = array(
			'@type' => 'BookSeries',
			'name'  => $ctx['series']['name'],
			'url'   => $ctx['series']['url'],
		);
	}

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

	$json = wp_json_encode( $book, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
	if ( false === $json ) {
		return $html;
	}

	$script = "\n<script type=\"application/ld+json\">\n" . $json . "\n</script>\n";
	$pos    = stripos( $html, '</head>' );

	return substr( $html, 0, $pos ) . $script . substr( $html, $pos );
}
