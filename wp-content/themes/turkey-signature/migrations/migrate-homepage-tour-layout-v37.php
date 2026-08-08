<?php
/**
 * Align reviews, remove placeholder introductions, and normalize tour pages.
 *
 * The migration preserves tour descriptions and programs while moving the
 * application button below editable tour facts, removing the obsolete side
 * card, and normalizing group size/language across every tour.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_tour_layout_v37';
$migration_state = get_option( $migration_key );

$hash_matches_post = static function ( int $post_id, string $expected_hash ): bool {
	$post = get_post( $post_id );
	return $post instanceof WP_Post && '' !== $expected_hash && hash_equals( $expected_hash, hash( 'sha256', $post->post_content ) );
};

$v38_state = get_option( 'turkey_signature_tour_page_order_v38' );
$v39_state = get_option( 'turkey_signature_homepage_hide_team_v39' );
if (
	is_array( $v39_state ) &&
	! empty( $v39_state['page_id'] ) &&
	$hash_matches_post( (int) $v39_state['page_id'], (string) ( $v39_state['content_sha256'] ?? '' ) ) &&
	is_array( $v38_state ) &&
	! empty( $v38_state['page_hashes'] )
) {
	$complete = true;
	foreach ( (array) $v38_state['page_hashes'] as $page_id => $page_hash ) {
		$complete = $complete && $hash_matches_post( (int) $page_id, (string) $page_hash );
	}
	if ( $complete ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( 'Homepage and tour layout migration v37 is superseded by v39.' );
		}
		return;
	}
}

if ( is_array( $v38_state ) && ! empty( $v38_state['page_hashes'] ) ) {
	$complete = is_array( $migration_state ) &&
		! empty( $migration_state['page_id'] ) &&
		$hash_matches_post( (int) $migration_state['page_id'], (string) ( $migration_state['content_sha256'] ?? '' ) );
	foreach ( (array) $v38_state['page_hashes'] as $page_id => $page_hash ) {
		$complete = $complete && $hash_matches_post( (int) $page_id, (string) $page_hash );
	}
	if ( $complete ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( 'Homepage and tour layout migration v37 is superseded by v38.' );
		}
		return;
	}
}

if ( is_array( $migration_state ) && ! empty( $migration_state['page_id'] ) && ! empty( $migration_state['page_hashes'] ) ) {
	$complete = $hash_matches_post( (int) $migration_state['page_id'], (string) ( $migration_state['content_sha256'] ?? '' ) );
	foreach ( (array) $migration_state['page_hashes'] as $page_id => $page_hash ) {
		$complete = $complete && $hash_matches_post( (int) $page_id, (string) $page_hash );
	}
	if ( $complete ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( 'Homepage and tour layout migration v37 is already complete.' );
		}
		return;
	}
}

$block_has_class = static function ( array $block, string $class_name ): bool {
	$classes = preg_split( '/\s+/', trim( (string) ( $block['attrs']['className'] ?? '' ) ) );
	return in_array( $class_name, $classes, true );
};

$find_block = static function ( array $blocks, string $class_name ) use ( &$find_block, $block_has_class ) {
	foreach ( $blocks as $block ) {
		if ( $block_has_class( $block, $class_name ) ) {
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

$count_class = static function ( array $blocks, string $class_name ) use ( &$count_class, $block_has_class ): int {
	$count = 0;
	foreach ( $blocks as $block ) {
		if ( $block_has_class( $block, $class_name ) ) {
			++$count;
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			$count += $count_class( $block['innerBlocks'], $class_name );
		}
	}
	return $count;
};

$parse_single = static function ( string $markup ): array {
	$blocks = parse_blocks( $markup );
	if ( 1 !== count( $blocks ) || empty( $blocks[0]['blockName'] ) ) {
		throw new RuntimeException( 'A generated Gutenberg block could not be parsed.' );
	}
	return $blocks[0];
};

$build_facts_markup = static function ( array $facts ): string {
	$markup  = '<!-- wp:group {"className":"tour-page-facts","layout":{"type":"grid","columnCount":' . count( $facts ) . ',"minimumColumnWidth":null}} -->';
	$markup .= '<div class="wp-block-group tour-page-facts">';
	foreach ( $facts as $fact ) {
		$markup .= '<!-- wp:paragraph --><p><strong>' . esc_html( $fact['value'] ) . '</strong><br>' . esc_html( $fact['label'] ) . '</p><!-- /wp:paragraph -->';
	}
	$markup .= '</div><!-- /wp:group -->';
	return $markup;
};

$normalize_facts = static function ( array $facts ): array {
	$normalized = array();
	$has_group  = false;
	$has_lang   = false;

	foreach ( $facts as $fact ) {
		$value = trim( (string) ( $fact['value'] ?? '' ) );
		$label = trim( (string) ( $fact['label'] ?? '' ) );
		if ( '' === $value || '' === $label ) {
			continue;
		}
		$label_lower = mb_strtolower( $label );
		if ( false !== mb_strpos( $label_lower, 'участ' ) ) {
			$value     = '8–12';
			$label     = 'участников';
			$has_group = true;
		}
		if ( false !== mb_strpos( $label_lower, 'язык' ) ) {
			$value    = 'Русский';
			$label    = 'язык тура';
			$has_lang = true;
		}
		$normalized[] = array( 'value' => $value, 'label' => $label );
	}

	if ( ! $has_group ) {
		$normalized[] = array( 'value' => '8–12', 'label' => 'участников' );
	}
	if ( ! $has_lang ) {
		$normalized[] = array( 'value' => 'Русский', 'label' => 'язык тура' );
	}

	return $normalized;
};

$extract_facts = static function ( array $facts_block ): array {
	$facts = array();
	foreach ( (array) ( $facts_block['innerBlocks'] ?? array() ) as $paragraph ) {
		$html = (string) ( $paragraph['innerHTML'] ?? '' );
		if ( ! preg_match( '/<strong>(.*?)<\/strong>/su', $html, $value_match ) ) {
			continue;
		}
		$value      = trim( html_entity_decode( wp_strip_all_tags( $value_match[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
		$label_html = preg_replace( '/<strong>.*?<\/strong>/su', '', $html, 1 );
		$label      = trim( html_entity_decode( wp_strip_all_tags( (string) $label_html ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
		if ( '' !== $value && '' !== $label ) {
			$facts[] = array( 'value' => $value, 'label' => $label );
		}
	}
	return $facts;
};

$application_markup = '<!-- wp:buttons {"className":"tour-page-application","layout":{"type":"flex","justifyContent":"right"}} --><div class="wp-block-buttons tour-page-application"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact-form">Оставить заявку <span>↗</span></a></div><!-- /wp:button --></div><!-- /wp:buttons -->';

$transform_tour_blocks = null;
$transform_tour_blocks = static function ( array $blocks ) use ( &$transform_tour_blocks, $block_has_class, $parse_single, $build_facts_markup, $normalize_facts, $extract_facts, $application_markup ): array {
	foreach ( $blocks as &$block ) {
		if ( ! empty( $block['innerBlocks'] ) ) {
			$block['innerBlocks'] = $transform_tour_blocks( $block['innerBlocks'] );
		}

		if ( 'core/heading' === ( $block['blockName'] ?? '' ) && 'О маршруте' === trim( wp_strip_all_tags( (string) ( $block['innerHTML'] ?? '' ) ) ) ) {
			$block = $parse_single( '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">О туре</h2><!-- /wp:heading -->' );
			continue;
		}

		if ( $block_has_class( $block, 'tour-page-hero__bottom' ) ) {
			$lead = '';
			foreach ( (array) $block['innerBlocks'] as $child ) {
				if ( $block_has_class( $child, 'tour-page-hero__lead' ) ) {
					$lead = serialize_block( $child );
					break;
				}
			}
			if ( '' === $lead ) {
				throw new RuntimeException( 'A tour hero lead could not be preserved.' );
			}
			$block = $parse_single( '<!-- wp:group {"className":"tour-page-hero__bottom","layout":{"type":"constrained"}} --><div class="wp-block-group tour-page-hero__bottom">' . $lead . '</div><!-- /wp:group -->' );
			continue;
		}

		if ( $block_has_class( $block, 'tour-page-facts' ) ) {
			$block = $parse_single( $build_facts_markup( $normalize_facts( $extract_facts( $block ) ) ) );
			continue;
		}

		if ( $block_has_class( $block, 'tour-page-summary' ) ) {
			$copy = null;
			foreach ( (array) $block['innerBlocks'] as $child ) {
				if ( $block_has_class( $child, 'tour-page-copy' ) ) {
					$copy = $child;
					break;
				}
			}
			if ( ! is_array( $copy ) ) {
				throw new RuntimeException( 'A tour description column could not be preserved.' );
			}
			$copy_markup  = '<!-- wp:column {"verticalAlignment":"top","width":"100%","className":"tour-page-copy"} -->';
			$copy_markup .= '<div class="wp-block-column is-vertically-aligned-top tour-page-copy" style="flex-basis:100%">' . serialize_blocks( (array) $copy['innerBlocks'] ) . '</div><!-- /wp:column -->';
			$summary      = '<!-- wp:columns {"align":"wide","verticalAlignment":"top","className":"tour-page-summary"} -->';
			$summary     .= '<div class="wp-block-columns alignwide are-vertically-aligned-top tour-page-summary">' . $copy_markup . '</div><!-- /wp:columns -->';
			$block        = $parse_single( $summary );
			continue;
		}

		if ( $block_has_class( $block, 'tour-page-hero__content' ) ) {
			$children     = (array) $block['innerBlocks'];
			$has_facts    = false;
			$child_markup = '';
			foreach ( $children as $child ) {
				if ( $block_has_class( $child, 'tour-page-facts' ) ) {
					$has_facts = true;
				}
				$child_markup .= serialize_block( $child );
			}
			if ( ! $has_facts ) {
				$child_markup .= $build_facts_markup(
					array(
						array( 'value' => '8–12', 'label' => 'участников' ),
						array( 'value' => 'Русский', 'label' => 'язык тура' ),
					)
				);
			}
			$child_markup .= $application_markup;
			$hero_markup   = '<!-- wp:group {"align":"wide","className":"tour-page-hero__content","layout":{"type":"constrained"}} -->';
			$hero_markup  .= '<div class="wp-block-group alignwide tour-page-hero__content">' . $child_markup . '</div><!-- /wp:group -->';
			$block         = $parse_single( $hero_markup );
		}
	}
	unset( $block );
	return $blocks;
};

$homepage_id = (int) get_option( 'page_on_front' );
$homepage    = $homepage_id ? get_post( $homepage_id ) : null;
if ( ! $homepage instanceof WP_Post || 'page' !== $homepage->post_type ) {
	throw new RuntimeException( 'The published WordPress front page could not be found.' );
}

$pattern_path = get_theme_file_path( 'patterns/turkey-landing.php' );
if ( ! is_readable( $pattern_path ) ) {
	throw new RuntimeException( 'The Turkey landing pattern could not be read.' );
}
ob_start();
include $pattern_path;
$landing_content = trim( (string) ob_get_clean() );

$current_home = $homepage->post_content;
$target_home  = $current_home;
$current_hash = hash( 'sha256', $current_home );
$v36_state    = get_option( 'turkey_signature_homepage_reviews_price_v36' );
$v36_hash     = is_array( $v36_state ) ? (string) ( $v36_state['content_sha256'] ?? '' ) : '';
$home_classes = array( 'reviews-section', 'primary-tour-footer' );

foreach ( $home_classes as $class_name ) {
	$current_block = $find_block( parse_blocks( $target_home ), $class_name );
	$target_block  = $find_block( parse_blocks( $landing_content ), $class_name );
	if ( ! is_array( $current_block ) || ! is_array( $target_block ) ) {
		throw new RuntimeException( 'A homepage section could not be isolated for migration v37.' );
	}
	$current_markup = serialize_block( $current_block );
	$target_markup  = serialize_block( $target_block );
	if ( hash_equals( hash( 'sha256', $current_markup ), hash( 'sha256', $target_markup ) ) ) {
		continue;
	}
	if ( '' === $v36_hash || ! hash_equals( $v36_hash, $current_hash ) ) {
		throw new RuntimeException( 'The homepage is neither the exact v36 state nor the current v37 landing.' );
	}
	if ( 1 !== substr_count( $target_home, $current_markup ) ) {
		throw new RuntimeException( 'A homepage section was not unique.' );
	}
	$target_home = str_replace( $current_markup, $target_markup, $target_home, $replacement_count );
	if ( 1 !== $replacement_count ) {
		throw new RuntimeException( 'A homepage section was not replaced exactly once.' );
	}
}

$home_blocks = parse_blocks( $target_home );
if (
	6 !== $count_class( $home_blocks, 'review-card' ) ||
	0 !== $count_class( $home_blocks, 'reviews-intro' ) ||
	0 !== $count_class( $home_blocks, 'team-intro' ) ||
	1 !== substr_count( $target_home, '<strong>8–12</strong><span>участников</span>' ) ||
	false !== strpos( $target_home, 'Тёплые впечатления путешественниц' ) ||
	false !== strpos( $target_home, 'Здесь появятся организаторы' ) ||
	false !== strpos( $target_home, '"lock"' ) ||
	false !== strpos( $target_home, '"templateLock"' )
) {
	throw new RuntimeException( 'The v37 homepage structure failed validation.' );
}

$tour_state = get_option( 'turkey_signature_homepage_conversion_tour_pages_v27' );
$tour_ids   = is_array( $tour_state ) ? (array) ( $tour_state['page_ids'] ?? array() ) : array();
if ( 8 !== count( $tour_ids ) ) {
	throw new RuntimeException( 'The eight existing tour pages could not be identified.' );
}

$tour_targets = array();
foreach ( $tour_ids as $slug => $page_id ) {
	$page = get_post( (int) $page_id );
	if ( ! $page instanceof WP_Post || 'page' !== $page->post_type ) {
		throw new RuntimeException( 'An expected tour page is missing.' );
	}
	$current_blocks = parse_blocks( $page->post_content );
	$is_target      = 1 === $count_class( $current_blocks, 'tour-page-facts' ) &&
		1 === $count_class( $current_blocks, 'tour-page-application' ) &&
		0 === $count_class( $current_blocks, 'tour-page-side' ) &&
		false !== strpos( $page->post_content, '>О туре</h2>' ) &&
		false !== strpos( $page->post_content, '>8–12</strong>' ) &&
		false !== strpos( $page->post_content, '>Русский</strong>' );

	if ( $is_target ) {
		$tour_targets[ (int) $page_id ] = $page->post_content;
		continue;
	}

	$is_source = 1 === $count_class( $current_blocks, 'tour-page-side' ) &&
		0 === $count_class( $current_blocks, 'tour-page-application' ) &&
		false !== strpos( $page->post_content, '>О маршруте</h2>' );
	if ( ! $is_source ) {
		throw new RuntimeException( sprintf( 'Tour page %s is not in an accepted v27/v37 state.', $slug ) );
	}

	$tour_targets[ (int) $page_id ] = serialize_blocks( $transform_tour_blocks( $current_blocks ) );
}

foreach ( $tour_targets as $page_id => $target_content ) {
	$blocks = parse_blocks( $target_content );
	if (
		1 !== $count_class( $blocks, 'tour-page-facts' ) ||
		1 !== $count_class( $blocks, 'tour-page-application' ) ||
		0 !== $count_class( $blocks, 'tour-page-side' ) ||
		1 !== substr_count( $target_content, '>О туре</h2>' ) ||
		0 !== substr_count( $target_content, '>О маршруте</h2>' ) ||
		1 !== substr_count( $target_content, '>8–12</strong>' ) ||
		1 !== substr_count( $target_content, '>Русский</strong>' ) ||
		false !== strpos( $target_content, 'Коротко о туре' ) ||
		false !== strpos( $target_content, 'Длительность, размер группы, даты и стоимость' ) ||
		false !== strpos( $target_content, '"lock"' ) ||
		false !== strpos( $target_content, '"templateLock"' )
	) {
		throw new RuntimeException( sprintf( 'Tour page %d failed v37 validation.', $page_id ) );
	}
}

if ( ! hash_equals( hash( 'sha256', $current_home ), hash( 'sha256', $target_home ) ) ) {
	wp_save_post_revision( $homepage_id );
	$result = wp_update_post( array( 'ID' => $homepage_id, 'post_content' => wp_slash( $target_home ) ), true );
	if ( is_wp_error( $result ) ) {
		throw new RuntimeException( $result->get_error_message() );
	}
}

foreach ( $tour_targets as $page_id => $target_content ) {
	$current = get_post( $page_id );
	if ( $current instanceof WP_Post && ! hash_equals( hash( 'sha256', $current->post_content ), hash( 'sha256', $target_content ) ) ) {
		wp_save_post_revision( $page_id );
		$result = wp_update_post( array( 'ID' => $page_id, 'post_content' => wp_slash( $target_content ) ), true );
		if ( is_wp_error( $result ) ) {
			throw new RuntimeException( $result->get_error_message() );
		}
	}
}

clean_post_cache( $homepage_id );
$persisted_home = get_post( $homepage_id );
if ( ! $persisted_home instanceof WP_Post || ! hash_equals( hash( 'sha256', $target_home ), hash( 'sha256', $persisted_home->post_content ) ) ) {
	throw new RuntimeException( 'The v37 homepage was not persisted exactly.' );
}

$page_hashes = array();
foreach ( array_keys( $tour_targets ) as $page_id ) {
	clean_post_cache( $page_id );
	$page = get_post( $page_id );
	if ( ! $page instanceof WP_Post || ! hash_equals( hash( 'sha256', $tour_targets[ $page_id ] ), hash( 'sha256', $page->post_content ) ) ) {
		throw new RuntimeException( sprintf( 'Tour page %d was not persisted exactly.', $page_id ) );
	}
	$page_hashes[ $page_id ] = hash( 'sha256', $page->post_content );
}

update_option(
	$migration_key,
	array(
		'version'        => 37,
		'page_id'        => $homepage_id,
		'content_sha256' => hash( 'sha256', $persisted_home->post_content ),
		'page_hashes'    => $page_hashes,
		'completed_at'   => gmdate( DATE_ATOM ),
	),
	false
);

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( 'Homepage and eight tour pages migrated to layout v37.' );
}
