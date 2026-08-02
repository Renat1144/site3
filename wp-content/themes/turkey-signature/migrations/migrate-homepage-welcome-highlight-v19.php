<?php
/**
 * Remove the saved inline highlight from the welcome heading.
 *
 * The migration updates only the exact Gutenberg heading block after
 * verifying the v18 homepage hash and saves a WordPress revision.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_homepage_welcome_highlight_v19';
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

$v18_state = get_option( 'turkey_signature_homepage_fairytale_program_v18' );

if (
	! is_array( $v18_state ) ||
	(int) ( $v18_state['page_id'] ?? 0 ) !== $page_id ||
	empty( $v18_state['content_sha256'] )
) {
	throw new RuntimeException( 'The Eastern Fairytale migration v18 must exist before migration v19.' );
}

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

$current_content = $home->post_content;
$current_hash    = hash( 'sha256', $current_content );

if ( ! hash_equals( (string) $v18_state['content_sha256'], $current_hash ) ) {
	throw new RuntimeException( 'The homepage changed after migration v18; migration v19 will not overwrite it.' );
}

$welcome_headings = $find_class_blocks( parse_blocks( $current_content ), 'welcome-title' );

if ( 1 !== count( $welcome_headings ) || 'core/heading' !== ( $welcome_headings[0]['blockName'] ?? '' ) ) {
	throw new RuntimeException( 'The welcome heading could not be isolated safely.' );
}

$current_serialized = serialize_block( $welcome_headings[0] );
$marked_text        = '<mark class="has-inline-color has-night-color">Приветствую вас, дорогие<br>путешественники</mark>';
$plain_text         = 'Приветствую вас, дорогие<br>путешественники';

if (
	1 !== substr_count( $current_serialized, $marked_text ) ||
	false !== strpos( $current_serialized, 'background-color' )
) {
	throw new RuntimeException( 'The welcome heading no longer matches the expected highlighted state.' );
}

$target_serialized = str_replace( $marked_text, $plain_text, $current_serialized, $heading_replacements );

if (
	1 !== $heading_replacements ||
	false !== strpos( $target_serialized, '<mark' ) ||
	false === strpos( $target_serialized, $plain_text )
) {
	throw new RuntimeException( 'The clean welcome heading could not be generated.' );
}

if ( 1 !== substr_count( $current_content, $current_serialized ) ) {
	throw new RuntimeException( 'The current welcome heading occurs more than once.' );
}

$page_content = str_replace( $current_serialized, $target_serialized, $current_content, $page_replacements );

if ( 1 !== $page_replacements ) {
	throw new RuntimeException( 'The welcome heading replacement count was not exactly one.' );
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
	throw new RuntimeException( 'The homepage could not be read after migration v19.' );
}

$persisted_content  = $persisted_home->post_content;
$persisted_headings = $find_class_blocks( parse_blocks( $persisted_content ), 'welcome-title' );
$persisted_heading  = 1 === count( $persisted_headings ) ? serialize_block( $persisted_headings[0] ) : '';

if (
	'' === $persisted_heading ||
	false !== strpos( $persisted_heading, '<mark' ) ||
	false !== strpos( $persisted_heading, 'has-night-color' ) ||
	false === strpos( $persisted_heading, $plain_text ) ||
	false !== strpos( $persisted_content, '"lock"' ) ||
	false !== strpos( $persisted_content, '"templateLock"' )
) {
	throw new RuntimeException( 'The clean welcome heading was not persisted correctly.' );
}

update_option(
	$migration_key,
	array(
		'version'          => 19,
		'page_id'          => $page_id,
		'previous_sha256'  => $current_hash,
		'content_sha256'   => hash( 'sha256', $persisted_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

clean_post_cache( $page_id );
