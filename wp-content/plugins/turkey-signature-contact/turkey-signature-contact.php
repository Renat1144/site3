<?php
/**
 * Plugin Name: Turkey Signature Contact
 * Description: Editable premium contact form with local mail testing support.
 * Version: 1.3.0
 * Requires at least: 6.7
 * Requires PHP: 7.4
 * Author: Site owner
 * Text Domain: turkey-signature-contact
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TS_CONTACT_OPTION = 'turkey_signature_contact_recipient';

add_filter(
	'wp_mail_from',
	static function ( $from ) {
		$mail_host = getenv( 'TS_MAIL_HOST' );
		return false !== $mail_host && '' !== trim( $mail_host ) ? 'wordpress@site3.local' : $from;
	}
);

add_filter(
	'wp_mail_from_name',
	static function ( $name ) {
		$mail_host = getenv( 'TS_MAIL_HOST' );
		return false !== $mail_host && '' !== trim( $mail_host ) ? 'Авторские туры' : $name;
	}
);

add_action(
	'init',
	static function () {
		wp_register_script(
			'turkey-signature-contact-editor',
			plugins_url( 'assets/editor.js', __FILE__ ),
			array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n', 'wp-server-side-render' ),
			'1.3.0',
			true
		);

		register_block_type(
			__DIR__,
			array(
				'editor_script'   => 'turkey-signature-contact-editor',
				'render_callback' => 'ts_contact_render_block',
			)
		);
	}
);

/**
 * Render the shared form panel.
 *
 * @param array  $attributes Block attributes.
 * @param string $instance_id Unique HTML id suffix.
 * @param bool   $editor_preview Whether controls should be inert.
 * @return string
 */
