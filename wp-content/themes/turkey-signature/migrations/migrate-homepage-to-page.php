<?php
/**
 * Move the file-based landing page into an editable WordPress page.
 *
 * Run with:
 * wp eval-file wp-content/themes/turkey-signature/migrations/migrate-homepage-to-page.php
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_page_migration_v1';
$migration_state = get_option( $migration_key );

if ( is_array( $migration_state ) && ! empty( $migration_state['page_id'] ) ) {
	$existing_home = get_post( (int) $migration_state['page_id'] );

	if ( $existing_home instanceof WP_Post && 'page' === $existing_home->post_type ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( sprintf( 'Homepage migration already completed for page %d.', $existing_home->ID ) );
		}
		return;
	}
}

$pattern_path = get_theme_file_path( 'patterns/turkey-landing.php' );

if ( ! is_readable( $pattern_path ) ) {
	throw new RuntimeException( 'The Turkey landing pattern could not be read.' );
}

ob_start();
include $pattern_path;
$landing_content = trim( (string) ob_get_clean() );

$header_block = '<!-- wp:template-part {"slug":"header","theme":"turkey-signature","tagName":"header","className":"site-header"} /-->';
$footer_block = '<!-- wp:template-part {"slug":"footer","theme":"turkey-signature","tagName":"footer","className":"site-footer"} /-->';
$page_content = $header_block . "\n" . $landing_content . "\n" . $footer_block;
$parsed_blocks = parse_blocks( $page_content );

if ( count( $parsed_blocks ) < 3 ) {
	throw new RuntimeException( 'The generated homepage block content is incomplete.' );
}

$home_page = get_page_by_path( 'home', OBJECT, 'page' );

if ( $home_page instanceof WP_Post ) {
	if ( '' !== trim( $home_page->post_content ) ) {
		throw new RuntimeException( 'A non-empty page with the slug "home" already exists. No content was overwritten.' );
	}

	$page_id = wp_update_post(
		array(
			'ID'           => $home_page->ID,
			'post_title'   => 'Главная',
			'post_status'  => 'publish',
			'post_content' => $page_content,
		),
		true
	);
} else {
	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_title'   => 'Главная',
			'post_name'    => 'home',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_content' => $page_content,
		),
		true
	);
}

if ( is_wp_error( $page_id ) ) {
	throw new RuntimeException( $page_id->get_error_message() );
}

$page_id = (int) $page_id;

update_post_meta( $page_id, '_wp_page_template', 'default' );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_id );
update_option( 'page_for_posts', 0 );

$deleted_pages = array();

foreach ( array( 'sample-page', 'privacy-policy' ) as $obsolete_slug ) {
	$obsolete_page = get_page_by_path( $obsolete_slug, OBJECT, 'page' );

	if ( ! $obsolete_page instanceof WP_Post || $obsolete_page->ID === $page_id ) {
		continue;
	}

	if ( (int) get_option( 'wp_page_for_privacy_policy' ) === $obsolete_page->ID ) {
		update_option( 'wp_page_for_privacy_policy', 0 );
	}

	if ( wp_delete_post( $obsolete_page->ID, true ) ) {
		$deleted_pages[] = $obsolete_page->ID;
	}
}

update_option(
	$migration_key,
	array(
		'version'           => 1,
		'page_id'           => $page_id,
		'deleted_page_ids'  => $deleted_pages,
		'completed_at_gmt'  => gmdate( 'c' ),
	),
	false
);

flush_rewrite_rules( false );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success(
		sprintf(
			'Created editable homepage %d and removed %d obsolete page(s).',
			$page_id,
			count( $deleted_pages )
		)
	);
}
