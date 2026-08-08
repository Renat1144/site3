<?php
/**
 * Synchronize the editable privacy policy with the real cookie controls.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key = 'turkey_signature_privacy_cookie_disclosure_v41';
$page          = get_page_by_path( 'privacy-policy', OBJECT, 'page' );

if ( ! $page instanceof WP_Post || 'publish' !== $page->post_status ) {
	throw new RuntimeException( 'The published privacy policy page could not be found.' );
}

$old_text = 'Сайт может использовать только технически необходимые cookie и локальные данные браузера для корректной работы интерфейса. Средства рекламного профилирования и аналитики не заявлены. Если такие средства будут подключены, Политика и механизм получения согласия будут обновлены до начала их использования.';
$new_text = 'Сайт использует технически необходимые cookie для корректной работы интерфейса и сохранения выбора посетителя. Cookie ts_cookie_consent хранит выбранный режим «разрешить все» или «только необходимые» в течение 12 месяцев, имеет атрибут SameSite=Lax и Secure при работе по HTTPS. До выбора и при режиме «только необходимые» дополнительные cookie не запускаются. Сейчас средства рекламного профилирования и аналитики не используются. Изменить выбор можно в любое время по ссылке «Настройки cookie» в подвале. До подключения новых сервисов перечень cookie, Политика и механизм согласия должны быть обновлены.';
$old_markup = '<!-- wp:paragraph --><p>' . $old_text . '</p><!-- /wp:paragraph -->';
$new_markup = '<!-- wp:paragraph --><p>' . $new_text . '</p><!-- /wp:paragraph -->';
$content    = $page->post_content;

if ( 1 === substr_count( $content, $old_markup ) ) {
	$target_content = str_replace( $old_markup, $new_markup, $content, $replacement_count );
	if ( 1 !== $replacement_count ) {
		throw new RuntimeException( 'The cookie disclosure was not replaced exactly once.' );
	}

	$result = wp_update_post(
		array(
			'ID'           => $page->ID,
			'post_content' => wp_slash( $target_content ),
		),
		true
	);
	if ( is_wp_error( $result ) ) {
		throw new RuntimeException( $result->get_error_message() );
	}
} elseif ( 1 === substr_count( $content, $new_markup ) ) {
	$target_content = $content;
} else {
	throw new RuntimeException( 'The privacy cookie paragraph was manually changed; migration v41 did not overwrite it.' );
}

clean_post_cache( $page->ID );
$persisted = get_post( $page->ID );
if ( ! $persisted instanceof WP_Post || ! hash_equals( hash( 'sha256', $target_content ), hash( 'sha256', $persisted->post_content ) ) ) {
	throw new RuntimeException( 'The updated privacy policy was not persisted exactly.' );
}

if (
	1 !== substr_count( $persisted->post_content, 'ts_cookie_consent' ) ||
	1 !== substr_count( $persisted->post_content, 'Настройки cookie' ) ||
	false !== strpos( $persisted->post_content, 'templateLock' ) ||
	false !== strpos( $persisted->post_content, '"lock"' )
) {
	throw new RuntimeException( 'The privacy policy cookie disclosure failed validation.' );
}

update_option(
	$migration_key,
	array(
		'version'        => 41,
		'page_id'        => (int) $persisted->ID,
		'content_sha256' => hash( 'sha256', $persisted->post_content ),
		'completed_at'   => gmdate( DATE_ATOM ),
	),
	false
);

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( 'Privacy policy cookie disclosure v41 is synchronized.' );
}
