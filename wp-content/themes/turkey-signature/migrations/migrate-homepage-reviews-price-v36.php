<?php
/**
 * Add two approved reviews and replace the primary tour price with a range.
 *
 * The migration changes only the reviews and offer-card sections, accepts the
 * exact v35 homepage (or the already-current v36 state), saves one revision,
 * and verifies the persisted Gutenberg tree.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_reviews_price_v36';
$migration_state = get_option( $migration_key );

if ( is_array( $migration_state ) && ! empty( $migration_state['page_id'] ) ) {
	$saved_page = get_post( (int) $migration_state['page_id'] );
	if (
		$saved_page instanceof WP_Post &&
		! empty( $migration_state['content_sha256'] ) &&
		hash_equals( (string) $migration_state['content_sha256'], hash( 'sha256', $saved_page->post_content ) )
	) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( 'Homepage reviews and price migration v36 is already complete.' );
		}
		return;
	}
}

$homepage_id = (int) get_option( 'page_on_front' );
$homepage    = $homepage_id ? get_post( $homepage_id ) : null;

if ( ! $homepage instanceof WP_Post || 'page' !== $homepage->post_type ) {
	throw new RuntimeException( 'The published WordPress front page could not be found.' );
}

$current_content = $homepage->post_content;
$current_hash    = hash( 'sha256', $current_content );
$pattern_path    = get_theme_file_path( 'patterns/turkey-landing.php' );

$v37_state = get_option( 'turkey_signature_homepage_tour_layout_v37' );
if (
	is_array( $v37_state ) &&
	! empty( $v37_state['content_sha256'] ) &&
	hash_equals( (string) $v37_state['content_sha256'], $current_hash )
) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::success( 'Homepage reviews and price migration v36 is superseded by v37.' );
	}
	return;
}

if ( ! is_readable( $pattern_path ) ) {
	throw new RuntimeException( 'The Turkey landing pattern could not be read.' );
}

ob_start();
include $pattern_path;
$landing_content = trim( (string) ob_get_clean() );

$find_block = static function ( array $blocks, string $class_name ) use ( &$find_block ) {
	foreach ( $blocks as $block ) {
		$classes = preg_split( '/\s+/', trim( (string) ( $block['attrs']['className'] ?? '' ) ) );
		if ( in_array( $class_name, $classes, true ) ) {
			return $block;
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			$found = $find_block( $block['innerBlocks'], $class_name );
			if ( is_array( $found ) ) {
				return $found;
			}
		}
	}
	return null;
};

$count_class = static function ( array $blocks, string $class_name ) use ( &$count_class ): int {
	$count = 0;
	foreach ( $blocks as $block ) {
		$classes = preg_split( '/\s+/', trim( (string) ( $block['attrs']['className'] ?? '' ) ) );
		if ( in_array( $class_name, $classes, true ) ) {
			++$count;
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			$count += $count_class( $block['innerBlocks'], $class_name );
		}
	}
	return $count;
};

$current_blocks  = parse_blocks( $current_content );
$target_blocks   = parse_blocks( $landing_content );
$current_reviews = $find_block( $current_blocks, 'reviews-section' );
$target_reviews  = $find_block( $target_blocks, 'reviews-section' );
$current_offer   = $find_block( $current_blocks, 'offer-card' );
$target_offer    = $find_block( $target_blocks, 'offer-card' );

if (
	! is_array( $current_reviews ) ||
	! is_array( $target_reviews ) ||
	! is_array( $current_offer ) ||
	! is_array( $target_offer )
) {
	throw new RuntimeException( 'The current or target reviews/offer section could not be isolated.' );
}

$current_reviews_markup = serialize_block( $current_reviews );
$target_reviews_markup  = serialize_block( $target_reviews );
$current_offer_markup   = serialize_block( $current_offer );
$target_offer_markup    = serialize_block( $target_offer );
$already_target         = hash_equals( hash( 'sha256', $current_reviews_markup ), hash( 'sha256', $target_reviews_markup ) ) &&
	hash_equals( hash( 'sha256', $current_offer_markup ), hash( 'sha256', $target_offer_markup ) );
$target_content         = $current_content;

if ( ! $already_target ) {
	$v35_state = get_option( 'turkey_signature_homepage_real_reviews_v35' );
	$v35_hash  = is_array( $v35_state ) ? (string) ( $v35_state['content_sha256'] ?? '' ) : '';
	if ( '' === $v35_hash || ! hash_equals( $v35_hash, $current_hash ) ) {
		throw new RuntimeException( 'The homepage is neither the exact v35 state nor the current v36 landing; migration v36 will not overwrite it.' );
	}

	$replacements = array(
		$current_reviews_markup => $target_reviews_markup,
		$current_offer_markup   => $target_offer_markup,
	);
	foreach ( $replacements as $current_markup => $target_markup ) {
		if ( 1 !== substr_count( $target_content, $current_markup ) ) {
			throw new RuntimeException( 'A current homepage section was not unique.' );
		}
		$target_content = str_replace( $current_markup, $target_markup, $target_content, $replacement_count );
		if ( 1 !== $replacement_count ) {
			throw new RuntimeException( 'A homepage section was not replaced exactly once.' );
		}
	}
}

$generated_blocks = parse_blocks( $target_content );
$expected_authors = array(
	'Алла'      => 2,
	'Надежда'   => 1,
	'Валентина' => 2,
	'Татьяна'   => 1,
);
$price_note = 'Цена может меняться. Чтобы уточнить актуальную стоимость, свяжитесь с нами или оставьте заявку на обратный звонок по кнопке ниже — мы обязательно с вами свяжемся!';

if (
	6 !== $count_class( $generated_blocks, 'review-card' ) ||
	1 !== $count_class( $generated_blocks, 'reviews-grid-real' ) ||
	1 !== $count_class( $generated_blocks, 'offer-card' ) ||
	1 !== $count_class( $generated_blocks, 'offer-price' ) ||
	0 !== $count_class( $generated_blocks, 'offer-price-old' ) ||
	1 !== substr_count( $target_content, '74 600–214 000 ₽' ) ||
	1 !== substr_count( $target_content, $price_note ) ||
	false !== strpos( $target_content, '102 904 ₽' ) ||
	false !== strpos( $target_content, '144 066 ₽' ) ||
	false !== strpos( $target_content, '"lock"' ) ||
	false !== strpos( $target_content, '"templateLock"' )
) {
	throw new RuntimeException( 'The v36 homepage structure failed validation.' );
}

foreach ( $expected_authors as $author_name => $expected_count ) {
	if ( $expected_count !== substr_count( $target_content, '>' . $author_name . '</p>' ) ) {
		throw new RuntimeException( sprintf( 'The v36 review author %s has an unexpected count.', $author_name ) );
	}
}

if ( ! $already_target ) {
	wp_save_post_revision( $homepage_id );
	$updated_id = wp_update_post(
		array(
			'ID'           => $homepage_id,
			'post_content' => wp_slash( $target_content ),
		),
		true
	);
	if ( is_wp_error( $updated_id ) ) {
		throw new RuntimeException( $updated_id->get_error_message() );
	}
}

clean_post_cache( $homepage_id );
$persisted_homepage = get_post( $homepage_id );

if (
	! $persisted_homepage instanceof WP_Post ||
	! hash_equals( hash( 'sha256', $target_content ), hash( 'sha256', $persisted_homepage->post_content ) )
) {
	throw new RuntimeException( 'The v36 homepage was not persisted exactly.' );
}

update_option(
	$migration_key,
	array(
		'version'          => 36,
		'page_id'          => $homepage_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted_homepage->post_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( sprintf( 'Applied homepage reviews and price v36 on page %d.', $homepage_id ) );
}
