<?php
/**
 * Apply the v33 homepage control cleanup and compact section transitions.
 *
 * The migration accepts only the exact v32 homepage (or the already-current
 * landing), saves one revision, and verifies the persisted Gutenberg tree.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_compact_layout_v33';
$migration_state = get_option( $migration_key );

if ( is_array( $migration_state ) && ! empty( $migration_state['page_id'] ) ) {
	$saved_page = get_post( (int) $migration_state['page_id'] );
	if (
		$saved_page instanceof WP_Post &&
		! empty( $migration_state['content_sha256'] ) &&
		hash_equals( (string) $migration_state['content_sha256'], hash( 'sha256', $saved_page->post_content ) )
	) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( 'Homepage compact layout migration v33 is already complete.' );
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

if ( ! is_readable( $pattern_path ) ) {
	throw new RuntimeException( 'The Turkey landing pattern could not be read.' );
}

ob_start();
include $pattern_path;
$landing_content = trim( (string) ob_get_clean() );

$header_block   = '<!-- wp:template-part {"slug":"header","theme":"turkey-signature","tagName":"header","className":"site-header"} /-->';
$footer_block   = '<!-- wp:template-part {"slug":"footer","theme":"turkey-signature","tagName":"footer","className":"site-footer"} /-->';
$fresh_target   = $header_block . "\n" . $landing_content . "\n" . $footer_block;
$already_target = hash_equals( hash( 'sha256', $fresh_target ), $current_hash );

if ( ! $already_target ) {
	$v32_state = get_option( 'turkey_signature_homepage_visual_polish_v32' );
	$v32_hash  = is_array( $v32_state ) ? (string) ( $v32_state['content_sha256'] ?? '' ) : '';
	if ( '' === $v32_hash || ! hash_equals( $v32_hash, $current_hash ) ) {
		throw new RuntimeException( 'The homepage is neither the exact v32 state nor the current v33 landing; migration v33 will not overwrite it.' );
	}
}

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

$target_content = $current_content;

if ( ! $already_target ) {
	$current_main = $find_block( parse_blocks( $current_content ), 'turkey-site' );
	$target_main  = $find_block( parse_blocks( $landing_content ), 'turkey-site' );
	if ( ! is_array( $current_main ) || ! is_array( $target_main ) ) {
		throw new RuntimeException( 'The current or target homepage main block could not be isolated.' );
	}
	$current_markup = serialize_block( $current_main );
	$target_markup  = serialize_block( $target_main );
	if ( 1 !== substr_count( $target_content, $current_markup ) ) {
		throw new RuntimeException( 'The current homepage main block was not unique.' );
	}
	$target_content = str_replace( $current_markup, $target_markup, $target_content, $replacement_count );
	if ( 1 !== $replacement_count ) {
		throw new RuntimeException( 'The homepage main block was not replaced exactly once.' );
	}
}

$generated_blocks = parse_blocks( $target_content );
$facts_block      = $find_block( $generated_blocks, 'primary-tour-facts' );

if (
	0 !== $count_class( $generated_blocks, 'primary-tour-actions' ) ||
	0 !== $count_class( $generated_blocks, 'welcome-actions' ) ||
	0 !== $count_class( $generated_blocks, 'program-eyebrow' ) ||
	1 !== $count_class( $generated_blocks, 'primary-tour-footer' ) ||
	! is_array( $facts_block ) ||
	5 !== count( $facts_block['innerBlocks'] ) ||
	7 !== $count_class( $generated_blocks, 'destination-card' ) ||
	7 !== $count_class( $generated_blocks, 'tour-details-trigger' ) ||
	1 !== $count_class( $generated_blocks, 'tour-selection-cta' ) ||
	false !== strpos( $target_content, 'Смотреть программу' ) ||
	false !== strpos( $target_content, 'Посмотреть программу' ) ||
	false !== strpos( $target_content, 'Программа · Восточная сказка' ) ||
	false === strpos( $target_content, 'Выбери понравившийся тебе тур' ) ||
	false !== strpos( $target_content, '"lock"' ) ||
	false !== strpos( $target_content, '"templateLock"' )
) {
	throw new RuntimeException( 'The v33 homepage structure failed validation.' );
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
	throw new RuntimeException( 'The v33 homepage was not persisted exactly.' );
}

update_option(
	$migration_key,
	array(
		'version'          => 33,
		'page_id'          => $homepage_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted_homepage->post_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( sprintf( 'Applied homepage compact layout v33 on page %d.', $homepage_id ) );
}
