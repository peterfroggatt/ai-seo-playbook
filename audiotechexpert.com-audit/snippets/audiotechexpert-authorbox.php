<?php
/**
 * Plugin Name: Audio Tech Expert — Author Box
 * Description: Appends a visible "About the author" box (real photo + honest bio + link) to the bottom of every single post, so the author photo readers never saw in the byline is now on-page. Complements the schema plugin (which only feeds crawlers). Fixes the E-E-A-T "no visible author" gap.
 * Version:     1.0.0
 * Author:      SEO audit remediation
 *
 * INSTALL: wp-content/mu-plugins/ (auto-activates). Keep exactly ONE copy (delete any "(1)" duplicate).
 * Reversible: delete this file. Guarded against double-load so a duplicate can't fatal the site.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ATE_AUTHORBOX_LOADED' ) ) {
	define( 'ATE_AUTHORBOX_LOADED', true );

	/* ---- Config ---- */
	if ( ! defined( 'ATE_AB_AUTHOR_ID' ) ) {
		define( 'ATE_AB_AUTHOR_ID', 1 );
	}
	// Photo: prefer the shared constant from the schema plugin; else fall back to the filtered avatar.
	if ( ! defined( 'ATE_AB_PHOTO' ) ) {
		define( 'ATE_AB_PHOTO', defined( 'ATE_AUTHOR_PHOTO' ) ? ATE_AUTHOR_PHOTO : '' );
	}
	// Author's "other work" link (crime/thriller fiction), shown as a subtle secondary link.
	if ( ! defined( 'ATE_AB_EXTLINK' ) ) {
		define( 'ATE_AB_EXTLINK', 'https://phillipstrang.com' );
	}

	add_filter(
		'the_content',
		function ( $content ) {
			// Only real single blog posts, in the main loop — never feeds, pages, archives, or shortcode renders.
			if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() || is_admin() ) {
				return $content;
			}
			if ( strpos( $content, 'ate-authorbox' ) !== false ) {
				return $content; // already present (safety)
			}

			$author_id = (int) get_post_field( 'post_author', get_the_ID() );
			if ( ! $author_id ) {
				$author_id = (int) ATE_AB_AUTHOR_ID;
			}

			$name = get_the_author_meta( 'display_name', $author_id );
			$bio  = trim( (string) get_the_author_meta( 'description', $author_id ) );
			if ( '' === $bio ) {
				$bio = 'Phillip Strang is the founder and editor of AudioTechExpert. A lifelong audio enthusiast, he has spent years buying, using and living with headphones, microphones and audio gear across every price bracket — and built AudioTechExpert to give buyers the honest, jargon-free guidance he wished he’d had.';
			}

			$photo = ATE_AB_PHOTO;
			if ( ! $photo ) {
				$photo = get_avatar_url( $author_id, array( 'size' => 200 ) );
			}

			$archive = get_author_posts_url( $author_id );

			ob_start();
			?>
<div class="ate-authorbox">
	<style>
		.ate-authorbox{--ab-navy:#1a1a2e;--ab-accent:#e94560;font-family:'Outfit',-apple-system,Segoe UI,sans-serif;display:flex;gap:20px;align-items:flex-start;margin:40px 0 12px;padding:24px;background:#faf9fc;border:1px solid #ececf2;border-left:4px solid var(--ab-accent);border-radius:14px;color:var(--ab-navy);}
		.ate-authorbox__photo{flex:0 0 auto;}
		.ate-authorbox__photo img{width:96px;height:96px;border-radius:50%;object-fit:cover;display:block;box-shadow:0 2px 12px rgba(26,26,46,.15);}
		.ate-authorbox__body{flex:1 1 auto;min-width:0;}
		.ate-authorbox__eyebrow{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--ab-accent);margin:0 0 4px;}
		.ate-authorbox__name{font-size:19px;font-weight:700;margin:0 0 8px;line-height:1.2;}
		.ate-authorbox__name a{color:var(--ab-navy);text-decoration:none;}
		.ate-authorbox__name a:hover{text-decoration:underline;}
		.ate-authorbox__bio{font-size:15px;line-height:1.6;margin:0;color:#333;}
		.ate-authorbox__more{display:inline-block;margin-top:10px;font-size:13.5px;font-weight:600;color:var(--ab-accent);text-decoration:none;}
		.ate-authorbox__more:hover{text-decoration:underline;}
		@media(max-width:560px){.ate-authorbox{flex-direction:column;gap:14px;padding:20px;}.ate-authorbox__photo img{width:80px;height:80px;}}
	</style>
	<?php if ( $photo ) : ?>
	<div class="ate-authorbox__photo">
		<img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $name ); ?>" width="96" height="96" loading="lazy" />
	</div>
	<?php endif; ?>
	<div class="ate-authorbox__body">
		<p class="ate-authorbox__eyebrow">About the author</p>
		<p class="ate-authorbox__name"><a href="<?php echo esc_url( $archive ); ?>" rel="author"><?php echo esc_html( $name ); ?></a></p>
		<p class="ate-authorbox__bio"><?php echo esc_html( $bio ); ?></p>
		<?php if ( ATE_AB_EXTLINK ) : ?>
		<a class="ate-authorbox__more" href="<?php echo esc_url( ATE_AB_EXTLINK ); ?>" rel="noopener nofollow" target="_blank">More about Phillip &rarr;</a>
		<?php endif; ?>
	</div>
</div>
			<?php
			return $content . ob_get_clean();
		},
		20
	);
}
