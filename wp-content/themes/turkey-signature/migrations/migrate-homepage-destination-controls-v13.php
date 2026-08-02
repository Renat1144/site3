<?php
/**
 * Refine the destination toolbar and remove its visible counter.
 *
 * Only the existing editable route block is replaced; all other homepage
 * content remains byte-for-byte unchanged.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_destination_controls_v13';
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

$v12_state = get_option( 'turkey_signature_homepage_destination_autoplay_v12' );

if ( ! is_array( $v12_state ) || (int) ( $v12_state['page_id'] ?? 0 ) !== $page_id ) {
	throw new RuntimeException( 'The destination autoplay migration v12 must exist before migration v13.' );
}

$find_anchor_block = static function ( array $blocks, string $anchor ) use ( &$find_anchor_block ) {
	foreach ( $blocks as $block ) {
		if ( $anchor === ( $block['attrs']['anchor'] ?? '' ) ) {
			return $block;
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$found = $find_anchor_block( $block['innerBlocks'], $anchor );

			if ( is_array( $found ) ) {
				return $found;
			}
		}
	}

	return null;
};

$current_content    = $home->post_content;
$current_hash       = hash( 'sha256', $current_content );
$current_block      = $find_anchor_block( parse_blocks( $current_content ), 'route' );
$current_serialized = is_array( $current_block ) ? serialize_block( $current_block ) : '';

if (
	'' === $current_serialized ||
	false === strpos( $current_serialized, 'destination-count' ) ||
	false !== strpos( $current_serialized, 'destination-topline' ) ||
	false === strpos( $current_serialized, 'destination-progress' ) ||
	false === strpos( $current_serialized, 'destination-slider' ) ||
	4 !== substr_count( $current_serialized, 'class="wp-block-group destination-card"' )
) {
	throw new RuntimeException( 'The current route block no longer matches the expected v12 structure.' );
}

$pattern_path = get_theme_file_path( 'patterns/turkey-landing.php' );

if ( ! is_readable( $pattern_path ) ) {
	throw new RuntimeException( 'The Turkey landing pattern could not be read.' );
}

ob_start();
include $pattern_path;
$landing_content    = trim( (string) ob_get_clean() );
$target_block       = $find_anchor_block( parse_blocks( $landing_content ), 'route' );
$target_serialized  = is_array( $target_block ) ? serialize_block( $target_block ) : '';
$target_controls    = strpos( $target_serialized, 'destination-progress' );
$target_viewport    = strpos( $target_serialized, 'destination-viewport' );

if (
	'' === $target_serialized ||
	false === strpos( $target_serialized, 'destination-topline' ) ||
	false !== strpos( $target_serialized, 'destination-count' ) ||
	false === strpos( $target_serialized, 'destination-arrow-prev' ) ||
	false === strpos( $target_serialized, 'destination-slider' ) ||
	false === strpos( $target_serialized, 'destination-arrow-next' ) ||
	false === $target_controls ||
	false === $target_viewport ||
	$target_controls > $target_viewport ||
	4 !== substr_count( $target_serialized, 'class="wp-block-group destination-card"' )
) {
	throw new RuntimeException( 'The generated v13 destination toolbar is incomplete.' );
}

if ( 1 !== substr_count( $current_content, $current_serialized ) ) {
	throw new RuntimeException( 'The current route block could not be isolated safely.' );
}

$replacement_count = 0;
$page_content       = str_replace( $current_serialized, $target_serialized, $current_content, $replacement_count );

if ( 1 !== $replacement_count ) {
	throw new RuntimeException( 'The route block replacement count was not exactly one.' );
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
	throw new RuntimeException( 'The homepage could not be read after migration v13.' );
}

$persisted_content = $persisted_home->post_content;
$persisted_block   = $find_anchor_block( parse_blocks( $persisted_content ), 'route' );
$persisted_markup  = is_array( $persisted_block ) ? serialize_block( $persisted_block ) : '';

if (
	false === strpos( $persisted_markup, 'destination-topline' ) ||
	false !== strpos( $persisted_markup, 'destination-count' ) ||
	false === strpos( $persisted_markup, 'destination-slider' )
) {
	throw new RuntimeException( 'The v13 destination toolbar was not persisted correctly.' );
}

update_option(
	$migration_key,
	array(
		'version'          => 13,
		'page_id'          => $page_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

clean_post_cache( $page_id );
