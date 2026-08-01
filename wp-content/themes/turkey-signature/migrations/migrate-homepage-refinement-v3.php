<?php
/**
 * Apply the refined philosophy layout and interaction-ready homepage once.
 *
 * The migration only proceeds when the current page still matches the
 * previously recorded v2 content hash, so later manual edits are preserved.
 *
 * Run with:
 * wp eval-file wp-content/themes/turkey-signature/migrations/migrate-homepage-refinement-v3.php
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_refinement_v3';
$migration_state = get_option( $migration_key );

if ( is_array( $migration_state ) && ! empty( $migration_state['page_id'] ) ) {
	$existing_home = get_post( (int) $migration_state['page_id'] );

	if ( $existing_home instanceof WP_Post && 'page' === $existing_home->post_type ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( sprintf( 'Homepage refinement already applied to page %d.', $existing_home->ID ) );
		}
		return;
	}
}

$page_id = (int) get_option( 'page_on_front' );
$home    = $page_id ? get_post( $page_id ) : null;

if ( ! $home instanceof WP_Post || 'page' !== $home->post_type ) {
	throw new RuntimeException( 'The published WordPress front page could not be found.' );
}

$v2_state     = get_option( 'turkey_signature_homepage_redesign_v2' );
$current_hash = hash( 'sha256', $home->post_content );
$v2_hash      = is_array( $v2_state ) && ! empty( $v2_state['content_sha256'] ) ? (string) $v2_state['content_sha256'] : '';

if ( '' === $v2_hash || ! hash_equals( $v2_hash, $current_hash ) ) {
	throw new RuntimeException( 'Homepage content has changed since redesign v2; refinement v3 refused to overwrite manual edits.' );
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
	throw new RuntimeException( 'The refined homepage block content is incomplete.' );
}

wp_save_post_revision( $page_id );

$result = wp_update_post(
	array(
		'ID'           => $page_id,
		'post_content' => $page_content,
	),
	true
);

if ( is_wp_error( $result ) ) {
	throw new RuntimeException( $result->get_error_message() );
}

update_option(
	$migration_key,
	array(
		'version'          => 3,
		'page_id'          => $page_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $page_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

clean_post_cache( $page_id );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( sprintf( 'Applied the homepage refinement to page %d.', $page_id ) );
}
