<?php
/**
 * Remove the decorative hero metadata and program image caption once.
 *
 * The migration only proceeds while the published page still matches the
 * recorded refinement v3 hash, so later manual edits are never overwritten.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_cleanup_v4';
$migration_state = get_option( $migration_key );

if ( is_array( $migration_state ) && ! empty( $migration_state['page_id'] ) ) {
	$existing_home = get_post( (int) $migration_state['page_id'] );

	if ( $existing_home instanceof WP_Post && 'page' === $existing_home->post_type ) {
		return;
	}
}

$page_id = (int) get_option( 'page_on_front' );
$home    = $page_id ? get_post( $page_id ) : null;

if ( ! $home instanceof WP_Post || 'page' !== $home->post_type ) {
	throw new RuntimeException( 'The published WordPress front page could not be found.' );
}

$v3_state     = get_option( 'turkey_signature_homepage_refinement_v3' );
$current_hash = hash( 'sha256', $home->post_content );
$v3_hash      = is_array( $v3_state ) && ! empty( $v3_state['content_sha256'] ) ? (string) $v3_state['content_sha256'] : '';

if ( '' === $v3_hash || ! hash_equals( $v3_hash, $current_hash ) ) {
	throw new RuntimeException( 'Homepage content has changed since refinement v3; cleanup v4 refused to overwrite manual edits.' );
}

$pattern_path = get_theme_file_path( 'patterns/turkey-landing.php' );

if ( ! is_readable( $pattern_path ) ) {
	throw new RuntimeException( 'The Turkey landing pattern could not be read.' );
}

ob_start();
include $pattern_path;
$landing_content = trim( (string) ob_get_clean() );

$header_block  = '<!-- wp:template-part {"slug":"header","theme":"turkey-signature","tagName":"header","className":"site-header"} /-->';
$footer_block  = '<!-- wp:template-part {"slug":"footer","theme":"turkey-signature","tagName":"footer","className":"site-footer"} /-->';
$page_content = $header_block . "\n" . $landing_content . "\n" . $footer_block;

if ( count( parse_blocks( $page_content ) ) < 3 ) {
	throw new RuntimeException( 'The cleaned homepage block content is incomplete.' );
}

if ( false !== strpos( $page_content, 'hero-meta' ) || false !== strpos( $page_content, 'program-visual-caption' ) ) {
	throw new RuntimeException( 'The removed decorative blocks are still present in the generated homepage.' );
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

clean_post_cache( $page_id );
$persisted_home = get_post( $page_id );

if ( ! $persisted_home instanceof WP_Post ) {
	throw new RuntimeException( 'The cleaned homepage could not be read after saving.' );
}

update_option(
	$migration_key,
	array(
		'version'          => 4,
		'page_id'          => $page_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted_home->post_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

clean_post_cache( $page_id );
