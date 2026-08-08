<?php
/**
 * Reorder every tour page into hero, description, facts, and application CTA.
 *
 * Existing tour descriptions, programs, images, and shared header/footer
 * blocks are preserved. Only the tour-page composition is rearranged.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_tour_page_order_v38';
$migration_state = get_option( $migration_key );

$hash_matches_post = static function ( int $post_id, string $expected_hash ): bool {
	$post = get_post( $post_id );
	return $post instanceof WP_Post && '' !== $expected_hash && hash_equals( $expected_hash, hash( 'sha256', $post->post_content ) );
};

if ( is_array( $migration_state ) && ! empty( $migration_state['page_hashes'] ) ) {
	$complete = true;
	foreach ( (array) $migration_state['page_hashes'] as $page_id => $page_hash ) {
		$complete = $complete && $hash_matches_post( (int) $page_id, (string) $page_hash );
	}
	if ( $complete ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( 'Tour page order migration v38 is already complete.' );
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

$cta_note = 'Для получения более подробной и актуальной информации оставьте заявку на обратный звонок — и мы обязательно с вами свяжемся!';

$facts_markup  = '<!-- wp:group {"className":"tour-page-facts","layout":{"type":"grid","columnCount":2,"minimumColumnWidth":null}} -->';
$facts_markup .= '<div class="wp-block-group tour-page-facts">';
$facts_markup .= '<!-- wp:paragraph --><p><strong>8–12</strong><br>участников</p><!-- /wp:paragraph -->';
$facts_markup .= '<!-- wp:paragraph --><p><strong>Русский</strong><br>язык тура</p><!-- /wp:paragraph -->';
$facts_markup .= '</div><!-- /wp:group -->';

$application_markup  = '<!-- wp:group {"className":"tour-page-application-row","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->';
$application_markup .= '<div class="wp-block-group tour-page-application-row">';
$application_markup .= '<!-- wp:paragraph {"className":"tour-page-application-note"} --><p class="tour-page-application-note">' . esc_html( $cta_note ) . '</p><!-- /wp:paragraph -->';
$application_markup .= '<!-- wp:buttons {"className":"tour-page-application","layout":{"type":"flex","justifyContent":"right"}} --><div class="wp-block-buttons tour-page-application"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact-form">Оставить заявку <span>↗</span></a></div><!-- /wp:button --></div><!-- /wp:buttons -->';
$application_markup .= '</div><!-- /wp:group -->';

$booking_markup  = '<!-- wp:group {"align":"wide","className":"tour-page-booking","layout":{"type":"constrained"}} -->';
$booking_markup .= '<div class="wp-block-group alignwide tour-page-booking">' . $facts_markup . $application_markup . '</div><!-- /wp:group -->';
$booking_block   = $parse_single( $booking_markup );

$v27_state = get_option( 'turkey_signature_homepage_conversion_tour_pages_v27' );
$v37_state = get_option( 'turkey_signature_homepage_tour_layout_v37' );
$tour_ids  = is_array( $v27_state ) ? (array) ( $v27_state['page_ids'] ?? array() ) : array();
$v37_hashes = is_array( $v37_state ) ? (array) ( $v37_state['page_hashes'] ?? array() ) : array();

if ( 8 !== count( $tour_ids ) || 8 !== count( $v37_hashes ) ) {
	throw new RuntimeException( 'The eight v37 tour pages could not be identified.' );
}

$targets = array();

foreach ( $tour_ids as $slug => $page_id ) {
	$page_id = (int) $page_id;
	$page    = get_post( $page_id );
	if ( ! $page instanceof WP_Post || 'page' !== $page->post_type ) {
		throw new RuntimeException( sprintf( 'Tour page %s is missing.', $slug ) );
	}

	$current_blocks = parse_blocks( $page->post_content );
	$is_target      = 1 === $count_class( $current_blocks, 'tour-page-booking' ) &&
		1 === $count_class( $current_blocks, 'tour-page-copy-lead' ) &&
		1 === $count_class( $current_blocks, 'tour-page-application-note' ) &&
		0 === $count_class( $current_blocks, 'tour-page-hero__bottom' );

	if ( $is_target ) {
		$targets[ $page_id ] = $page->post_content;
		continue;
	}

	$expected_hash = (string) ( $v37_hashes[ $page_id ] ?? '' );
	if ( '' === $expected_hash || ! hash_equals( $expected_hash, hash( 'sha256', $page->post_content ) ) ) {
		throw new RuntimeException( sprintf( 'Tour page %s is not in the exact v37 state.', $slug ) );
	}

	if (
		1 !== $count_class( $current_blocks, 'tour-page-hero__bottom' ) ||
		1 !== $count_class( $current_blocks, 'tour-page-hero__lead' ) ||
		1 !== $count_class( $current_blocks, 'tour-page-facts' ) ||
		1 !== $count_class( $current_blocks, 'tour-page-application' ) ||
		1 !== $count_class( $current_blocks, 'tour-page-summary' ) ||
		1 !== $count_class( $current_blocks, 'tour-page-copy' )
	) {
		throw new RuntimeException( sprintf( 'Tour page %s has an unexpected v37 structure.', $slug ) );
	}

	$lead_block = $find_block( $current_blocks, 'tour-page-hero__lead' );
	$lead_text  = is_array( $lead_block ) ? trim( wp_strip_all_tags( (string) ( $lead_block['innerHTML'] ?? '' ) ) ) : '';
	if ( '' === $lead_text ) {
		throw new RuntimeException( sprintf( 'Tour page %s has no hero lead to preserve.', $slug ) );
	}

	$transform = null;
	$transform = static function ( array $blocks ) use ( &$transform, $block_has_class, $parse_single, $booking_block, $lead_text ): array {
		foreach ( $blocks as &$block ) {
			if ( ! empty( $block['innerBlocks'] ) ) {
				$block['innerBlocks'] = $transform( $block['innerBlocks'] );
			}

			if ( $block_has_class( $block, 'tour-page-hero__content' ) ) {
				$kept = '';
				foreach ( (array) $block['innerBlocks'] as $child ) {
					if (
						$block_has_class( $child, 'tour-page-hero__bottom' ) ||
						$block_has_class( $child, 'tour-page-facts' ) ||
						$block_has_class( $child, 'tour-page-application' )
					) {
						continue;
					}
					$kept .= serialize_block( $child );
				}
				$markup  = '<!-- wp:group {"align":"wide","className":"tour-page-hero__content","layout":{"type":"constrained"}} -->';
				$markup .= '<div class="wp-block-group alignwide tour-page-hero__content">' . $kept . '</div><!-- /wp:group -->';
				$block   = $parse_single( $markup );
				continue;
			}

			if ( $block_has_class( $block, 'tour-page-copy' ) ) {
				$inner = '';
				$added = false;
				foreach ( (array) $block['innerBlocks'] as $child ) {
					$inner .= serialize_block( $child );
					if ( ! $added && 'core/heading' === ( $child['blockName'] ?? '' ) ) {
						$inner .= '<!-- wp:paragraph {"className":"tour-page-copy-lead"} --><p class="tour-page-copy-lead">' . esc_html( $lead_text ) . '</p><!-- /wp:paragraph -->';
						$added  = true;
					}
				}
				if ( ! $added ) {
					throw new RuntimeException( 'The tour copy heading could not be preserved.' );
				}
				$markup  = '<!-- wp:column {"verticalAlignment":"top","width":"100%","className":"tour-page-copy"} -->';
				$markup .= '<div class="wp-block-column is-vertically-aligned-top tour-page-copy" style="flex-basis:100%">' . $inner . '</div><!-- /wp:column -->';
				$block   = $parse_single( $markup );
				continue;
			}

			if ( $block_has_class( $block, 'tour-page-body' ) ) {
				$children = array();
				$inserted = false;
				foreach ( (array) $block['innerBlocks'] as $child ) {
					$children[] = $child;
					if ( ! $inserted && $block_has_class( $child, 'tour-page-summary' ) ) {
						$children[] = $booking_block;
						$inserted   = true;
					}
				}
				if ( ! $inserted ) {
					throw new RuntimeException( 'The tour booking block could not be positioned.' );
				}
				$markup  = '<!-- wp:group {"align":"full","className":"tour-page-body","layout":{"type":"constrained"}} -->';
				$markup .= '<div class="wp-block-group alignfull tour-page-body">' . serialize_blocks( $children ) . '</div><!-- /wp:group -->';
				$block   = $parse_single( $markup );
			}
		}
		unset( $block );
		return $blocks;
	};

	$targets[ $page_id ] = serialize_blocks( $transform( $current_blocks ) );
}

foreach ( $targets as $page_id => $target_content ) {
	$blocks       = parse_blocks( $target_content );
	$hero         = $find_block( $blocks, 'tour-page-hero__content' );
	$body         = $find_block( $blocks, 'tour-page-body' );
	$copy         = $find_block( $blocks, 'tour-page-copy' );
	$body_markup  = is_array( $body ) ? serialize_block( $body ) : '';
	$summary_pos  = strpos( $body_markup, 'tour-page-summary' );
	$booking_pos  = strpos( $body_markup, 'tour-page-booking' );
	$program_pos  = strpos( $body_markup, 'tour-page-program' );

	if (
		! is_array( $hero ) ||
		! is_array( $body ) ||
		! is_array( $copy ) ||
		0 !== $count_class( array( $hero ), 'tour-page-hero__bottom' ) ||
		0 !== $count_class( array( $hero ), 'tour-page-facts' ) ||
		0 !== $count_class( array( $hero ), 'tour-page-application' ) ||
		1 !== $count_class( $blocks, 'tour-page-booking' ) ||
		1 !== $count_class( $blocks, 'tour-page-facts' ) ||
		1 !== $count_class( $blocks, 'tour-page-application-row' ) ||
		1 !== $count_class( $blocks, 'tour-page-application-note' ) ||
		1 !== $count_class( $blocks, 'tour-page-application' ) ||
		1 !== $count_class( $blocks, 'tour-page-copy-lead' ) ||
		false === $summary_pos ||
		false === $booking_pos ||
		$summary_pos >= $booking_pos ||
		( false !== $program_pos && $booking_pos >= $program_pos ) ||
		1 !== substr_count( $target_content, '>8–12</strong>' ) ||
		1 !== substr_count( $target_content, '>Русский</strong>' ) ||
		1 !== substr_count( $target_content, $cta_note ) ||
		false !== strpos( $target_content, '"lock"' ) ||
		false !== strpos( $target_content, '"templateLock"' )
	) {
		throw new RuntimeException( sprintf( 'Tour page %d failed v38 validation.', $page_id ) );
	}
}

foreach ( $targets as $page_id => $target_content ) {
	$current = get_post( $page_id );
	if ( $current instanceof WP_Post && ! hash_equals( hash( 'sha256', $current->post_content ), hash( 'sha256', $target_content ) ) ) {
		wp_save_post_revision( $page_id );
		$result = wp_update_post( array( 'ID' => $page_id, 'post_content' => wp_slash( $target_content ) ), true );
		if ( is_wp_error( $result ) ) {
			throw new RuntimeException( $result->get_error_message() );
		}
	}
}

$page_hashes = array();
foreach ( $targets as $page_id => $target_content ) {
	clean_post_cache( $page_id );
	$page = get_post( $page_id );
	if ( ! $page instanceof WP_Post || ! hash_equals( hash( 'sha256', $target_content ), hash( 'sha256', $page->post_content ) ) ) {
		throw new RuntimeException( sprintf( 'Tour page %d was not persisted exactly.', $page_id ) );
	}
	$page_hashes[ $page_id ] = hash( 'sha256', $page->post_content );
}

update_option(
	$migration_key,
	array(
		'version'      => 38,
		'page_hashes'  => $page_hashes,
		'completed_at' => gmdate( DATE_ATOM ),
	),
	false
);

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( 'Eight tour pages reordered by migration v38.' );
}