function ts_contact_render_panel( array $attributes, string $instance_id, bool $editor_preview ): string {
	$success_id     = 'ts-contact-success-title-' . $instance_id;
	$disabled       = $editor_preview ? ' disabled' : '';
	$endpoint       = $editor_preview ? '' : admin_url( 'admin-ajax.php' );
	$required_mark  = '<span aria-hidden="true">*</span>';
	$success_title  = (string) $attributes['successTitle'];
	$success_text   = (string) $attributes['successText'];

	ob_start();
	?>
	<div class="ts-contact-modal__panel">
		<div class="ts-contact-modal__form-wrap">
			<form class="ts-contact-form" data-ts-contact-form action="<?php echo esc_url( $endpoint ); ?>" method="post" novalidate>
				<div class="ts-contact-form__heading">
					<h2><?php echo esc_html( $attributes['formTitle'] ); ?></h2>
					<p><?php echo esc_html( $attributes['formText'] ); ?></p>
				</div>
				<input type="hidden" name="action" value="ts_contact_submit">
				<input type="hidden" name="nonce" value="<?php echo esc_attr( $editor_preview ? '' : wp_create_nonce( 'ts_contact_submit' ) ); ?>">
				<input type="hidden" name="tour_context" value="">
				<div class="ts-contact-honeypot" aria-hidden="true"><label>Ваш сайт<input type="text" name="website" tabindex="-1" autocomplete="off"<?php echo $disabled; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>></label></div>
				<div class="ts-contact-form__grid">
					<label class="ts-contact-field"><span><?php echo esc_html( $attributes['nameLabel'] ); ?> <?php echo $required_mark; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><input type="text" name="name" autocomplete="name" maxlength="120" required<?php echo $disabled; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>></label>
					<label class="ts-contact-field"><span><?php echo esc_html( $attributes['phoneLabel'] ); ?> <?php echo $required_mark; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><input type="tel" name="phone" autocomplete="tel" inputmode="tel" maxlength="50" required<?php echo $disabled; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>></label>
					<label class="ts-contact-field ts-contact-field--full"><span><?php echo esc_html( $attributes['emailLabel'] ); ?></span><input type="email" name="email" autocomplete="email"<?php echo $disabled; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>></label>
				</div>
				<label class="ts-contact-form__consent"><input type="checkbox" name="consent" value="1" required<?php echo $disabled; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><span><?php echo esc_html( $attributes['consentText'] ); ?></span></label>
				<div class="ts-contact-form__footer">
					<p class="ts-contact-form__status" data-ts-contact-status role="status" aria-live="polite"></p>
					<button class="ts-contact-form__submit" type="submit"<?php echo $disabled; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $attributes['submitLabel'] ); ?> <span aria-hidden="true">↗</span></button>
				</div>
			</form>
			<div class="ts-contact-success" data-ts-contact-success hidden>
				<span class="ts-contact-success__mark" aria-hidden="true"><span>✓</span></span>
				<h3 id="<?php echo esc_attr( $success_id ); ?>"><?php echo esc_html( $success_title ); ?></h3>
				<p><?php echo esc_html( $success_text ); ?></p>
			</div>
		</div>
		<?php if ( ! $editor_preview ) : ?>
			<button class="ts-contact-modal__close" type="button" data-ts-contact-close aria-label="Закрыть форму">×</button>
		<?php endif; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render the launcher and exact server-side preview used by Gutenberg.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function ts_contact_render_block( array $attributes ): string {
	$instance_id   = wp_unique_id( 'form-' );
	$request_uri   = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$editor_preview = defined( 'REST_REQUEST' ) && REST_REQUEST && false !== strpos( $request_uri, '/block-renderer/' );
	$panel         = ts_contact_render_panel( $attributes, $instance_id, $editor_preview );

	if ( $editor_preview ) {
		return '<div class="ts-contact-modal ts-contact-modal--editor">' . $panel . '</div>';
	}

	ob_start();
	?>
	<!-- ts-contact-static-omit:start -->
	<div class="ts-contact-root" data-ts-contact-root>
		<div class="ts-contact-launcher">
			<div>
				<p class="ts-contact-launcher__meta">Личная консультация</p>
				<p class="ts-contact-launcher__lead">Оставьте номер телефона — поможем выбрать маршрут и ответим на вопросы без навязчивых звонков.</p>
			</div>
			<a class="ts-contact-launcher__button" href="#contact-form" data-ts-contact-open>Связаться с нами <span aria-hidden="true">↗</span></a>
		</div>
		<dialog class="ts-contact-modal" id="contact-form" data-ts-contact-dialog aria-label="Форма заявки">
			<?php echo $panel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</dialog>
	</div>
	<!-- ts-contact-static-omit:end -->
	<?php
	return (string) ob_get_clean();
}

add_action( 'wp_ajax_ts_contact_submit', 'ts_contact_handle_submit' );
add_action( 'wp_ajax_nopriv_ts_contact_submit', 'ts_contact_handle_submit' );

