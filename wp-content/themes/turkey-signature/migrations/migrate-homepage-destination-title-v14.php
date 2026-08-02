<?php
/**
 * Rename and refine the editable destination heading.
 *
 * Only the heading inside the existing route block is changed; all other
 * homepage content remains byte-for-byte unchanged.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_destination_title_v14';
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

$v13_state = get_option( 'turkey_signature_homepage_destination_controls_v13' );

if ( ! is_array( $v13_state ) || (int) ( $v13_state['page_id'] ?? 0 ) !== $page_id ) {
	throw new RuntimeException( 'The destination controls migration v13 must exist before migration v14.' );
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

$old_heading = 'Одна страна.<br><em>Несколько состояний.</em>';
$new_heading = 'Все туры';

$current_content    = $home->post_content;
$current_hash       = hash( 'sha256', $current_content );
$current_block      = $find_anchor_block( parse_blocks( $current_content ), 'route' );
$current_serialized = is_array( $current_block ) ? serialize_block( $current_block ) : '';

if (
	'' === $current_serialized ||
	false === strpos( $current_serialized, 'destination-topline' ) ||
	false === strpos( $current_serialized, 'destination-heading' ) ||
	false === strpos( $current_serialized, 'destination-progress' ) ||
	1 !== substr_count( $current_serialized, $old_heading ) ||
	false !== strpos( $current_serialized, $new_heading ) ||
	4 !== substr_count( $current_serialized, 'class="wp-block-group destination-card"' )
) {
	throw new RuntimeException( 'The current route block no longer matches the expected v13 heading structure.' );
}

$heading_replacements = 0;
$target_serialized    = str_replace( $old_heading, $new_heading, $current_serialized, $heading_replacements );

if (
	1 !== $heading_replacements ||
	false !== strpos( $target_serialized, $old_heading ) ||
	1 !== substr_count( $target_serialized, $new_heading )
) {
	throw new RuntimeException( 'The destination heading replacement was not isolated safely.' );
}

if ( 1 !== substr_count( $current_content, $current_serialized ) ) {
	throw new RuntimeException( 'The current route block could not be isolated safely.' );
}

$route_replacements = 0;
$page_content       = str_replace( $current_serialized, $target_serialized, $current_content, $route_replacements );

if ( 1 !== $route_replacements ) {
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
	throw new RuntimeException( 'The homepage could not be read after migration v14.' );
}

$persisted_content = $persisted_home->post_content;
$persisted_block   = $find_anchor_block( parse_blocks( $persisted_content ), 'route' );
$persisted_markup  = is_array( $persisted_block ) ? serialize_block( $persisted_block ) : '';

if (
	false === strpos( $persisted_markup, 'destination-heading' ) ||
	false !== strpos( $persisted_markup, $old_heading ) ||
	1 !== substr_count( $persisted_markup, $new_heading )
) {
	throw new RuntimeException( 'The v14 destination heading was not persisted correctly.' );
}

update_option(
	$migration_key,
	array(
		'version'          => 14,
		'page_id'          => $page_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

clean_post_cache( $page_id );
