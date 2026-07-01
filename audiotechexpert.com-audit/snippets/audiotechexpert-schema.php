<?php
/**
 * Plugin Name: Audio Tech Expert — Schema Enhancements
 * Description: Adds Organization schema, enriches the author Person node (incl. real author photo), and uses the uploaded author photo as the avatar everywhere (bylines + schema). Fixes SEO-audit items C-3, W-3, W-4 and the missing author image.
 * Version:     1.2.0
 * Author:      SEO audit remediation
 *
 * INSTALL: wp-content/mu-plugins/ (auto-activates). Keep exactly ONE copy (delete any "(1)" duplicate).
 * Requires Yoast SEO. Guarded against double-load so a duplicate can't fatal the site.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ATE_SCHEMA_LOADED' ) ) {
	define( 'ATE_SCHEMA_LOADED', true );

	/* ---- Config ---- */
	if ( ! defined( 'ATE_ORG_NAME' ) ) {
		define( 'ATE_ORG_NAME', 'Audio Tech Expert' );
	}
	if ( ! defined( 'ATE_LOGO_URL' ) ) {
		define( 'ATE_LOGO_URL', '' ); // empty = auto-detect theme logo
	}
	if ( ! defined( 'ATE_AUTHOR_SAMEAS' ) ) {
		define( 'ATE_AUTHOR_SAMEAS', 'https://phillipstrang.com' );
	}
	// The author account (user ID) and the uploaded author photo.
	if ( ! defined( 'ATE_AUTHOR_ID' ) ) {
		define( 'ATE_AUTHOR_ID', 1 );
	}
	if ( ! defined( 'ATE_AUTHOR_PHOTO' ) ) {
		define( 'ATE_AUTHOR_PHOTO', 'https://audiotechexpert.com/wp-content/uploads/2026/07/f-phillip-image-2500-2500-crop-1.jpg' );
	}

	/* ---- Use the uploaded author photo as the avatar (bylines + Yoast Person image) ---- */
	add_filter(
		'get_avatar_url',
		function ( $url, $id_or_email, $args = array() ) {
			if ( ! ATE_AUTHOR_PHOTO ) {
				return $url;
			}
			$uid = 0;
			if ( is_numeric( $id_or_email ) ) {
				$uid = (int) $id_or_email;
			} elseif ( is_object( $id_or_email ) ) {
				$uid = (int) ( isset( $id_or_email->user_id ) ? $id_or_email->user_id : 0 );
			} elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
				$u   = get_user_by( 'email', $id_or_email );
				$uid = $u ? (int) $u->ID : 0;
			}
			return ( $uid === (int) ATE_AUTHOR_ID ) ? ATE_AUTHOR_PHOTO : $url;
		},
		20,
		3
	);

	add_filter(
		'wpseo_schema_graph',
		function ( $graph, $context = null ) {
			if ( ! is_array( $graph ) ) {
				return $graph;
			}

			$home   = trailingslashit( home_url() );
			$org_id = $home . '#organization';

			$logo = ATE_LOGO_URL;
			if ( ! $logo ) {
				$cl = get_theme_mod( 'custom_logo' );
				if ( $cl ) {
					$logo = wp_get_attachment_image_url( $cl, 'full' );
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
					$same           = isset( $node['sameAs'] ) ? (array) $node['sameAs'] : array();
					$same[]         = ATE_AUTHOR_SAMEAS;
					$node['sameAs'] = array_values( array_unique( array_filter( $same ) ) );
					if ( empty( $node['worksFor'] ) ) {
						$node['worksFor'] = array( '@id' => $org_id );
					}
					if ( empty( $node['knowsAbout'] ) ) {
						$node['knowsAbout'] = array( 'Headphones', 'Microphones', 'Audio equipment', 'Noise-cancelling technology', 'Bluetooth audio codecs' );
					}
					// Force the real author photo as the Person image.
					if ( ATE_AUTHOR_PHOTO ) {
						$node['image'] = array(
							'@type'      => 'ImageObject',
							'@id'        => $home . '#/schema/person/image/',
							'url'        => ATE_AUTHOR_PHOTO,
							'contentUrl' => ATE_AUTHOR_PHOTO,
							'caption'    => isset( $node['name'] ) ? $node['name'] : ATE_ORG_NAME,
						);
					}
				}
			}
			unset( $node );

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
}