/** Handle a public contact form submission. */
function ts_contact_handle_submit(): void {
	if ( ! check_ajax_referer( 'ts_contact_submit', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => 'Сессия формы истекла. Обновите страницу и попробуйте снова.' ), 403 );
	}

	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => 'Заявка отправлена.' ) );
	}

	$recipient = sanitize_email( (string) get_option( TS_CONTACT_OPTION, '' ) );
	if ( ! is_email( $recipient ) ) {
		wp_send_json_error( array( 'message' => 'Форма временно недоступна. Пожалуйста, попробуйте позже.' ), 503 );
	}

	$name         = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$email        = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$phone        = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$tour_context = sanitize_text_field( wp_unslash( $_POST['tour_context'] ?? '' ) );
	$consent      = isset( $_POST['consent'] ) && '1' === (string) $_POST['consent'];

	if ( '' === $name || '' === $phone || ( '' !== $email && ! is_email( $email ) ) || ! $consent ) {
		wp_send_json_error( array( 'message' => 'Проверьте обязательные поля и согласие на обработку данных.' ), 422 );
	}

	if ( strlen( $name ) > 240 || strlen( $phone ) > 100 || strlen( $tour_context ) > 300 ) {
		wp_send_json_error( array( 'message' => 'Одно из полей содержит слишком длинный текст.' ), 422 );
	}

	$remote_address = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? 'unknown' ) );
	$rate_key       = 'ts_contact_rate_' . hash_hmac( 'sha256', $remote_address, wp_salt( 'nonce' ) );
	$attempts       = (int) get_transient( $rate_key );
	if ( $attempts >= 5 ) {
		wp_send_json_error( array( 'message' => 'Слишком много заявок. Пожалуйста, повторите попытку через несколько минут.' ), 429 );
	}
	set_transient( $rate_key, $attempts + 1, 10 * MINUTE_IN_SECONDS );

	$lines = array(
		'Новая заявка с сайта «Авторские туры»',
		'',
		'Имя: ' . $name,
		'E-mail: ' . ( '' !== $email ? $email : 'не указан' ),
		'Телефон: ' . $phone,
	);

	if ( '' !== $tour_context ) {
		$lines[] = 'Тур: ' . $tour_context;
	}

	$lines[] = '';
	$lines[] = 'Страница: ' . home_url( '/' );
	$lines[] = 'Время: ' . wp_date( 'd.m.Y H:i' );

	$headers = '' !== $email ? array( 'Reply-To: ' . $name . ' <' . $email . '>' ) : array();
	$sent    = wp_mail( $recipient, 'Новая заявка — Авторские туры', implode( "\n", $lines ), $headers );

	if ( ! $sent ) {
		wp_send_json_error( array( 'message' => 'Письмо не отправлено. Пожалуйста, попробуйте ещё раз.' ), 500 );
	}

	wp_send_json_success( array( 'message' => 'Заявка отправлена.' ) );
}

add_action(
	'phpmailer_init',
	static function ( $phpmailer ) {
		$mail_host = getenv( 'TS_MAIL_HOST' );
		$mail_port = (int) ( getenv( 'TS_MAIL_PORT' ) ?: 1025 );
		if ( false === $mail_host || '' === trim( $mail_host ) ) {
			return;
		}

		$phpmailer->isSMTP();
		$phpmailer->Host       = $mail_host;
		$phpmailer->Port       = $mail_port;
		$phpmailer->SMTPAuth   = false;
		$phpmailer->SMTPSecure = '';
		$phpmailer->SMTPAutoTLS = false;
		$phpmailer->setFrom( 'wordpress@site3.local', 'Авторские туры', false );
	}
);

add_action(
	'admin_init',
	static function () {
		register_setting(
			'ts_contact_settings',
			TS_CONTACT_OPTION,
			array(
				'type'              => 'string',
				'sanitize_callback' => static function ( $value ) {
					$value = sanitize_email( (string) $value );
					if ( '' !== $value && ! is_email( $value ) ) {
						add_settings_error( TS_CONTACT_OPTION, 'invalid_email', 'Укажите корректный адрес электронной почты.' );
						return (string) get_option( TS_CONTACT_OPTION, '' );
					}
					return $value;
				},
				'default'           => '',
			)
		);
	}
);

add_action(
	'admin_menu',
	static function () {
		add_options_page(
			'Форма связи',
			'Форма связи',
			'manage_options',
			'turkey-signature-contact',
			'ts_contact_render_settings_page'
		);
	}
);

/** Render the recipient settings screen. */
function ts_contact_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1>Форма связи</h1>
		<p>Укажите адрес, на который WordPress будет отправлять новые заявки. Настройки SMTP задаются отдельно на хостинге и не хранятся в теме.</p>
		<form action="options.php" method="post">
			<?php settings_fields( 'ts_contact_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="ts-contact-recipient">Получатель заявок</label></th>
					<td><input class="regular-text" id="ts-contact-recipient" name="<?php echo esc_attr( TS_CONTACT_OPTION ); ?>" type="email" value="<?php echo esc_attr( (string) get_option( TS_CONTACT_OPTION, '' ) ); ?>" autocomplete="email"><p class="description">Этот адрес хранится в локальной базе WordPress и не попадает в статический экспорт.</p></td>
				</tr>
			</table>
			<?php submit_button( 'Сохранить получателя' ); ?>
		</form>
	</div>
	<?php
}
