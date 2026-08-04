<?php
/**
 * Store the tour list as a native editable Gutenberg grid.
 *
 * The public theme has displayed this block as a grid since v22, but the
 * saved block attribute still described the retired horizontal flex track.
 * This migration changes only that one opening block comment, preserving all
 * cards, images, descriptions, their order and the owner's manual edits.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key = 'turkey_signature_homepage_editable_layouts_v23';
$page_id       = (int) get_option( 'page_on_front' );
$home          = $page_id ? get_post( $page_id ) : null;

if ( ! $home instanceof WP_Post || 'page' !== $home->post_type ) {
	throw new RuntimeException( 'The published WordPress front page could not be found.' );
}

$old_comment = '<!-- wp:group {"className":"destination-track","layout":{"type":"flex","flexWrap":"nowrap"}} -->';
$new_comment = '<!-- wp:group {"className":"destination-track","layout":{"type":"grid","columnCount":2,"minimumColumnWidth":null}} -->';
$content     = $home->post_content;
$old_count   = substr_count( $content, $old_comment );
$new_count   = substr_count( $content, $new_comment );

$count_class_blocks = static function ( array $blocks, string $class_name ) use ( &$count_class_blocks ) {
	$count = 0;

	foreach ( $blocks as $block ) {
		$classes = preg_split( '/\s+/', trim( (string) ( $block['attrs']['className'] ?? '' ) ) );

		if ( in_array( $class_name, $classes, true ) ) {
			++$count;
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$count += $count_class_blocks( $block['innerBlocks'], $class_name );
		}
	}

	return $count;
};

$validate = static function ( string $candidate ) use ( $old_comment, $new_comment, $count_class_blocks ) {
	if ( 8 !== $count_class_blocks( parse_blocks( $candidate ), 'destination-card' ) ) {
		throw new RuntimeException( 'The homepage tour card count is invalid.' );
	}

	if ( false !== strpos( $candidate, '"lock"' ) || false !== strpos( $candidate, '"templateLock"' ) ) {
		throw new RuntimeException( 'The homepage unexpectedly contains editing locks.' );
	}

	if ( 0 !== substr_count( $candidate, $old_comment ) || 1 !== substr_count( $candidate, $new_comment ) ) {
		throw new RuntimeException( 'The editable tour grid marker is invalid.' );
	}
};

if ( 0 === $old_count && 1 === $new_count ) {
	$validate( $content );

	update_option(
		$migration_key,
		array(
			'version'          => 23,
			'page_id'          => $page_id,
			'content_sha256'   => hash( 'sha256', $content ),
			'completed_at_gmt' => gmdate( 'c' ),
		),
		false
	);

	return;
}

if ( 1 !== $old_count || 0 !== $new_count ) {
	throw new RuntimeException( 'The retired tour track could not be isolated safely.' );
}

$target = str_replace( $old_comment, $new_comment, $content, $replacement_count );

if ( 1 !== $replacement_count ) {
	throw new RuntimeException( 'The tour track layout replacement count was not exactly one.' );
}

$validate( $target );
wp_save_post_revision( $page_id );

$result = wp_update_post(
	array(
		'ID'           => $page_id,
		'post_content' => $target,
	),
	true
);

if ( is_wp_error( $result ) ) {
	throw new RuntimeException( $result->get_error_message() );
}

clean_post_cache( $page_id );
$persisted = get_post( $page_id );

if ( ! $persisted instanceof WP_Post ) {
	throw new RuntimeException( 'The homepage could not be read after migration v23.' );
}

$validate( $persisted->post_content );

update_option(
	$migration_key,
	array(
		'version'          => 23,
		'page_id'          => $page_id,
		'previous_sha256'  => hash( 'sha256', $content ),
		'content_sha256'   => hash( 'sha256', $persisted->post_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

clean_post_cache( $page_id );
