<?php
/**
 * Refine the homepage composition and restore the three-card review preview.
 *
 * The migration accepts only the exact v30 homepage (or the current fresh
 * landing), replaces its single main block, saves a revision, and verifies
 * the persisted Gutenberg structure.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_design_refinement_v31';
$migration_state = get_option( $migration_key );

if ( is_array( $migration_state ) && ! empty( $migration_state['page_id'] ) ) {
	$saved_page = get_post( (int) $migration_state['page_id'] );
	if (
		$saved_page instanceof WP_Post &&
		! empty( $migration_state['content_sha256'] ) &&
		hash_equals( (string) $migration_state['content_sha256'], hash( 'sha256', $saved_page->post_content ) )
	) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( 'Homepage design refinement migration v31 is already complete.' );
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

$header_block = '<!-- wp:template-part {"slug":"header","theme":"turkey-signature","tagName":"header","className":"site-header"} /-->';
$footer_block = '<!-- wp:template-part {"slug":"footer","theme":"turkey-signature","tagName":"footer","className":"site-footer"} /-->';
$fresh_target = $header_block . "\n" . $landing_content . "\n" . $footer_block;
$already_target = hash_equals( hash( 'sha256', $fresh_target ), $current_hash );

if ( ! $already_target ) {
	$v30_state = get_option( 'turkey_signature_homepage_primary_tour_content_v30' );
	$v30_hash  = is_array( $v30_state ) ? (string) ( $v30_state['content_sha256'] ?? '' ) : '';
	$source_matches_v30 = '' !== $v30_hash && hash_equals( $v30_hash, $current_hash );

	if ( ! $source_matches_v30 && '' !== $v30_hash ) {
		$v30_revision = null;
		foreach ( wp_get_post_revisions( $homepage_id ) as $revision ) {
			if ( hash_equals( $v30_hash, hash( 'sha256', $revision->post_content ) ) ) {
				$v30_revision = $revision;
				break;
			}
		}

		$canonical_signature = static function ( string $content ): string {
			$normalize_value = static function ( $value ) use ( &$normalize_value ) {
				if ( ! is_array( $value ) ) {
					return $value;
				}
				if ( array_keys( $value ) !== range( 0, count( $value ) - 1 ) ) {
					ksort( $value );
				}
				foreach ( $value as $key => $item ) {
					$value[ $key ] = $normalize_value( $item );
				}
				return $value;
			};

			$normalize_blocks = static function ( array $blocks ) use ( &$normalize_blocks, $normalize_value ): array {
				$normalized = array();
				foreach ( $blocks as $block ) {
					$block_name = (string) ( $block['blockName'] ?? '' );
					if ( '' === $block_name ) {
						continue;
					}
					$attributes = (array) ( $block['attrs'] ?? array() );
					if ( 'core/heading' === $block_name && 2 === (int) ( $attributes['level'] ?? 2 ) ) {
						unset( $attributes['level'] );
					}
					$normalized[] = array(
						'name'       => $block_name,
						'attributes' => $normalize_value( $attributes ),
						'children'   => $normalize_blocks( (array) ( $block['innerBlocks'] ?? array() ) ),
					);
				}
				return $normalized;
			};

			$html = do_blocks( $content );
			$text_html = preg_replace( '/></u', '> <', $html );
			$text = html_entity_decode( wp_strip_all_tags( $text_html, true ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
			preg_match_all( '/[\p{L}\p{N}]+|[^\s\p{L}\p{N}]/u', $text, $text_tokens );
			preg_match_all( '/(?:href|src)=(["\x27])([^"\x27]+)\1/u', $html, $reference_matches );
			$references = $reference_matches[2];
			sort( $references );

			return hash(
				'sha256',
				wp_json_encode(
					array(
						'blocks'     => $normalize_blocks( parse_blocks( $content ) ),
						'text'       => $text_tokens[0],
						'references' => $references,
					),
					JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
				)
			);
		};

		$source_matches_v30 = $v30_revision instanceof WP_Post && hash_equals(
			$canonical_signature( $v30_revision->post_content ),
			$canonical_signature( $current_content )
		);
	}

	if ( ! $source_matches_v30 ) {
		throw new RuntimeException( 'The homepage is neither the exact v30 state nor the current fresh landing; migration v31 will not overwrite it.' );
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
	1 !== $count_class( $generated_blocks, 'primary-tour-footer' ) ||
	1 !== $count_class( $generated_blocks, 'primary-tour-facts' ) ||
	! is_array( $facts_block ) ||
	5 !== count( $facts_block['innerBlocks'] ) ||
	0 !== $count_class( $generated_blocks, 'hero-eyebrow' ) ||
	0 !== $count_class( $generated_blocks, 'dates-eyebrow' ) ||
	0 !== $count_class( $generated_blocks, 'destination-eyebrow' ) ||
	3 !== $count_class( $generated_blocks, 'review-card' ) ||
	1 !== $count_class( $generated_blocks, 'reviews-disclaimer' ) ||
	0 !== $count_class( $generated_blocks, 'reviews-grid-single' ) ||
	0 !== $count_class( $generated_blocks, 'review-card-placeholder' ) ||
	false === strpos( $target_content, 'До публикации замените примеры реальными отзывами' ) ||
	false === strpos( $target_content, 'демонстрационный текст' ) ||
	false !== strpos( $target_content, 'Камерные путешествия · Турция' ) ||
	false !== strpos( $target_content, 'Стоимость главного тура' ) ||
	false !== strpos( $target_content, 'Другие маршруты' ) ||
	false !== strpos( $target_content, '"lock"' ) ||
	false !== strpos( $target_content, '"templateLock"' )
) {
	throw new RuntimeException( 'The refined homepage structure failed validation.' );
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
	throw new RuntimeException( 'The refined homepage was not persisted exactly.' );
}

update_option(
	$migration_key,
	array(
		'version'          => 31,
		'page_id'          => $homepage_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted_homepage->post_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( sprintf( 'Refined the homepage composition on page %d.', $homepage_id ) );
}
