<?php
/**
 * Rename the editable tour-list heading on the published homepage.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key = 'turkey_signature_homepage_tour_list_heading_v26';
$page_id       = (int) get_option( 'page_on_front' );
$home          = $page_id ? get_post( $page_id ) : null;

if ( ! $home instanceof WP_Post || 'page' !== $home->post_type ) {
	throw new RuntimeException( 'The published WordPress front page could not be found.' );
}

$v25_state = get_option( 'turkey_signature_homepage_tour_cta_v25' );

if (
	! is_array( $v25_state ) ||
	(int) ( $v25_state['page_id'] ?? 0 ) !== $page_id ||
	empty( $v25_state['content_sha256'] )
) {
	throw new RuntimeException( 'The tour CTA migration v25 must exist before migration v26.' );
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

$validate_route = static function ( string $serialized ) use ( $find_class_blocks ) {
	$blocks   = parse_blocks( $serialized );
	$triggers = $find_class_blocks( $blocks, 'tour-details-trigger' );
	$panels   = $find_class_blocks( $blocks, 'tour-details' );

	if (
		1 !== substr_count( $serialized, '>Список наших авторских туров:<' ) ||
		0 !== substr_count( $serialized, '>Все туры<' ) ||
		8 !== count( $triggers ) ||
		8 !== count( $panels )
	) {
		throw new RuntimeException( 'The tour-list heading or route structure is invalid.' );
	}

	if ( false !== strpos( $serialized, '"lock"' ) || false !== strpos( $serialized, '"templateLock"' ) ) {
		throw new RuntimeException( 'The route unexpectedly contains editing locks.' );
	}
};

$content      = $home->post_content;
$current_hash = hash( 'sha256', $content );
$route        = $find_anchor_block( parse_blocks( $content ), 'route' );
$route_markup = is_array( $route ) ? serialize_block( $route ) : '';
$old_count    = substr_count( $route_markup, '>Все туры<' );
$new_count    = substr_count( $route_markup, '>Список наших авторских туров:<' );

if ( '' === $route_markup ) {
	throw new RuntimeException( 'The route block could not be found.' );
}

if ( 0 === $old_count && 1 === $new_count ) {
	$validate_route( $route_markup );
	update_option(
		$migration_key,
		array(
			'version'          => 26,
			'page_id'          => $page_id,
			'content_sha256'   => $current_hash,
			'completed_at_gmt' => gmdate( 'c' ),
		),
		false
	);
	return;
}

if ( ! hash_equals( (string) $v25_state['content_sha256'], $current_hash ) ) {
	throw new RuntimeException( 'The homepage changed after migration v25; migration v26 will not overwrite it.' );
}

if ( 1 !== $old_count || 0 !== $new_count ) {
	throw new RuntimeException( 'The existing tour-list heading could not be isolated safely.' );
}

$target_route = str_replace(
	'>Все туры<',
	'>Список наших авторских туров:<',
	$route_markup,
	$replacement_count
);

if ( 1 !== $replacement_count ) {
	throw new RuntimeException( 'The tour-list heading replacement count was not exactly one.' );
}

$validate_route( $target_route );

if ( 1 !== substr_count( $content, $route_markup ) ) {
	throw new RuntimeException( 'The route block could not be isolated safely in the homepage.' );
}

$target_content = str_replace( $route_markup, $target_route, $content, $route_replacement_count );

if ( 1 !== $route_replacement_count ) {
	throw new RuntimeException( 'The route block replacement count was not exactly one.' );
}

wp_save_post_revision( $page_id );

$result = wp_update_post(
	array(
		'ID'           => $page_id,
		'post_content' => $target_content,
	),
	true
);

if ( is_wp_error( $result ) ) {
	throw new RuntimeException( $result->get_error_message() );
}

clean_post_cache( $page_id );
$persisted       = get_post( $page_id );
$persisted_route = $persisted instanceof WP_Post
	? $find_anchor_block( parse_blocks( $persisted->post_content ), 'route' )
	: null;
$persisted_markup = is_array( $persisted_route ) ? serialize_block( $persisted_route ) : '';

$validate_route( $persisted_markup );
update_option(
	$migration_key,
	array(
		'version'          => 26,
		'page_id'          => $page_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted->post_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);
