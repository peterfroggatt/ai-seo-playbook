<?php
/**
 * Phillip Strang - inject "authors like X" funnel blocks into the 10 posts.
 *
 * WHY: those posts store their whole article as one HTML block, so you can't
 * drop a separate block inside them. This filter inserts the right callout
 * automatically, keyed by post ID - no manual HTML editing.
 *
 * INSTALL: Code Snippets -> Add New -> paste everything below the <?php line
 * -> Run everywhere -> Save & Activate -> LiteSpeed -> Purge All.
 * To tweak wording later, edit the matching entry below and re-save.
 */

add_filter( 'the_content', 'ps_author_funnel_blocks', 9 );

function ps_author_funnel_blocks( $content ) {

	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$blocks = array(
		13577 => '<div style="border-left:4px solid #c8a951;background:#faf9f7;padding:18px 22px;margin:28px 0;border-radius:4px;"><p style="margin:0 0 6px;font-weight:700;color:#1e3a5f;font-size:1.1em;">Love Rankin\'s Rebus? Meet DI Sarah Lynch</p><p style="margin:0;">If Rebus draws you to Scottish detectives working murder amid the lochs and Highlands, Phillip Strang\'s DI Sarah Lynch series is your next binge. <a href="https://phillipstrang.com/di-sarah-lynch/" style="color:#b8860b;font-weight:600;">Explore the DI Sarah Lynch series &rarr;</a></p></div>',
		11240 => '<div style="border-left:4px solid #c8a951;background:#faf9f7;padding:18px 22px;margin:28px 0;border-radius:4px;"><p style="margin:0 0 6px;font-weight:700;color:#1e3a5f;font-size:1.1em;">Enjoy MacBride\'s Scottish grit? Try DI Sarah Lynch</p><p style="margin:0;">For the same dark, procedural edge set against the Scottish landscape, Phillip Strang\'s DI Sarah Lynch delivers murder in the Highlands. <a href="https://phillipstrang.com/di-sarah-lynch/" style="color:#b8860b;font-weight:600;">Explore the DI Sarah Lynch series &rarr;</a></p></div>',
		13817 => '<div style="border-left:4px solid #c8a951;background:#faf9f7;padding:18px 22px;margin:28px 0;border-radius:4px;"><p style="margin:0 0 6px;font-weight:700;color:#1e3a5f;font-size:1.1em;">If you love Ann Cleeves\' atmosphere &mdash; try DI Sarah Lynch</p><p style="margin:0;">Cleeves fans who love a strong detective in a wild, remote setting will feel at home with Phillip Strang\'s DI Sarah Lynch, set across the Scottish Highlands. <a href="https://phillipstrang.com/di-sarah-lynch/" style="color:#b8860b;font-weight:600;">Explore the DI Sarah Lynch series &rarr;</a></p></div>',
		14059 => '<div style="border-left:4px solid #c8a951;background:#faf9f7;padding:18px 22px;margin:28px 0;border-radius:4px;"><p style="margin:0 0 6px;font-weight:700;color:#1e3a5f;font-size:1.1em;">Hooked on Nordic noir? DI Sarah Lynch is next</p><p style="margin:0;">If Jonasson\'s isolated, moody mysteries are your thing, Phillip Strang\'s DI Sarah Lynch brings that same remote menace to the Scottish Highlands. <a href="https://phillipstrang.com/di-sarah-lynch/" style="color:#b8860b;font-weight:600;">Explore the DI Sarah Lynch series &rarr;</a></p></div>',
		11243 => '<div style="border-left:4px solid #c8a951;background:#faf9f7;padding:18px 22px;margin:28px 0;border-radius:4px;"><p style="margin:0 0 6px;font-weight:700;color:#1e3a5f;font-size:1.1em;">Craven\'s Cumbria calling? Try DI Tobias Stone</p><p style="margin:0;">Washington Poe fans will love that Phillip Strang\'s DI Tobias Stone works murder among the same lakes and fells of the Lake District. <a href="https://phillipstrang.com/di-tobias-stone/" style="color:#b8860b;font-weight:600;">Explore the DI Tobias Stone series &rarr;</a></p></div>',
		13475 => '<div style="border-left:4px solid #c8a951;background:#faf9f7;padding:18px 22px;margin:28px 0;border-radius:4px;"><p style="margin:0 0 6px;font-weight:700;color:#1e3a5f;font-size:1.1em;">Like McKinty\'s pace? Meet DCI Isaac Cook</p><p style="margin:0;">For fast, gritty urban police work, Phillip Strang\'s DCI Isaac Cook hunts murderers across London from Challis Street Homicide. <a href="https://phillipstrang.com/dci-isaac-cook-series/" style="color:#b8860b;font-weight:600;">Explore the DCI Isaac Cook series &rarr;</a></p></div>',
		13897 => '<div style="border-left:4px solid #c8a951;background:#faf9f7;padding:18px 22px;margin:28px 0;border-radius:4px;"><p style="margin:0 0 6px;font-weight:700;color:#1e3a5f;font-size:1.1em;">Harris fan? Try the Alex Harlan FBI series</p><p style="margin:0;">If you\'re drawn to FBI profilers hunting dangerous minds, Phillip Strang\'s Alex Harlan uncovers a global conspiracy rooted in the Appalachians. <a href="https://phillipstrang.com/alex-harlan-fbi-series/" style="color:#b8860b;font-weight:600;">Explore the Alex Harlan FBI series &rarr;</a></p></div>',
		11657 => '<div style="border-left:4px solid #c8a951;background:#faf9f7;padding:18px 22px;margin:28px 0;border-radius:4px;"><p style="margin:0 0 6px;font-weight:700;color:#1e3a5f;font-size:1.1em;">le Carre\'s world? Meet Steve Case</p><p style="margin:0;">For slow-burn international intrigue spanning war zones and global stakes, Phillip Strang\'s Steve Case thrillers deliver. <a href="https://phillipstrang.com/steve-case/" style="color:#b8860b;font-weight:600;">Explore the Steve Case series &rarr;</a></p></div>',
		14198 => '<div style="border-left:4px solid #c8a951;background:#faf9f7;padding:18px 22px;margin:28px 0;border-radius:4px;"><p style="margin:0 0 6px;font-weight:700;color:#1e3a5f;font-size:1.1em;">Herron\'s intrigue? Try Steve Case</p><p style="margin:0;">Readers who like their spies flawed and their stakes global will find plenty to love in Phillip Strang\'s Steve Case international thrillers. <a href="https://phillipstrang.com/steve-case/" style="color:#b8860b;font-weight:600;">Explore the Steve Case series &rarr;</a></p></div>',
		14031 => '<div style="border-left:4px solid #c8a951;background:#faf9f7;padding:18px 22px;margin:28px 0;border-radius:4px;"><p style="margin:0 0 6px;font-weight:700;color:#1e3a5f;font-size:1.1em;">Izzo\'s European noir? Meet Reid Harper</p><p style="margin:0;">For crime that moves through the great cities of Europe, Phillip Strang\'s Reid Harper series carries that same continental edge. <a href="https://phillipstrang.com/reid-harper/" style="color:#b8860b;font-weight:600;">Explore the Reid Harper series &rarr;</a></p></div>',
	);

	$id = get_the_ID();
	if ( empty( $blocks[ $id ] ) ) {
		return $content;
	}
	$block = $blocks[ $id ];

	// Insert right after the first paragraph (the intro), before the list.
	$pos = stripos( $content, '</p>' );
	if ( false !== $pos ) {
		$pos += 4;
		return substr( $content, 0, $pos ) . $block . substr( $content, $pos );
	}
	return $block . $content;
}
