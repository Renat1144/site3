<?php
/**
 * Temporarily remove the team section from the editable homepage.
 *
 * The removed Gutenberg markup is retained in the migration state so the
 * section can be restored later without reconstructing its content.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_hide_team_v39';
$migration_state = get_option( $migration_key );

$homepage_id = (int) get_option( 'page_on_front' );
$homepage    = $homepage_id ? get_post( $homepage_id ) : null;
if ( ! $homepage instanceof WP_Post || 'page' !== $homepage->post_type ) {
	throw new RuntimeException( 'The published WordPress front page could not be found.' );
}

$current_content = $homepage->post_content;
$current_hash    = hash( 'sha256', $current_content );
if (
	is_array( $migration_state ) &&
	$homepage_id === (int) ( $migration_state['page_id'] ?? 0 ) &&
	! empty( $migration_state['content_sha256'] ) &&
	hash_equals( (string) $migration_state['content_sha256'], $current_hash )
) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::success( 'Homepage team visibility migration v39 is already complete.' );
	}
	return;
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

$target_content = $current_content;
$team_block     = $find_block( parse_blocks( $current_content ), 'team-section' );
$removed_markup = is_array( $migration_state ) ? (string) ( $migration_state['removed_markup'] ?? '' ) : '';

if ( is_array( $team_block ) ) {
	$team_markup = serialize_block( $team_block );
	if ( 1 !== substr_count( $current_content, $team_markup ) ) {
		throw new RuntimeException( 'The team section was not unique on the homepage.' );
	}
	$target_content = str_replace( $team_markup, '', $current_content, $replacement_count );
	if ( 1 !== $replacement_count ) {
		throw new RuntimeException( 'The team section was not removed exactly once.' );
	}
	$removed_markup = $team_markup;
}

$target_blocks = parse_blocks( $target_content );
if (
	0 !== $count_class( $target_blocks, 'team-section' ) ||
	0 !== $count_class( $target_blocks, 'team-card' ) ||
	false !== strpos( $target_content, 'id="team"' ) ||
	false !== strpos( $target_content, 'Люди, которые' ) ||
	false !== strpos( $target_content, 'Имя участника' ) ||
	false !== strpos( $target_content, '"lock"' ) ||
	false !== strpos( $target_content, '"templateLock"' )
) {
	throw new RuntimeException( 'The homepage still contains team placeholder content.' );
}

if ( ! hash_equals( $current_hash, hash( 'sha256', $target_content ) ) ) {
	wp_save_post_revision( $homepage_id );
	$result = wp_update_post(
		array(
			'ID'           => $homepage_id,
			'post_content' => wp_slash( $target_content ),
		),
		true
	);
	if ( is_wp_error( $result ) ) {
		throw new RuntimeException( $result->get_error_message() );
	}
}

clean_post_cache( $homepage_id );
$persisted = get_post( $homepage_id );
if ( ! $persisted instanceof WP_Post || ! hash_equals( hash( 'sha256', $target_content ), hash( 'sha256', $persisted->post_content ) ) ) {
	throw new RuntimeException( 'The team-free homepage was not persisted exactly.' );
}

update_option(
	$migration_key,
	array(
		'version'               => 39,
		'page_id'               => $homepage_id,
		'content_sha256'        => hash( 'sha256', $persisted->post_content ),
		'removed_markup'        => $removed_markup,
		'removed_markup_sha256' => '' !== $removed_markup ? hash( 'sha256', $removed_markup ) : '',
		'completed_at'          => gmdate( DATE_ATOM ),
	),
	false
);

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( 'The homepage team section was temporarily removed.' );
}
