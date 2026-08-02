<?php
/**
 * Add the supplied facts, price and five-day programme to Eastern Fairytale.
 *
 * The migration replaces only the editable route block after verifying the
 * exact v17 content hash and saves a WordPress revision before the update.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_fairytale_program_v18';
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

$v17_state = get_option( 'turkey_signature_homepage_tour_card_row_v17' );

if (
	! is_array( $v17_state ) ||
	(int) ( $v17_state['page_id'] ?? 0 ) !== $page_id ||
	empty( $v17_state['content_sha256'] )
) {
	throw new RuntimeException( 'The tour card row migration v17 must exist before migration v18.' );
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
$current_copies     = is_array( $current_block ) ? $find_class_blocks( array( $current_block ), 'destination-card-copy' ) : array();

if ( ! hash_equals( (string) $v17_state['content_sha256'], $current_hash ) ) {
	throw new RuntimeException( 'The homepage changed after migration v17; migration v18 will not overwrite it.' );
}

if (
	'' === $current_serialized ||
	5 !== substr_count( $current_serialized, 'class="wp-block-group destination-card tour-card-' ) ||
	5 !== substr_count( $current_serialized, 'class="wp-block-buttons tour-details-trigger"' ) ||
	5 !== substr_count( $current_serialized, 'class="wp-block-group tour-details"' ) ||
	5 !== count( $current_copies ) ||
	false === strpos( $current_serialized, '>Восточная сказка<' ) ||
	false !== strpos( $current_serialized, 'tour-detail-program' ) ||
	false !== strpos( $current_serialized, '102 904 ₽' )
) {
	throw new RuntimeException( 'The current route block no longer matches the expected v17 structure.' );
}

foreach ( $current_copies as $current_copy ) {
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
$landing_content   = trim( (string) ob_get_clean() );
$target_block      = $find_anchor_block( parse_blocks( $landing_content ), 'route' );
$target_serialized = is_array( $target_block ) ? serialize_block( $target_block ) : '';
$target_copies     = is_array( $target_block ) ? $find_class_blocks( array( $target_block ), 'destination-card-copy' ) : array();
$target_facts      = is_array( $target_block ) ? $find_class_blocks( array( $target_block ), 'tour-detail-facts--five' ) : array();
$target_programs   = is_array( $target_block ) ? $find_class_blocks( array( $target_block ), 'tour-detail-program' ) : array();
$target_days       = is_array( $target_block ) ? $find_class_blocks( array( $target_block ), 'tour-detail-day' ) : array();
$target_prices     = is_array( $target_block ) ? $find_class_blocks( array( $target_block ), 'tour-detail-price' ) : array();

if (
	'' === $target_serialized ||
	5 !== substr_count( $target_serialized, 'class="wp-block-group destination-card tour-card-' ) ||
	5 !== substr_count( $target_serialized, 'class="wp-block-buttons tour-details-trigger"' ) ||
	5 !== substr_count( $target_serialized, 'class="wp-block-group tour-details"' ) ||
	5 !== count( $target_copies ) ||
	1 !== count( $target_facts ) ||
	1 !== count( $target_programs ) ||
	5 !== count( $target_days ) ||
	1 !== count( $target_prices ) ||
	false === strpos( $target_serialized, 'Комфорт · уникальное жильё' ) ||
	false === strpos( $target_serialized, 'Активность · средняя' ) ||
	false === strpos( $target_serialized, 'День 5 · Последнее утро и до скорой встречи' ) ||
	false === strpos( $target_serialized, '144 066 ₽' ) ||
	false === strpos( $target_serialized, '102 904 ₽' ) ||
	false === strpos( $target_serialized, '−29%' ) ||
	false === strpos( $target_serialized, '>Записаться на тур<' )
) {
	throw new RuntimeException( 'The generated v18 Eastern Fairytale programme is incomplete.' );
}

foreach ( $target_copies as $target_copy ) {
	if (
		1 !== count( $target_copy['innerBlocks'] ?? array() ) ||
		'core/heading' !== ( $target_copy['innerBlocks'][0]['blockName'] ?? '' )
	) {
		throw new RuntimeException( 'A generated v18 card contains content other than its title.' );
	}
}

if ( false !== strpos( $target_serialized, '"lock"' ) || false !== strpos( $target_serialized, '"templateLock"' ) ) {
	throw new RuntimeException( 'The generated v18 block unexpectedly contains editing locks.' );
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
	throw new RuntimeException( 'The homepage could not be read after migration v18.' );
}

$persisted_content  = $persisted_home->post_content;
$persisted_block    = $find_anchor_block( parse_blocks( $persisted_content ), 'route' );
$persisted_markup   = is_array( $persisted_block ) ? serialize_block( $persisted_block ) : '';
$persisted_programs = is_array( $persisted_block ) ? $find_class_blocks( array( $persisted_block ), 'tour-detail-program' ) : array();
$persisted_days     = is_array( $persisted_block ) ? $find_class_blocks( array( $persisted_block ), 'tour-detail-day' ) : array();
$persisted_prices   = is_array( $persisted_block ) ? $find_class_blocks( array( $persisted_block ), 'tour-detail-price' ) : array();

if (
	1 !== count( $persisted_programs ) ||
	5 !== count( $persisted_days ) ||
	1 !== count( $persisted_prices ) ||
	false === strpos( $persisted_markup, '102 904 ₽' ) ||
	false === strpos( $persisted_markup, '>Записаться на тур<' ) ||
	false !== strpos( $persisted_markup, '"lock"' ) ||
	false !== strpos( $persisted_markup, '"templateLock"' )
) {
	throw new RuntimeException( 'The v18 Eastern Fairytale programme was not persisted correctly.' );
}

update_option(
	$migration_key,
	array(
		'version'          => 18,
		'page_id'          => $page_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

clean_post_cache( $page_id );
