<?php
/**
 * Add the editable team section to the published homepage once.
 *
 * The migration only proceeds while the published page still matches the
 * recorded simplification v5 hash, so later manual edits are never overwritten.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_team_v6';
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

$v5_state     = get_option( 'turkey_signature_homepage_simplification_v5' );
$current_hash = hash( 'sha256', $home->post_content );
$v5_hash      = is_array( $v5_state ) && ! empty( $v5_state['content_sha256'] ) ? (string) $v5_state['content_sha256'] : '';

if ( '' === $v5_hash || ! hash_equals( $v5_hash, $current_hash ) ) {
	throw new RuntimeException( 'Homepage content has changed since simplification v5; team migration v6 refused to overwrite manual edits.' );
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

if ( count( parse_blocks( $page_content ) ) < 3 ) {
	throw new RuntimeException( 'The team homepage block content is incomplete.' );
}

if ( false === strpos( $page_content, 'id="team"' ) || 3 !== substr_count( $page_content, 'class="wp-block-group team-card"' ) ) {
	throw new RuntimeException( 'The generated homepage does not contain the expected editable team section.' );
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
	throw new RuntimeException( 'The homepage could not be read after adding the team section.' );
}

$persisted_content = $persisted_home->post_content;

if ( false === strpos( $persisted_content, 'id="team"' ) || 3 !== substr_count( $persisted_content, 'class="wp-block-group team-card"' ) ) {
	throw new RuntimeException( 'The team section was not persisted correctly.' );
}

update_option(
	$migration_key,
	array(
		'version'          => 6,
		'page_id'          => $page_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

clean_post_cache( $page_id );
