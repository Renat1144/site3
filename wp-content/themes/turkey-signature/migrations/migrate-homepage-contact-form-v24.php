<?php
/**
 * Replace the retired contact placeholder with the editable contact block.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key = 'turkey_signature_homepage_contact_form_v24';
$page_id       = (int) get_option( 'page_on_front' );
$home          = $page_id ? get_post( $page_id ) : null;

if ( ! $home instanceof WP_Post || 'page' !== $home->post_type ) {
	throw new RuntimeException( 'The published WordPress front page could not be found.' );
}

$old_block = <<<'HTML'
<!-- wp:group {"className":"contact-placeholder","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group contact-placeholder"><!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"placeholder-label"} -->
<p class="placeholder-label">Форма будет подключена</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Имя · телефон · удобный способ связи</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"placeholder-note"} -->
<p class="placeholder-note">Нужны получатель заявок и согласованный текст обработки персональных данных.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
HTML;

$new_block = '<!-- wp:turkey-signature/contact-form /-->';
$content   = $home->post_content;
$old_count = substr_count( $content, $old_block );
$new_count = substr_count( $content, $new_block );

$validate = static function ( string $candidate ) use ( $old_block, $new_block ) {
	if ( 0 !== substr_count( $candidate, $old_block ) || 1 !== substr_count( $candidate, $new_block ) ) {
		throw new RuntimeException( 'The homepage contact form marker is invalid.' );
	}

	if ( false !== strpos( $candidate, '"className":"contact-placeholder"' ) || false !== strpos( $candidate, 'class="wp-block-group contact-placeholder"' ) ) {
		throw new RuntimeException( 'The retired contact placeholder remains in the homepage.' );
	}

	if ( false !== strpos( $candidate, '"lock"' ) || false !== strpos( $candidate, '"templateLock"' ) ) {
		throw new RuntimeException( 'The homepage unexpectedly contains editing locks.' );
	}
};

if ( 0 === $old_count && 1 === $new_count ) {
	$validate( $content );
	update_option(
		$migration_key,
		array(
			'version'          => 24,
			'page_id'          => $page_id,
			'content_sha256'   => hash( 'sha256', $content ),
			'completed_at_gmt' => gmdate( 'c' ),
		),
		false
	);
	return;
}

if ( 1 !== $old_count || 0 !== $new_count ) {
	throw new RuntimeException( 'The retired contact placeholder could not be isolated safely.' );
}

$target = str_replace( $old_block, $new_block, $content, $replacement_count );
if ( 1 !== $replacement_count ) {
	throw new RuntimeException( 'The contact block replacement count was not exactly one.' );
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
	throw new RuntimeException( 'The homepage could not be read after migration v24.' );
}

$validate( $persisted->post_content );
update_option(
	$migration_key,
	array(
		'version'          => 24,
		'page_id'          => $page_id,
		'previous_sha256'  => hash( 'sha256', $content ),
		'content_sha256'   => hash( 'sha256', $persisted->post_content ),
		'completed_at_gmt' => gmdate( 'c' ),
	),
	false
);
