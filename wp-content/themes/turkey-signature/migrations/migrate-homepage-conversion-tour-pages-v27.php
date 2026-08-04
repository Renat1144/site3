<?php
/**
 * Improve the homepage conversion flow and create editable tour pages.
 *
 * The migration accepts only the exact v26 homepage, creates each tour page
 * idempotently, then replaces the affected homepage sections and saves a
 * revision. Existing non-matching tour pages are never overwritten.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_conversion_tour_pages_v27';
$migration_state = get_option( $migration_key );

if ( is_array( $migration_state ) && ! empty( $migration_state['page_ids'] ) ) {
	$all_pages_exist = true;

	foreach ( (array) $migration_state['page_ids'] as $saved_page_id ) {
		$saved_page = get_post( (int) $saved_page_id );
		if ( ! $saved_page instanceof WP_Post || 'page' !== $saved_page->post_type ) {
			$all_pages_exist = false;
			break;
		}
	}

	if ( $all_pages_exist ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( 'Homepage conversion and tour-page migration v27 is already complete.' );
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

$pattern_path = get_theme_file_path( 'patterns/turkey-landing.php' );

if ( ! is_readable( $pattern_path ) ) {
	throw new RuntimeException( 'The Turkey landing pattern could not be read.' );
}

ob_start();
include $pattern_path;
$landing_content = trim( (string) ob_get_clean() );

if ( empty( $tours ) || 8 !== count( $tours ) ) {
	throw new RuntimeException( 'The landing pattern does not expose the expected eight tours.' );
}

$header_block = '<!-- wp:template-part {"slug":"header","theme":"turkey-signature","tagName":"header","className":"site-header"} /-->';
$footer_block = '<!-- wp:template-part {"slug":"footer","theme":"turkey-signature","tagName":"footer","className":"site-footer"} /-->';
$fresh_homepage_content  = $header_block . "\n" . $landing_content . "\n" . $footer_block;
$homepage_already_target = hash_equals( hash( 'sha256', $fresh_homepage_content ), $current_hash );

if ( ! $homepage_already_target ) {
	$v26_state = get_option( 'turkey_signature_homepage_tour_list_heading_v26' );

	if (
		! is_array( $v26_state ) ||
		(int) ( $v26_state['page_id'] ?? 0 ) !== $homepage_id ||
		empty( $v26_state['content_sha256'] ) ||
		! hash_equals( (string) $v26_state['content_sha256'], $current_hash )
	) {
		throw new RuntimeException( 'The homepage is neither the exact v26 state nor a fresh current landing; migration v27 will not overwrite it.' );
	}
}

$build_tour_page = static function ( array $tour ) use ( $header_block, $footer_block, $images_uri ): string {
	$image_url = esc_url( $images_uri . '/' . $tour['image'] );
	$title     = esc_html( $tour['title'] );
	$kicker    = esc_html( $tour['kicker'] );
	$lead      = esc_html( $tour['lead'] );
	$alt       = esc_attr( $tour['alt'] );

	$content  = $header_block . "\n";
	$content .= '<!-- wp:group {"tagName":"main","align":"full","anchor":"start","className":"tour-page","layout":{"type":"default"}} -->' . "\n";
	$content .= '<main id="start" class="wp-block-group alignfull tour-page">' . "\n";
	$content .= '<!-- wp:group {"align":"full","className":"tour-page-hero","layout":{"type":"constrained"}} -->' . "\n";
	$content .= '<div class="wp-block-group alignfull tour-page-hero">';
	$content .= '<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"tour-page-hero__image"} --><figure class="wp-block-image size-full tour-page-hero__image"><img src="' . $image_url . '" alt="' . $alt . '" /></figure><!-- /wp:image -->';
	$content .= '<!-- wp:group {"align":"wide","className":"tour-page-hero__content","layout":{"type":"constrained"}} --><div class="wp-block-group alignwide tour-page-hero__content">';
	$content .= '<!-- wp:paragraph {"className":"tour-page-kicker"} --><p class="tour-page-kicker">' . $kicker . '</p><!-- /wp:paragraph -->';
	$content .= '<!-- wp:heading {"level":1,"className":"tour-page-title"} --><h1 class="wp-block-heading tour-page-title">' . $title . '</h1><!-- /wp:heading -->';
	$content .= '<!-- wp:group {"className":"tour-page-hero__bottom","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} --><div class="wp-block-group tour-page-hero__bottom">';
	$content .= '<!-- wp:paragraph {"className":"tour-page-hero__lead"} --><p class="tour-page-hero__lead">' . $lead . '</p><!-- /wp:paragraph -->';
	$content .= '<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact-form">Оставить заявку <span>↗</span></a></div><!-- /wp:button --></div><!-- /wp:buttons -->';
	$content .= '</div><!-- /wp:group -->';

	if ( ! empty( $tour['facts'] ) ) {
		$content .= '<!-- wp:group {"className":"tour-page-facts","layout":{"type":"grid","columnCount":5,"minimumColumnWidth":null}} --><div class="wp-block-group tour-page-facts">';
		foreach ( $tour['facts'] as $fact ) {
			$content .= '<!-- wp:paragraph --><p><strong>' . esc_html( $fact['value'] ) . '</strong><br>' . esc_html( $fact['label'] ) . '</p><!-- /wp:paragraph -->';
		}
		$content .= '</div><!-- /wp:group -->';
	}

	$content .= '</div><!-- /wp:group -->';
	$content .= '</div><!-- /wp:group -->' . "\n";
	$content .= '<!-- wp:group {"align":"full","className":"tour-page-body","layout":{"type":"constrained"}} -->' . "\n";
	$content .= '<div class="wp-block-group alignfull tour-page-body">';
	$content .= '<!-- wp:columns {"align":"wide","verticalAlignment":"top","className":"tour-page-summary"} --><div class="wp-block-columns alignwide are-vertically-aligned-top tour-page-summary">';
	$content .= '<!-- wp:column {"verticalAlignment":"top","width":"65%","className":"tour-page-copy"} --><div class="wp-block-column is-vertically-aligned-top tour-page-copy" style="flex-basis:65%">';
	$content .= '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">О маршруте</h2><!-- /wp:heading -->';
	foreach ( (array) $tour['paragraphs'] as $paragraph ) {
		$content .= '<!-- wp:paragraph --><p>' . esc_html( $paragraph ) . '</p><!-- /wp:paragraph -->';
	}
	$content .= '</div><!-- /wp:column -->';
	$content .= '<!-- wp:column {"verticalAlignment":"top","className":"tour-page-side"} --><div class="wp-block-column is-vertically-aligned-top tour-page-side">';
	$content .= '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Коротко о туре</h3><!-- /wp:heading -->';

	if ( ! empty( $tour['facts'] ) ) {
		$content .= '<!-- wp:list --><ul class="wp-block-list">';
		foreach ( $tour['facts'] as $fact ) {
			$content .= '<li><strong>' . esc_html( $fact['value'] ) . '</strong> — ' . esc_html( $fact['label'] ) . '</li>';
		}
		$content .= '</ul><!-- /wp:list -->';
	} else {
		$content .= '<!-- wp:paragraph --><p>Длительность, размер группы, даты и стоимость этого маршрута уточняются. Оставьте заявку — сообщим подтверждённые условия до бронирования.</p><!-- /wp:paragraph -->';
	}

	if ( ! empty( $tour['price'] ) ) {
		$content .= '<!-- wp:separator --><hr class="wp-block-separator has-alpha-channel-opacity"/><!-- /wp:separator -->';
		$content .= '<!-- wp:paragraph {"className":"tour-page-price-old"} --><p class="tour-page-price-old"><s>' . esc_html( $tour['price']['old'] ) . '</s> <mark>' . esc_html( $tour['price']['discount'] ) . '</mark></p><!-- /wp:paragraph -->';
		$content .= '<!-- wp:paragraph {"className":"tour-page-price-current"} --><p class="tour-page-price-current"><strong>' . esc_html( $tour['price']['current'] ) . '</strong></p><!-- /wp:paragraph -->';
		$content .= '<!-- wp:paragraph --><p>' . esc_html( $tour['price']['note'] ) . '</p><!-- /wp:paragraph -->';
	}

	$content .= '</div><!-- /wp:column -->';
	$content .= '</div><!-- /wp:columns -->';

	if ( ! empty( $tour['program'] ) ) {
		$content .= '<!-- wp:group {"align":"wide","className":"tour-page-program","layout":{"type":"constrained"}} --><div class="wp-block-group alignwide tour-page-program">';
		$content .= '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Программа тура</h2><!-- /wp:heading -->';
		$content .= '<!-- wp:group {"className":"program-list","layout":{"type":"constrained"}} --><div class="wp-block-group program-list">';
		foreach ( $tour['program'] as $day ) {
			$content .= '<!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary>' . esc_html( $day['title'] ) . '</summary>';
			foreach ( $day['paragraphs'] as $day_paragraph ) {
				$content .= '<!-- wp:paragraph --><p>' . esc_html( $day_paragraph ) . '</p><!-- /wp:paragraph -->';
			}
			$content .= '</details><!-- /wp:details -->';
		}
		$content .= '</div><!-- /wp:group -->';
		$content .= '</div><!-- /wp:group -->';
	}

	$content .= '</div><!-- /wp:group -->' . "\n";
	$content .= '<!-- wp:group {"align":"full","anchor":"contact","className":"tour-page-contact","layout":{"type":"constrained"}} --><div id="contact" class="wp-block-group alignfull tour-page-contact">';
	$content .= '<!-- wp:heading {"level":2,"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Обсудим этот маршрут?</h2><!-- /wp:heading -->';
	$content .= '<!-- wp:paragraph --><p>Оставьте номер телефона — ответим на вопросы и поможем понять, подходит ли вам этот тур.</p><!-- /wp:paragraph -->';
	$content .= '<!-- wp:turkey-signature/contact-form /-->';
	$content .= '</div><!-- /wp:group -->' . "\n";
	$content .= '</main><!-- /wp:group -->' . "\n";
	$content .= $footer_block;

	return $content;
};

$tour_page_ids    = array();
$tour_page_hashes = array();

foreach ( $tours as $tour ) {
	if ( empty( $tour['slug'] ) || empty( $tour['title'] ) ) {
		throw new RuntimeException( 'A tour is missing its title or slug.' );
	}

	$page_content = $build_tour_page( $tour );
	$page_hash    = hash( 'sha256', $page_content );
	$existing     = get_page_by_path( $tour['slug'], OBJECT, 'page' );

	if ( $existing instanceof WP_Post ) {
		if ( ! hash_equals( $page_hash, hash( 'sha256', $existing->post_content ) ) ) {
			throw new RuntimeException( sprintf( 'The existing page "%s" has different content and was not overwritten.', $tour['title'] ) );
		}
		$page_id = $existing->ID;
	} else {
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_title'   => $tour['title'],
				'post_name'    => $tour['slug'],
				'post_status'  => 'publish',
				'post_author'  => (int) $homepage->post_author,
				'post_excerpt' => $tour['lead'],
				'post_content' => $page_content,
			),
			true
		);

		if ( is_wp_error( $page_id ) ) {
			throw new RuntimeException( $page_id->get_error_message() );
		}
	}

	$page_id = (int) $page_id;
	update_post_meta( $page_id, '_wp_page_template', 'default' );
	$tour_page_ids[ $tour['slug'] ]    = $page_id;
	$tour_page_hashes[ $tour['slug'] ] = $page_hash;
}

$find_block = static function ( array $blocks, string $selector_type, string $selector ) use ( &$find_block ) {
	foreach ( $blocks as $block ) {
		$matches = false;

		if ( 'anchor' === $selector_type ) {
			$matches = $selector === ( $block['attrs']['anchor'] ?? '' );
		} else {
			$classes = preg_split( '/\s+/', trim( (string) ( $block['attrs']['className'] ?? '' ) ) );
			$matches = in_array( $selector, $classes, true );
		}

		if ( $matches ) {
			return $block;
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$found = $find_block( $block['innerBlocks'], $selector_type, $selector );
			if ( is_array( $found ) ) {
				return $found;
			}
		}
	}

	return null;
};

$current_blocks = parse_blocks( $current_content );
$target_blocks  = parse_blocks( $landing_content );
$target_content = $current_content;

if ( ! $homepage_already_target ) {
	foreach (
		array(
			array( 'class', 'hero-cinematic' ),
			array( 'anchor', 'route' ),
			array( 'class', 'faq-section' ),
			array( 'anchor', 'contact' ),
		) as $selector
	) {
		$current_block = $find_block( $current_blocks, $selector[0], $selector[1] );
		$target_block  = $find_block( $target_blocks, $selector[0], $selector[1] );

		if ( ! is_array( $current_block ) || ! is_array( $target_block ) ) {
			throw new RuntimeException( sprintf( 'The homepage block "%s" could not be isolated.', $selector[1] ) );
		}

		$current_markup = serialize_block( $current_block );
		$target_markup  = serialize_block( $target_block );

		if ( 1 !== substr_count( $target_content, $current_markup ) ) {
			throw new RuntimeException( sprintf( 'The homepage block "%s" was not unique.', $selector[1] ) );
		}

		$target_content = str_replace( $current_markup, $target_markup, $target_content, $replacement_count );
		if ( 1 !== $replacement_count ) {
			throw new RuntimeException( sprintf( 'The homepage block "%s" was not replaced exactly once.', $selector[1] ) );
		}
	}

	$reviews_block = $find_block( $target_blocks, 'anchor', 'reviews' );
	$current_team  = $find_block( $current_blocks, 'anchor', 'team' );

	if ( ! is_array( $reviews_block ) || ! is_array( $current_team ) ) {
		throw new RuntimeException( 'The reviews or team section could not be isolated.' );
	}

	$reviews_markup = serialize_block( $reviews_block );
	$team_markup    = serialize_block( $current_team );

	if ( false !== strpos( $target_content, 'reviews-section' ) || 1 !== substr_count( $target_content, $team_markup ) ) {
		throw new RuntimeException( 'The reviews section cannot be inserted safely before the team section.' );
	}

	$target_content = str_replace( $team_markup, $reviews_markup . "\n" . $team_markup, $target_content, $reviews_replacement_count );
	if ( 1 !== $reviews_replacement_count ) {
		throw new RuntimeException( 'The reviews section was not inserted exactly once.' );
	}
}

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

$count_block_name = static function ( array $blocks, string $block_name ) use ( &$count_block_name ): int {
	$count = 0;
	foreach ( $blocks as $block ) {
		if ( $block_name === ( $block['blockName'] ?? '' ) ) {
			++$count;
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			$count += $count_block_name( $block['innerBlocks'], $block_name );
		}
	}
	return $count;
};

$generated_blocks = parse_blocks( $target_content );
$generated_faq    = $find_block( $generated_blocks, 'class', 'faq-section' );

if (
	8 !== $count_class( $generated_blocks, 'destination-card' ) ||
	8 !== $count_class( $generated_blocks, 'tour-details-trigger' ) ||
	0 !== $count_class( $generated_blocks, 'tour-details-library' ) ||
	3 !== $count_class( $generated_blocks, 'review-card' ) ||
	! is_array( $generated_faq ) ||
	8 !== $count_block_name( array( $generated_faq ), 'core/details' ) ||
	false !== strpos( $target_content, '"lock"' ) ||
	false !== strpos( $target_content, '"templateLock"' )
) {
	throw new RuntimeException( 'The generated homepage structure failed validation.' );
}

if ( ! $homepage_already_target ) {
	wp_save_post_revision( $homepage_id );

	$updated_homepage_id = wp_update_post(
		array(
			'ID'           => $homepage_id,
			'post_content' => wp_slash( $target_content ),
		),
		true
	);

	if ( is_wp_error( $updated_homepage_id ) ) {
		throw new RuntimeException( $updated_homepage_id->get_error_message() );
	}
}

clean_post_cache( $homepage_id );
$persisted_homepage = get_post( $homepage_id );

if ( ! $persisted_homepage instanceof WP_Post || ! hash_equals( hash( 'sha256', $target_content ), hash( 'sha256', $persisted_homepage->post_content ) ) ) {
	throw new RuntimeException( 'The updated homepage was not persisted exactly.' );
}

update_option(
	$migration_key,
	array(
		'version'          => 27,
		'page_id'          => $homepage_id,
		'page_ids'         => $tour_page_ids,
		'page_hashes'      => $tour_page_hashes,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted_homepage->post_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

flush_rewrite_rules( false );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( sprintf( 'Updated homepage %d and created or verified %d editable tour pages.', $homepage_id, count( $tour_page_ids ) ) );
}
