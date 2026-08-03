<?php
/**
 * Add the supplied children and Istanbul empires tours.
 *
 * The migration replaces only the editable route block after verifying the
 * exact v20 homepage hash and saves a WordPress revision before the update.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_additional_tours_v21';
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

$v20_state = get_option( 'turkey_signature_homepage_cappadocia_comfort_v20' );

if (
	! is_array( $v20_state ) ||
	(int) ( $v20_state['page_id'] ?? 0 ) !== $page_id ||
	empty( $v20_state['content_sha256'] )
) {
	throw new RuntimeException( 'The Cappadocia comfort migration v20 must exist before migration v21.' );
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
	return array(
		'copies'   => is_array( $route ) ? $find_class_blocks( array( $route ), 'destination-card-copy' ) : array(),
		'triggers' => is_array( $route ) ? $find_class_blocks( array( $route ), 'tour-details-trigger' ) : array(),
		'details'  => is_array( $route ) ? $find_class_blocks( array( $route ), 'tour-details' ) : array(),
		'programs' => is_array( $route ) ? $find_class_blocks( array( $route ), 'tour-detail-program' ) : array(),
		'days'     => is_array( $route ) ? $find_class_blocks( array( $route ), 'tour-detail-day' ) : array(),
		'prices'   => is_array( $route ) ? $find_class_blocks( array( $route ), 'tour-detail-price' ) : array(),
	);
};

$current_content    = $home->post_content;
$current_hash       = hash( 'sha256', $current_content );
$current_route      = $find_anchor_block( parse_blocks( $current_content ), 'route' );
$current_serialized = is_array( $current_route ) ? serialize_block( $current_route ) : '';
$current_parts      = $inspect_route( $current_route );

if ( ! hash_equals( (string) $v20_state['content_sha256'], $current_hash ) ) {
	throw new RuntimeException( 'The homepage changed after migration v20; migration v21 will not overwrite it.' );
}

if (
	'' === $current_serialized ||
	6 !== count( $current_parts['copies'] ) ||
	6 !== count( $current_parts['triggers'] ) ||
	6 !== count( $current_parts['details'] ) ||
	1 !== count( $current_parts['programs'] ) ||
	5 !== count( $current_parts['days'] ) ||
	1 !== count( $current_parts['prices'] ) ||
	false === strpos( $current_serialized, 'tour-card-cappadocia-comfort' ) ||
	false === strpos( $current_serialized, 'Стамбул и Каппадокия: комфорт-тур' ) ||
	false !== strpos( $current_serialized, 'tour-card-city-box' ) ||
	false !== strpos( $current_serialized, 'tour-card-empires' ) ||
	false !== strpos( $current_serialized, '>Город-шкатулка для детей<' ) ||
	false !== strpos( $current_serialized, '>Слияние империй: Стамбул<' )
) {
	throw new RuntimeException( 'The current route block no longer matches the expected v20 structure.' );
}

foreach ( $current_parts['copies'] as $current_copy ) {
	if (
		1 !== count( $current_copy['innerBlocks'] ?? array() ) ||
		'core/heading' !== ( $current_copy['innerBlocks'][0]['blockName'] ?? '' )
	) {
		throw new RuntimeException( 'A current card contains content other than its title.' );
	}
}

$pattern_path = get_theme_file_path( 'patterns/turkey-landing.php' );

if ( ! is_readable( $pattern_path ) ) {
	throw new RuntimeException( 'The Turkey landing pattern could not be read.' );
}

ob_start();
include $pattern_path;
$landing_content    = trim( (string) ob_get_clean() );
$target_route       = $find_anchor_block( parse_blocks( $landing_content ), 'route' );
$target_serialized  = is_array( $target_route ) ? serialize_block( $target_route ) : '';
$target_parts       = $inspect_route( $target_route );
$required_fragments = array(
	'tour-card-cappadocia-comfort',
	'Стамбул и Каппадокия: комфорт-тур',
	'tour-card-city-box',
	'Город-шкатулка для детей',
	'deti2.png',
	'Посмотрите на мир глазами ребёнка',
	'tour-card-empires',
	'Слияние империй: Стамбул',
	'stambul2.png',
	'Римской, Византийской и Османской',
	'День 1 · Первое знакомство с городом',
	'прибыть до 14:00',
	'уютном кафе с потрясающим видом',
	'Комфорт · уникальное жильё',
	'102 904 ₽',
);

if (
	'' === $target_serialized ||
	8 !== count( $target_parts['copies'] ) ||
	8 !== count( $target_parts['triggers'] ) ||
	8 !== count( $target_parts['details'] ) ||
	2 !== count( $target_parts['programs'] ) ||
	6 !== count( $target_parts['days'] ) ||
	1 !== count( $target_parts['prices'] )
) {
	throw new RuntimeException( 'The generated v21 route block has an invalid block count.' );
}

foreach ( $required_fragments as $required_fragment ) {
	if ( false === strpos( $target_serialized, $required_fragment ) ) {
		throw new RuntimeException( 'The generated v21 route block is missing required content.' );
	}
}

foreach ( $target_parts['copies'] as $target_copy ) {
	if (
		1 !== count( $target_copy['innerBlocks'] ?? array() ) ||
		'core/heading' !== ( $target_copy['innerBlocks'][0]['blockName'] ?? '' )
	) {
		throw new RuntimeException( 'A generated v21 card contains content other than its title.' );
	}
}

if ( false !== strpos( $target_serialized, 'nbsp;' ) ) {
	throw new RuntimeException( 'The generated v21 route block still contains an nbsp artifact.' );
}

if ( false !== strpos( $target_serialized, '"lock"' ) || false !== strpos( $target_serialized, '"templateLock"' ) ) {
	throw new RuntimeException( 'The generated v21 block unexpectedly contains editing locks.' );
}

if ( 1 !== substr_count( $current_content, $current_serialized ) ) {
	throw new RuntimeException( 'The current route block could not be isolated safely.' );
}

$page_content = str_replace( $current_serialized, $target_serialized, $current_content, $replacement_count );

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
	throw new RuntimeException( 'The homepage could not be read after migration v21.' );
}

$persisted_content    = $persisted_home->post_content;
$persisted_route      = $find_anchor_block( parse_blocks( $persisted_content ), 'route' );
$persisted_serialized = is_array( $persisted_route ) ? serialize_block( $persisted_route ) : '';
$persisted_parts      = $inspect_route( $persisted_route );

if (
	8 !== count( $persisted_parts['copies'] ) ||
	8 !== count( $persisted_parts['triggers'] ) ||
	8 !== count( $persisted_parts['details'] ) ||
	2 !== count( $persisted_parts['programs'] ) ||
	6 !== count( $persisted_parts['days'] ) ||
	1 !== count( $persisted_parts['prices'] ) ||
	false !== strpos( $persisted_serialized, 'nbsp;' ) ||
	false !== strpos( $persisted_serialized, '"lock"' ) ||
	false !== strpos( $persisted_serialized, '"templateLock"' )
) {
	throw new RuntimeException( 'The v21 route block was not persisted correctly.' );
}

foreach ( $required_fragments as $required_fragment ) {
	if ( false === strpos( $persisted_serialized, $required_fragment ) ) {
		throw new RuntimeException( 'The persisted v21 route block is missing required content.' );
	}
}

update_option(
	$migration_key,
	array(
		'version'          => 21,
		'page_id'          => $page_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

clean_post_cache( $page_id );
