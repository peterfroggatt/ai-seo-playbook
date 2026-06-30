<?php
/**
 * Plugin Name: Audio Tech Expert — Roundup ItemList Schema
 * Description: Adds ItemList + Product schema to "best of" roundup posts, built from the product H2 headings on the page. Fixes audit item C-5 (no ItemList on roundups).
 * Version:     1.0.0
 * Author:      SEO audit remediation
 *
 * INSTALL: drop into  wp-content/mu-plugins/  (auto-activates). Remove the file to revert.
 *
 * SCOPE / LIMITS — read this:
 *  - Runs only on single posts whose slug contains "best" (your roundups). Tune
 *    the match below if some roundups use a different slug pattern (e.g. "top-").
 *  - Builds the list from the post's <h2> product headings, skipping non-product
 *    sections (How to choose / FAQ / Verdict / etc.). It is a heuristic — ALWAYS
 *    verify a couple of posts in Google's Rich Results Test after installing, and
 *    add any stray heading words to the $skip pattern.
 *  - PRICE IS INTENTIONALLY OMITTED. Google requires Offer/price to be live-accurate;
 *    AAWP prices change independently. A price-bearing version must read AAWP's cached
 *    product data (needs your AAWP version: Lite vs Pro) — ask for that as a follow-up.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

		$names = ate_roundup_product_names( $post->post_content );
		if ( count( $names ) < 2 ) {
			return $graph; // not enough product picks found -> emit nothing (safe)
		}

		$permalink = get_permalink( $post );
		$elements  = array();
		foreach ( array_slice( $names, 0, 15 ) as $i => $name ) {
			$elements[] = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => $name,
				'item'     => array(
					'@type' => 'Product',
					'name'  => $name,
					'url'   => $permalink,
				),
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
 * Extract likely product names from a roundup's <h2> headings.
 * Returns a de-duplicated, order-preserving list of strings.
 */
function ate_roundup_product_names( $content ) {
	$names = array();
	if ( ! preg_match_all( '/<h2[^>]*>(.*?)<\/h2>/is', (string) $content, $m ) ) {
		return $names;
	}
	// Headings that are sections, not products.
	$skip = '/\b(how to|how we|why|what|faq|frequently asked|buying|guide|conclusion|verdict|final word|final thoughts|table of|comparison|compared|about|methodology|tips|things to|consider|takeaway|summary|recommend|overview|introduction|which|should you)\b/i';
	foreach ( $m[1] as $h ) {
		$name = trim( wp_strip_all_tags( html_entity_decode( $h, ENT_QUOTES ) ) );
		if ( '' === $name || mb_strlen( $name ) > 110 ) {
			continue;
		}
		if ( preg_match( $skip, $name ) ) {
			continue;
		}
		// Strip a leading role label like "Best overall:" / "Best for gaming:" so
		// the schema carries the clean product name, not the editorial label.
		$name = trim( preg_replace( '/^\s*best\b[^:]{0,60}:\s*/i', '', $name ) );
		if ( '' === $name ) {
			continue;
		}
		if ( ! in_array( $name, $names, true ) ) {
			$names[] = $name;
		}
	}
	return $names;
}
