<?php
/**
 * Plugin Name: Audio Tech Expert — Roundup ItemList + Product Schema (AAWP Pro)
 * Description: Adds ItemList + Product + Offer schema to "best of" roundup posts, reading product name / price / image / affiliate URL directly from AAWP Pro's rendered output (so the schema price always matches what the visitor sees). Fixes audit item C-5.
 * Version:     2.0.0
 * Author:      SEO audit remediation
 *
 * Requires: AAWP Pro (uses the data-aawp-product-* attributes + .aawp-product__price--current
 *           markup it renders). Requires Yoast SEO (wpseo_schema_graph filter).
 *
 * INSTALL: place in wp-content/mu-plugins/ (overwrites the earlier names-only v1 of the
 *          same filename — keep only ONE copy). Remove the file to revert.
 *
 * PRICE ACCURACY: the Offer price is parsed from AAWP's own .aawp-product__price--current
 * value, i.e. the exact figure shown on the page. Result is cached in post meta for 6h and
 * rebuilt on save, so it tracks AAWP's own price-refresh cycle. Verify a couple of posts in
 * Google's Rich Results Test after installing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ATE_ITEMLIST_META = '_ate_itemlist_cache';
const ATE_ITEMLIST_TTL  = 6 * HOUR_IN_SECONDS;

/* Rebuild cache when a post is saved. */
add_action(
	'save_post_post',
	function ( $post_id ) {
		delete_post_meta( $post_id, ATE_ITEMLIST_META );
	}
);

add_filter(
	'wpseo_schema_graph',
	function ( $graph, $context = null ) {
		if ( ! is_array( $graph ) || ! is_singular( 'post' ) ) {
			return $graph;
		}
		$post = get_post();
		if ( ! $post || stripos( $post->post_name, 'best' ) === false ) {
			return $graph; // roundups only
		}

		$products = ate_roundup_products( $post );
		if ( count( $products ) < 2 ) {
			return $graph;
		}

		$permalink = get_permalink( $post );
		$elements  = array();
		$pos       = 1;
		foreach ( $products as $p ) {
			$product = array(
				'@type' => 'Product',
				'name'  => $p['name'],
			);
			if ( ! empty( $p['image'] ) ) {
				$product['image'] = $p['image'];
			}
			if ( ! empty( $p['asin'] ) ) {
				$product['sku'] = $p['asin'];
			}
			if ( ! empty( $p['price'] ) && ! empty( $p['currency'] ) ) {
				$product['offers'] = array(
					'@type'         => 'Offer',
					'price'         => $p['price'],
					'priceCurrency' => $p['currency'],
					'availability'  => 'https://schema.org/InStock',
					'url'           => $p['url'],
				);
			}
			$elements[] = array(
				'@type'    => 'ListItem',
				'position' => $pos++,
				'name'     => $p['name'],
				'item'     => $product,
			);
		}

		$graph[] = array(
			'@type'           => 'ItemList',
			'@id'             => trailingslashit( $permalink ) . '#itemlist',
			'name'            => get_the_title( $post ),
			'numberOfItems'   => count( $elements ),
			'itemListElement' => $elements,
		);

		return $graph;
	},
	30,
	2
);

/**
 * Return de-duplicated product data for a roundup post, cached in post meta.
 * Each item: array(asin, name, price, currency, image, url).
 */
function ate_roundup_products( $post ) {
	$cache = get_post_meta( $post->ID, ATE_ITEMLIST_META, true );
	if ( is_array( $cache ) && isset( $cache['t'], $cache['items'] ) && ( time() - (int) $cache['t'] ) < ATE_ITEMLIST_TTL ) {
		return $cache['items'];
	}

	$items    = array();
	$rendered = do_blocks( do_shortcode( $post->post_content ) );
	if ( strpos( $rendered, 'aawp-product' ) === false ) {
		update_post_meta( $post->ID, ATE_ITEMLIST_META, array( 't' => time(), 'items' => $items ) );
		return $items;
	}

	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="utf-8"?>' . $rendered );
	libxml_clear_errors();
	$xp = new DOMXPath( $dom );

	$nodes = $xp->query( "//*[contains(concat(' ', normalize-space(@class), ' '), ' aawp-product ')]" );
	$seen  = array();
	foreach ( $nodes as $node ) {
		$asin = $node->getAttribute( 'data-aawp-product-asin' );
		if ( ! $asin || isset( $seen[ $asin ] ) ) {
			continue;
		}
		$name = trim( (string) $node->getAttribute( 'data-aawp-product-title' ) );

		// Current price (skip strikethrough / "old" price by targeting --current only).
		$price_raw = '';
		$pnodes    = $xp->query( ".//*[contains(concat(' ', normalize-space(@class), ' '), ' aawp-product__price--current ')]", $node );
		if ( $pnodes->length ) {
			$price_raw = trim( $pnodes->item( 0 )->textContent );
		}
		list( $price, $currency ) = ate_parse_price( $price_raw );

		// First Amazon affiliate link in the box.
		$url = '';
		foreach ( $xp->query( './/a[@href]', $node ) as $a ) {
			$href = $a->getAttribute( 'href' );
			if ( false !== strpos( $href, 'amazon' ) || false !== strpos( $href, 'amzn' ) ) {
				$url = $href;
				break;
			}
		}

		// First Amazon product image in the box.
		$image = '';
		foreach ( $xp->query( './/img', $node ) as $im ) {
			$src = $im->getAttribute( 'src' );
			if ( ! $src ) {
				$src = $im->getAttribute( 'data-src' );
			}
			if ( $src && false !== strpos( $src, 'media-amazon' ) ) {
				$image = $src;
				break;
			}
		}

		if ( '' === $name || '' === $url ) {
			continue;
		}
		$seen[ $asin ] = true;
		$items[]       = array(
			'asin'     => $asin,
			'name'     => $name,
			'price'    => $price,
			'currency' => $currency,
			'image'    => $image,
			'url'      => $url,
		);
	}

	update_post_meta( $post->ID, ATE_ITEMLIST_META, array( 't' => time(), 'items' => $items ) );
	return $items;
}

/** Parse "$67.99" / "£1,299.00" -> array('67.99','USD'). Returns array('','') on failure. */
function ate_parse_price( $raw ) {
	if ( ! $raw || ! preg_match( '/([$£€])\s?([0-9][0-9,]*(?:\.[0-9]{1,2})?)/u', $raw, $m ) ) {
		return array( '', '' );
	}
	$map      = array( '$' => 'USD', '£' => 'GBP', '€' => 'EUR' );
	$currency = isset( $map[ $m[1] ] ) ? $map[ $m[1] ] : '';
	$price    = str_replace( ',', '', $m[2] );
	return array( $price, $currency );
}
