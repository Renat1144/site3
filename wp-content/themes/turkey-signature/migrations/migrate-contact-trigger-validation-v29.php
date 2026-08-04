<?php
/**
 * Remove unsupported custom attributes from core Button markup.
 *
 * The contact script already recognises href="#contact-form", so the custom
 * data attribute is redundant and makes core/button fail Gutenberg validation.
 * Exact v28/v27 hashes prevent overwriting later manual edits.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key   = 'turkey_signature_contact_trigger_validation_v29';
$migration_state = get_option( $migration_key );
$v31_state       = get_option( 'turkey_signature_homepage_design_refinement_v31' );
$v30_state       = get_option( 'turkey_signature_homepage_primary_tour_content_v30' );

if ( is_array( $migration_state ) && is_array( $v31_state ) && ! empty( $migration_state['page_hashes'] ) ) {
	$v31_page_id = (int) ( $v31_state['page_id'] ?? 0 );
	$v31_page    = $v31_page_id ? get_post( $v31_page_id ) : null;
	$v31_hash    = (string) ( $v31_state['content_sha256'] ?? '' );
	$later_state_valid = $v31_page instanceof WP_Post && '' !== $v31_hash && hash_equals( $v31_hash, hash( 'sha256', $v31_page->post_content ) );

	foreach ( $migration_state['page_hashes'] as $page_id => $saved_hash ) {
		if ( (int) $page_id === $v31_page_id ) {
			continue;
		}
		$page = get_post( (int) $page_id );
		if ( ! $page instanceof WP_Post || ! hash_equals( (string) $saved_hash, hash( 'sha256', $page->post_content ) ) ) {
			$later_state_valid = false;
			break;
		}
	}

	if ( $later_state_valid ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( 'Contact-trigger validation migration v29 is superseded by v31.' );
		}
		return;
	}
}

if ( is_array( $migration_state ) && is_array( $v30_state ) && ! empty( $migration_state['page_hashes'] ) ) {
	$v30_page_id = (int) ( $v30_state['page_id'] ?? 0 );
	$v30_page    = $v30_page_id ? get_post( $v30_page_id ) : null;
	$v30_hash    = (string) ( $v30_state['content_sha256'] ?? '' );
	$later_state_valid = $v30_page instanceof WP_Post && '' !== $v30_hash && hash_equals( $v30_hash, hash( 'sha256', $v30_page->post_content ) );

	foreach ( $migration_state['page_hashes'] as $page_id => $saved_hash ) {
		if ( (int) $page_id === $v30_page_id ) {
			continue;
		}
		$page = get_post( (int) $page_id );
		if ( ! $page instanceof WP_Post || ! hash_equals( (string) $saved_hash, hash( 'sha256', $page->post_content ) ) ) {
			$later_state_valid = false;
			break;
		}
	}

	if ( $later_state_valid ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( 'Contact-trigger validation migration v29 is superseded by v30.' );
		}
		return;
	}
}

if ( is_array( $migration_state ) && ! empty( $migration_state['page_hashes'] ) ) {
	$state_valid = true;
	foreach ( $migration_state['page_hashes'] as $page_id => $saved_hash ) {
		$page = get_post( (int) $page_id );
		if ( ! $page instanceof WP_Post || ! hash_equals( (string) $saved_hash, hash( 'sha256', $page->post_content ) ) ) {
			$state_valid = false;
			break;
		}
	}
	if ( $state_valid ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( 'Contact-trigger validation migration v29 is already complete.' );
		}
		return;
	}
}

$v28_state = get_option( 'turkey_signature_homepage_primary_tour_v28' );
$v27_state = get_option( 'turkey_signature_homepage_conversion_tour_pages_v27' );

if ( ! is_array( $v28_state ) || ! is_array( $v27_state ) ) {
	throw new RuntimeException( 'The v28 homepage or v27 tour-page state is missing.' );
}

$expected_hashes = array(
	(int) $v28_state['page_id'] => (string) $v28_state['content_sha256'],
);

foreach ( (array) $v27_state['page_ids'] as $slug => $page_id ) {
	if ( empty( $v27_state['page_hashes'][ $slug ] ) ) {
		throw new RuntimeException( sprintf( 'The expected hash for tour page "%s" is missing.', $slug ) );
	}
	$expected_hashes[ (int) $page_id ] = (string) $v27_state['page_hashes'][ $slug ];
}

$updated_hashes = array();
$updated_count  = 0;

foreach ( $expected_hashes as $page_id => $expected_hash ) {
	$page = get_post( $page_id );
	if ( ! $page instanceof WP_Post || 'page' !== $page->post_type ) {
		throw new RuntimeException( sprintf( 'Expected page %d could not be found.', $page_id ) );
	}

	$current_content = $page->post_content;
	$current_hash    = hash( 'sha256', $current_content );
	$target_content  = str_replace( ' data-ts-contact-open', '', $current_content );

	if ( ! hash_equals( $expected_hash, $current_hash ) ) {
		throw new RuntimeException( sprintf( 'Page %d has later manual edits and was not overwritten.', $page_id ) );
	}

	if ( $target_content !== $current_content ) {
		if ( false === strpos( $target_content, 'href="#contact-form"' ) ) {
			throw new RuntimeException( sprintf( 'Page %d lost its contact-form link.', $page_id ) );
		}

		wp_save_post_revision( $page_id );
		$updated_id = wp_update_post(
			array(
				'ID'           => $page_id,
				'post_content' => wp_slash( $target_content ),
			),
			true
		);
		if ( is_wp_error( $updated_id ) ) {
			throw new RuntimeException( $updated_id->get_error_message() );
		}
		++$updated_count;
	}

	clean_post_cache( $page_id );
	$persisted = get_post( $page_id );
	if ( ! $persisted instanceof WP_Post || false !== strpos( $persisted->post_content, ' data-ts-contact-open' ) ) {
		throw new RuntimeException( sprintf( 'Page %d was not persisted without the unsupported attribute.', $page_id ) );
	}
	$updated_hashes[ $page_id ] = hash( 'sha256', $persisted->post_content );
}

update_option(
	$migration_key,
	array(
		'version'          => 29,
		'page_hashes'      => $updated_hashes,
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( sprintf( 'Validated contact triggers on %d pages; %d pages updated.', count( $updated_hashes ), $updated_count ) );
}
