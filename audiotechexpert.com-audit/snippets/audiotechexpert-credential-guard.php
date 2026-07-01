<?php
/**
 * Plugin Name: Audio Tech Expert — Credential Guard
 * Description: Safety net for E-E-A-T integrity. Rewrites the fabricated "fifteen years of working…" author-experience claim to the approved honest voice on every post save/publish (REST, classic editor, or programmatic pipeline), so newly generated posts can't silently reintroduce it. Backstop only — best paired with fixing the content generator at source.
 * Version:     1.0.0
 * Author:      SEO audit remediation
 *
 * INSTALL: wp-content/mu-plugins/ (auto-activates). Keep exactly ONE copy (delete any "(1)" duplicate).
 * Reversible: delete this file. Guarded against double-load so a duplicate can't fatal the site.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ATE_CREDGUARD_LOADED' ) ) {
	define( 'ATE_CREDGUARD_LOADED', true );

	if ( ! defined( 'ATE_CREDGUARD_REPL' ) ) {
		define( 'ATE_CREDGUARD_REPL', 'After years of obsessively buying, using and comparing audio gear,' );
	}

	/**
	 * Rewrite the fabricated credential clause(s) in a content string.
	 * Mirrors the audited Batch 9 logic: only touches a sentence-leading
	 * "After/Over … fifteen years … ," clause (and the "Fifteen years of … " subject form),
	 * never a bare mid-sentence mention.
	 */
	function ate_credguard_clean( $content ) {
		if ( '' === $content || stripos( $content, 'fifteen years' ) === false ) {
			return $content;
		}

		// Pattern 1 — sentence-leading adverbial clause:
		//   "After [more than|the better part of] fifteen years …, <clause>"
		// Match up to the comma that closes the intro clause (the last comma before the
		// sentence terminator, when the main clause is comma-free). Capital opener only,
		// so mid-sentence lowercase "…give after fifteen years…" is left untouched.
		$content = preg_replace(
			'/\b(?:After|Over)\s+(?:more than\s+|the better part of\s+)?fifteen years\b[^.?!<]*?,(?=[^,.?!<]*[.?!<])/u',
			ATE_CREDGUARD_REPL,
			$content
		);

		// Pattern 2 — subject form at a clause start:
		//   "Fifteen years of <fabricated context> has/have <verb>…"
		//   -> "Years of obsessively buying, using and comparing audio gear have <verb>…"
		$content = preg_replace(
			'/\bFifteen years of\b[^.?!<]*?\b(has|have)\b/u',
			'Years of obsessively buying, using and comparing audio gear have',
			$content
		);

		return $content;
	}

	add_filter(
		'wp_insert_post_data',
		function ( $data, $postarr ) {
			// Skip revisions, autosaves, auto-drafts, trashed items.
			if ( empty( $data['post_content'] ) ) {
				return $data;
			}
			if ( isset( $data['post_type'] ) && 'revision' === $data['post_type'] ) {
				return $data;
			}
			if ( isset( $data['post_status'] ) && in_array( $data['post_status'], array( 'inherit', 'auto-draft', 'trash' ), true ) ) {
				return $data;
			}

			// Content arrives slashed; unslash → clean → reslash so quotes/backslashes are preserved.
			$content = wp_unslash( $data['post_content'] );
			$cleaned = ate_credguard_clean( $content );
			if ( $cleaned !== $content ) {
				$data['post_content'] = wp_slash( $cleaned );
			}
			return $data;
		},
		20,
		2
	);
}
