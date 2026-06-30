<?php
/**
 * Plugin Name: Audio Tech Expert — Schema Enhancements
 * Description: Adds Organization schema and enriches the author Person node in Yoast's schema graph. Fixes SEO-audit items C-3 (no Organization), W-3 (Person.sameAs self-only), W-4 (Person not linked to publisher).
 * Version:     1.0.0
 * Author:      SEO audit remediation
 *
 * INSTALL: drop this file into  wp-content/mu-plugins/  (create that folder if
 * it does not exist). "mu" = must-use: it activates automatically, no plugin
 * activation needed. Remove the file to fully revert.
 *
 * Requires Yoast SEO (uses its `wpseo_schema_graph` filter). Safe no-op if Yoast
 * is inactive (the filter simply never fires).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---- Config: edit only if you want to override the defaults ---- */
if ( ! defined( 'ATE_ORG_NAME' ) ) {
	define( 'ATE_ORG_NAME', 'Audio Tech Expert' );
}
// Leave empty to auto-detect the theme's Customizer logo / site icon.
if ( ! defined( 'ATE_LOGO_URL' ) ) {
	define( 'ATE_LOGO_URL', '' );
}
// External identity for the author (used in Person.sameAs).
if ( ! defined( 'ATE_AUTHOR_SAMEAS' ) ) {
	define( 'ATE_AUTHOR_SAMEAS', 'https://phillipstrang.com' );
}

add_filter(
	'wpseo_schema_graph',
	function ( $graph, $context = null ) {
		if ( ! is_array( $graph ) ) {
			return $graph;
		}

		$home   = trailingslashit( home_url() );
		$org_id = $home . '#organization';

		/* Resolve a logo URL: constant override -> Customizer logo -> site icon. */
		$logo = ATE_LOGO_URL;
		if ( ! $logo ) {
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			if ( $custom_logo_id ) {
				$logo = wp_get_attachment_image_url( $custom_logo_id, 'full' );
			}
		}
		if ( ! $logo ) {
			$logo = get_site_icon_url();
		}

		$logo_node = $logo ? array(
			'@type'      => 'ImageObject',
			'@id'        => $home . '#logo',
			'url'        => $logo,
			'contentUrl' => $logo,
			'caption'    => ATE_ORG_NAME,
		) : null;

		/* Walk the graph: enrich any existing Organization + every Person node. */
		$found_org = false;
		foreach ( $graph as &$node ) {
			if ( empty( $node['@type'] ) ) {
				continue;
			}
			$types = (array) $node['@type'];

			if ( in_array( 'Organization', $types, true ) ) {
				$found_org = true;
				if ( empty( $node['logo'] ) && $logo_node ) {
					$node['logo']  = $logo_node;
					$node['image'] = array( '@id' => $home . '#logo' );
				}
				if ( empty( $node['publishingPrinciples'] ) ) {
					$node['publishingPrinciples'] = $home . 'how-we-choose/';
				}
			}

			if ( in_array( 'Person', $types, true ) ) {
				$same = isset( $node['sameAs'] ) ? (array) $node['sameAs'] : array();
				$same[] = ATE_AUTHOR_SAMEAS;
				$node['sameAs'] = array_values( array_unique( array_filter( $same ) ) );

				if ( empty( $node['worksFor'] ) ) {
					$node['worksFor'] = array( '@id' => $org_id );
				}
				if ( empty( $node['knowsAbout'] ) ) {
					$node['knowsAbout'] = array(
						'Headphones',
						'Microphones',
						'Audio equipment',
						'Noise-cancelling technology',
						'Bluetooth audio codecs',
					);
				}
			}
		}
		unset( $node );

		/* If Yoast did not output an Organization, add one and wire publisher refs. */
		if ( ! $found_org ) {
			$org = array(
				'@type'                => 'Organization',
				'@id'                  => $org_id,
				'name'                 => ATE_ORG_NAME,
				'url'                  => $home,
				'description'          => get_bloginfo( 'description' ),
				'publishingPrinciples' => $home . 'how-we-choose/',
				'sameAs'               => array( $home ),
			);
			if ( $logo_node ) {
				$org['logo']  = $logo_node;
				$org['image'] = array( '@id' => $home . '#logo' );
			}
			$graph[] = $org;

			foreach ( $graph as &$node ) {
				if ( empty( $node['@type'] ) ) {
					continue;
				}
				$types = (array) $node['@type'];
				if ( array_intersect( array( 'WebSite', 'WebPage', 'Article' ), $types ) && empty( $node['publisher'] ) ) {
					$node['publisher'] = array( '@id' => $org_id );
				}
			}
			unset( $node );
		}

		return $graph;
	},
	20,
	2
);
