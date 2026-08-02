<?php
/**
 * Leave only the tour title and details button on each carousel card.
 *
 * The migration replaces only the editable route block after verifying the
 * exact v16 content hash and saves a WordPress revision before the update.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_tour_card_row_v17';
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

$v16_state = get_option( 'turkey_signature_homepage_real_tours_v16' );

if (
	! is_array( $v16_state ) ||
	(int) ( $v16_state['page_id'] ?? 0 ) !== $page_id ||
	empty( $v16_state['content_sha256'] )
) {
	throw new RuntimeException( 'The real tours migration v16 must exist before migration v17.' );
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

$find_class_blocks = static function ( array $blocks, string $class_name ) use ( &$find_class_blocks ) {
	$found = array();

	foreach ( $blocks as $block ) {
		$classes = preg_split( '/\s+/', trim( (string) ( $block['attrs']['className'] ?? '' ) ) );

		if ( in_array( $class_name, $classes, true ) ) {
			$found[] = $block;
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$found = array_merge( $found, $find_class_blocks( $block['innerBlocks'], $class_name ) );
		}
	}

	return $found;
};

$current_content    = $home->post_content;
$current_hash       = hash( 'sha256', $current_content );
$current_block      = $find_anchor_block( parse_blocks( $current_content ), 'route' );
$current_serialized = is_array( $current_block ) ? serialize_block( $current_block ) : '';

if ( ! hash_equals( (string) $v16_state['content_sha256'], $current_hash ) ) {
	throw new RuntimeException( 'The homepage changed after migration v16; migration v17 will not overwrite it.' );
}

if (
	'' === $current_serialized ||
	5 !== substr_count( $current_serialized, 'class="wp-block-group destination-card"' ) ||
	5 !== substr_count( $current_serialized, 'class="wp-block-buttons tour-details-trigger"' ) ||
	5 !== substr_count( $current_serialized, 'class="wp-block-group tour-details"' ) ||
	false === strpos( $current_serialized, '01 · Восток Турции' ) ||
	false === strpos( $current_serialized, '05 · Стамбул' )
) {
	throw new RuntimeException( 'The current route block no longer matches the expected v16 structure.' );
}

$pattern_path = get_theme_file_path( 'patterns/turkey-landing.php' );

if ( ! is_readable( $pattern_path ) ) {
	throw new RuntimeException( 'The Turkey landing pattern could not be read.' );
}

ob_start();
include $pattern_path;
$landing_content   = trim( (string) ob_get_clean() );
$target_block      = $find_anchor_block( parse_blocks( $landing_content ), 'route' );
$target_serialized = is_array( $target_block ) ? serialize_block( $target_block ) : '';
$card_copy_blocks  = is_array( $target_block ) ? $find_class_blocks( array( $target_block ), 'destination-card-copy' ) : array();

if (
	'' === $target_serialized ||
	5 !== substr_count( $target_serialized, 'class="wp-block-group destination-card tour-card-' ) ||
	5 !== substr_count( $target_serialized, 'class="wp-block-buttons tour-details-trigger"' ) ||
	5 !== substr_count( $target_serialized, 'class="wp-block-group tour-details"' ) ||
	5 !== count( $card_copy_blocks ) ||
	false !== strpos( $target_serialized, '01 · Восток Турции' ) ||
	false !== strpos( $target_serialized, '02 · Черноморье' ) ||
	false !== strpos( $target_serialized, '03 · Стамбул' ) ||
	false !== strpos( $target_serialized, '04 · Для всей семьи' ) ||
	false !== strpos( $target_serialized, '05 · Стамбул' )
) {
	throw new RuntimeException( 'The generated v17 tour card row is incomplete.' );
}

foreach ( $card_copy_blocks as $card_copy_block ) {
	if (
		1 !== count( $card_copy_block['innerBlocks'] ?? array() ) ||
		'core/heading' !== ( $card_copy_block['innerBlocks'][0]['blockName'] ?? '' )
	) {
		throw new RuntimeException( 'A v17 card contains content other than its title.' );
	}
}

if ( false !== strpos( $target_serialized, '"lock"' ) || false !== strpos( $target_serialized, '"templateLock"' ) ) {
	throw new RuntimeException( 'The generated v17 block unexpectedly contains editing locks.' );
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
	throw new RuntimeException( 'The homepage could not be read after migration v17.' );
}

$persisted_content = $persisted_home->post_content;
$persisted_block   = $find_anchor_block( parse_blocks( $persisted_content ), 'route' );
$persisted_markup  = is_array( $persisted_block ) ? serialize_block( $persisted_block ) : '';
$persisted_copies  = is_array( $persisted_block ) ? $find_class_blocks( array( $persisted_block ), 'destination-card-copy' ) : array();

if (
	5 !== substr_count( $persisted_markup, 'class="wp-block-group destination-card tour-card-' ) ||
	5 !== substr_count( $persisted_markup, 'class="wp-block-buttons tour-details-trigger"' ) ||
	5 !== substr_count( $persisted_markup, 'class="wp-block-group tour-details"' ) ||
	5 !== count( $persisted_copies ) ||
	false !== strpos( $persisted_markup, '01 · Восток Турции' ) ||
	false !== strpos( $persisted_markup, '05 · Стамбул' ) ||
	false !== strpos( $persisted_markup, '"lock"' ) ||
	false !== strpos( $persisted_markup, '"templateLock"' )
) {
	throw new RuntimeException( 'The v17 tour card row was not persisted correctly.' );
}

foreach ( $persisted_copies as $persisted_copy ) {
	if (
		1 !== count( $persisted_copy['innerBlocks'] ?? array() ) ||
		'core/heading' !== ( $persisted_copy['innerBlocks'][0]['blockName'] ?? '' )
	) {
		throw new RuntimeException( 'A persisted v17 card contains content other than its title.' );
	}
}

update_option(
	$migration_key,
	array(
		'version'          => 17,
		'page_id'          => $page_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

clean_post_cache( $page_id );
