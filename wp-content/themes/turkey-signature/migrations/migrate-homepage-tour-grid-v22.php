<?php
/**
 * Replace the tour carousel controls with a normal editable tour grid.
 *
 * Only the controls group inside the existing route block is removed. Tour
 * cards, descriptions, images and their order are preserved byte-for-byte.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_tour_grid_v22';
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

$v21_state = get_option( 'turkey_signature_homepage_additional_tours_v21' );

if (
	! is_array( $v21_state ) ||
	(int) ( $v21_state['page_id'] ?? 0 ) !== $page_id ||
	empty( $v21_state['content_sha256'] )
) {
	throw new RuntimeException( 'The additional tours migration v21 must exist before migration v22.' );
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

$inspect_route = static function ( $route ) use ( $find_class_blocks ) {
	$route_blocks = is_array( $route ) ? array( $route ) : array();

	return array(
		'cards'    => $find_class_blocks( $route_blocks, 'destination-card' ),
		'triggers' => $find_class_blocks( $route_blocks, 'tour-details-trigger' ),
		'details'  => $find_class_blocks( $route_blocks, 'tour-details' ),
		'programs' => $find_class_blocks( $route_blocks, 'tour-detail-program' ),
		'days'     => $find_class_blocks( $route_blocks, 'tour-detail-day' ),
		'prices'   => $find_class_blocks( $route_blocks, 'tour-detail-price' ),
		'controls' => $find_class_blocks( $route_blocks, 'destination-progress' ),
		'ranges'   => $find_class_blocks( $route_blocks, 'destination-range' ),
		'arrows'   => $find_class_blocks( $route_blocks, 'destination-arrow' ),
		'sliders'  => $find_class_blocks( $route_blocks, 'destination-slider' ),
	);
};

$validate_content = static function ( array $parts, string $serialized, bool $expect_controls ) {
	if (
		8 !== count( $parts['cards'] ) ||
		8 !== count( $parts['triggers'] ) ||
		8 !== count( $parts['details'] ) ||
		2 !== count( $parts['programs'] ) ||
		6 !== count( $parts['days'] ) ||
		1 !== count( $parts['prices'] )
	) {
		throw new RuntimeException( 'The route block tour content count is invalid.' );
	}

	$expected_controls = $expect_controls ? array( 1, 1, 2, 1 ) : array( 0, 0, 0, 0 );
	$actual_controls   = array(
		count( $parts['controls'] ),
		count( $parts['ranges'] ),
		count( $parts['arrows'] ),
		count( $parts['sliders'] ),
	);

	if ( $expected_controls !== $actual_controls ) {
		throw new RuntimeException( 'The route block carousel control count is invalid.' );
	}

	$required_fragments = array(
		'Месопотамия',
		'Карадеиз',
		'Восточная сказка',
		'Тур для детей',
		'Многогранность Стамбула',
		'Стамбул и Каппадокия: комфорт-тур',
		'Город-шкатулка для детей',
		'Слияние империй: Стамбул',
	);

	foreach ( $required_fragments as $required_fragment ) {
		if ( false === strpos( $serialized, $required_fragment ) ) {
			throw new RuntimeException( 'The route block is missing a required tour.' );
		}
	}

	if ( false !== strpos( $serialized, '"lock"' ) || false !== strpos( $serialized, '"templateLock"' ) ) {
		throw new RuntimeException( 'The route block unexpectedly contains editing locks.' );
	}
};

$current_content    = $home->post_content;
$current_hash       = hash( 'sha256', $current_content );
$current_route      = $find_anchor_block( parse_blocks( $current_content ), 'route' );
$current_serialized = is_array( $current_route ) ? serialize_block( $current_route ) : '';
$current_parts      = $inspect_route( $current_route );

if ( ! hash_equals( (string) $v21_state['content_sha256'], $current_hash ) ) {
	throw new RuntimeException( 'The homepage changed after migration v21; migration v22 will not overwrite it.' );
}

if ( '' === $current_serialized ) {
	throw new RuntimeException( 'The current route block could not be found.' );
}

$validate_content( $current_parts, $current_serialized, true );

$controls_serialized = serialize_block( $current_parts['controls'][0] );

if ( 1 !== substr_count( $current_serialized, $controls_serialized ) ) {
	throw new RuntimeException( 'The carousel controls could not be isolated safely.' );
}

$target_serialized = str_replace( $controls_serialized, '', $current_serialized, $controls_replacement_count );
$target_route      = $find_anchor_block( parse_blocks( $target_serialized ), 'route' );
$target_parts      = $inspect_route( $target_route );

if ( 1 !== $controls_replacement_count ) {
	throw new RuntimeException( 'The carousel controls replacement count was not exactly one.' );
}

$validate_content( $target_parts, $target_serialized, false );

if ( 1 !== substr_count( $current_content, $current_serialized ) ) {
	throw new RuntimeException( 'The current route block could not be isolated safely.' );
}

$page_content = str_replace( $current_serialized, $target_serialized, $current_content, $route_replacement_count );

if ( 1 !== $route_replacement_count ) {
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
	throw new RuntimeException( 'The homepage could not be read after migration v22.' );
}

$persisted_content    = $persisted_home->post_content;
$persisted_route      = $find_anchor_block( parse_blocks( $persisted_content ), 'route' );
$persisted_serialized = is_array( $persisted_route ) ? serialize_block( $persisted_route ) : '';
$persisted_parts      = $inspect_route( $persisted_route );

$validate_content( $persisted_parts, $persisted_serialized, false );

update_option(
	$migration_key,
	array(
		'version'          => 22,
		'page_id'          => $page_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

clean_post_cache( $page_id );
