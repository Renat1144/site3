<?php
/**
 * Create the editable privacy policy and personal-data consent pages.
 *
 * The migration is idempotent and never overwrites a page after its initial
 * managed creation, so later edits in Gutenberg remain authoritative.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$migration_key = 'turkey_signature_legal_pages_v40';
$version       = 40;
$operator_mail = 'Oksana.oksana.74@mail.ru';
$header_block  = '<!-- wp:template-part {"slug":"header","theme":"turkey-signature","tagName":"header","className":"site-header"} /-->';
$footer_block  = '<!-- wp:template-part {"slug":"footer","theme":"turkey-signature","tagName":"footer","className":"site-footer"} /-->';

$wrap_page = static function ( string $title, string $kicker, string $lead, string $article ) use ( $header_block, $footer_block ): string {
	$content  = $header_block . "\n";
	$content .= '<!-- wp:group {"tagName":"main","align":"full","anchor":"start","className":"legal-page","layout":{"type":"default"}} -->' . "\n";
	$content .= '<main id="start" class="wp-block-group alignfull legal-page">';
	$content .= '<!-- wp:group {"align":"full","className":"legal-page__hero","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull legal-page__hero">';
	$content .= '<!-- wp:group {"align":"wide","className":"legal-page__hero-inner","layout":{"type":"constrained"}} --><div class="wp-block-group alignwide legal-page__hero-inner">';
	$content .= '<!-- wp:paragraph {"className":"legal-page__kicker"} --><p class="legal-page__kicker">' . esc_html( $kicker ) . '</p><!-- /wp:paragraph -->';
	$content .= '<!-- wp:heading {"level":1,"className":"legal-page__title"} --><h1 class="wp-block-heading legal-page__title">' . esc_html( $title ) . '</h1><!-- /wp:heading -->';
	$content .= '<!-- wp:paragraph {"className":"legal-page__lead"} --><p class="legal-page__lead">' . esc_html( $lead ) . '</p><!-- /wp:paragraph -->';
	$content .= '<!-- wp:paragraph {"className":"legal-page__date"} --><p class="legal-page__date">Редакция от 8 августа 2026 года</p><!-- /wp:paragraph -->';
	$content .= '</div><!-- /wp:group --></div><!-- /wp:group -->';
	$content .= '<!-- wp:group {"align":"full","className":"legal-page__body","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull legal-page__body">';
	$content .= '<!-- wp:group {"align":"wide","className":"legal-page__article","layout":{"type":"constrained","contentSize":"920px"}} --><div class="wp-block-group alignwide legal-page__article">' . $article . '</div><!-- /wp:group -->';
	$content .= '<!-- wp:group {"align":"wide","className":"legal-page__contact","layout":{"type":"constrained","contentSize":"920px"}} --><div class="wp-block-group alignwide legal-page__contact">';
	$content .= '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Вопросы о персональных данных</h2><!-- /wp:heading -->';
	$content .= '<!-- wp:paragraph --><p>Для обращений, уточнения данных или отзыва согласия напишите на <a href="mailto:Oksana.oksana.74@mail.ru">Oksana.oksana.74@mail.ru</a>. В теме письма укажите «Персональные данные».</p><!-- /wp:paragraph -->';
	$content .= '</div><!-- /wp:group -->';
	$content .= '<!-- wp:turkey-signature/contact-form /-->';
	$content .= '</div><!-- /wp:group -->';
	$content .= '</main><!-- /wp:group -->' . "\n";
	$content .= $footer_block;
	return $content;
};

$policy_article = <<<'HTML'
<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">1. Общие положения</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Настоящая Политика определяет порядок обработки и защиты персональных данных посетителей сайта проекта «Авторские туры» (далее — Сайт) и применяется ко всем сведениям, которые могут быть получены через формы Сайта. Политика подготовлена с учётом Конституции Российской Федерации, Федерального закона от 27.07.2006 № 152-ФЗ «О персональных данных» и иных применимых требований законодательства Российской Федерации.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>Использование Сайта означает ознакомление с настоящей Политикой. Перед отправкой формы пользователь отдельно подтверждает согласие обязательной отметкой; без неё поля формы и отправка заявки недоступны.</p><!-- /wp:paragraph -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">2. Оператор и контакты</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Оператор персональных данных — владелец и администратор Сайта «Авторские туры». Контакт для обращений по вопросам обработки персональных данных: <a href="mailto:Oksana.oksana.74@mail.ru">Oksana.oksana.74@mail.ru</a>.</p><!-- /wp:paragraph -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">3. Какие данные обрабатываются</h2><!-- /wp:heading -->
<!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>имя;</li><!-- /wp:list-item --><!-- wp:list-item --><li>контактный телефон;</li><!-- /wp:list-item --><!-- wp:list-item --><li>адрес электронной почты — если пользователь указал его добровольно;</li><!-- /wp:list-item --><!-- wp:list-item --><li>выбранный тур и адрес страницы, с которой отправлена заявка;</li><!-- /wp:list-item --><!-- wp:list-item --><li>технические сведения, необходимые для безопасности и ограничения злоупотреблений, включая кратковременно используемый сетевой адрес, а также стандартные записи серверных журналов.</li><!-- /wp:list-item --></ul><!-- /wp:list -->
<!-- wp:paragraph --><p>Оператор не запрашивает специальные категории персональных данных, биометрические данные, паспортные данные и платёжные реквизиты через формы Сайта.</p><!-- /wp:paragraph -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">4. Цели и правовые основания обработки</h2><!-- /wp:heading -->
<!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>ответ на обращение и обратный звонок;</li><!-- /wp:list-item --><!-- wp:list-item --><li>подбор тура, уточнение программы, дат, стоимости и состава услуг;</li><!-- /wp:list-item --><!-- wp:list-item --><li>подготовка к заключению договора по инициативе пользователя;</li><!-- /wp:list-item --><!-- wp:list-item --><li>обеспечение работоспособности и безопасности формы, предотвращение автоматических и повторных отправок.</li><!-- /wp:list-item --></ul><!-- /wp:list -->
<!-- wp:paragraph --><p>Основаниями обработки являются согласие субъекта персональных данных, действия по запросу субъекта до заключения договора, исполнение договора и обязанности, установленные законом. Контакты из формы не используются для рекламных рассылок без отдельного согласия.</p><!-- /wp:paragraph -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">5. Принципы, способы и действия с данными</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Обработка ведётся законно, добросовестно и только в объёме, необходимом для заявленных целей. Она может выполняться автоматизированным способом и без средств автоматизации. Возможные действия: сбор, запись, систематизация, накопление, хранение, уточнение, извлечение, использование, предоставление доступа уполномоченным лицам, блокирование, удаление и уничтожение.</p><!-- /wp:paragraph -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">6. Передача и поручение обработки</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Данные могут быть доступны только Оператору и лицам, которые обеспечивают работу Сайта, хостинга и электронной почты, в объёме, необходимом для их функций и при соблюдении конфиденциальности. Передача третьим лицам для самостоятельного маркетинга не производится. Иная передача допускается с согласия пользователя либо когда она обязательна по закону.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>До подключения постоянного домена и промышленной инфраструктуры перечень поставщиков хостинга и почтовых сервисов подлежит уточнению. При использовании инфраструктуры за пределами Российской Федерации Оператор до начала трансграничной передачи выполняет требования статьи 12 Федерального закона № 152-ФЗ.</p><!-- /wp:paragraph -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">7. Сроки хранения и удаление</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Данные хранятся до достижения целей обработки, отзыва согласия или прекращения необходимости в общении, но, как правило, не более трёх лет с даты последнего взаимодействия, если более длительный срок не требуется законом или договором. После достижения цели либо получения отзыва данные удаляются или уничтожаются в срок до 30 дней, кроме случаев, когда закон допускает продолжение обработки.</p><!-- /wp:paragraph -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">8. Права пользователя</h2><!-- /wp:heading -->
<!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>получать сведения об обработке своих данных;</li><!-- /wp:list-item --><!-- wp:list-item --><li>требовать уточнения, блокирования или удаления неполных, устаревших, неточных либо незаконно полученных данных;</li><!-- /wp:list-item --><!-- wp:list-item --><li>отозвать согласие и возражать против обработки в предусмотренных законом случаях;</li><!-- /wp:list-item --><!-- wp:list-item --><li>обжаловать действия Оператора в Роскомнадзоре или суде.</li><!-- /wp:list-item --></ul><!-- /wp:list -->
<!-- wp:paragraph --><p>Запрос направляется с адреса, позволяющего идентифицировать заявителя, на e-mail Оператора. В запросе следует указать имя, способ связи, суть требования и сведения, позволяющие найти соответствующую заявку.</p><!-- /wp:paragraph -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">9. Файлы cookie и технические данные</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Сайт может использовать только технически необходимые cookie и локальные данные браузера для корректной работы интерфейса. Средства рекламного профилирования и аналитики не заявлены. Если такие средства будут подключены, Политика и механизм получения согласия будут обновлены до начала их использования.</p><!-- /wp:paragraph -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">10. Защита данных</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Оператор принимает разумные организационные и технические меры: ограничивает доступ, проверяет права, применяет защиту формы от автоматических отправок, использует обновляемое программное обеспечение и защищённые каналы связи там, где это применимо.</p><!-- /wp:paragraph -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">11. Изменение Политики</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Политика действует до замены новой редакцией. Актуальная версия публикуется на этой странице. Существенные изменения применяются к новым обращениям с даты публикации, если иное не предусмотрено законом.</p><!-- /wp:paragraph -->
HTML;

$consent_article = <<<'HTML'
<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">1. Содержание согласия</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Проставляя обязательную отметку в форме и нажимая кнопку отправки заявки, я свободно, своей волей и в своём интересе даю владельцу и администратору Сайта «Авторские туры» (далее — Оператор) конкретное, предметное, информированное, сознательное и однозначное согласие на обработку моих персональных данных на условиях настоящего документа.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>Контакт Оператора по вопросам персональных данных: <a href="mailto:Oksana.oksana.74@mail.ru">Oksana.oksana.74@mail.ru</a>.</p><!-- /wp:paragraph -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">2. Перечень данных</h2><!-- /wp:heading -->
<!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>имя;</li><!-- /wp:list-item --><!-- wp:list-item --><li>номер телефона;</li><!-- /wp:list-item --><!-- wp:list-item --><li>адрес электронной почты, если он указан;</li><!-- /wp:list-item --><!-- wp:list-item --><li>выбранный тур и страница отправки заявки;</li><!-- /wp:list-item --><!-- wp:list-item --><li>технические сведения, необходимые для безопасности формы и ограничения злоупотреблений.</li><!-- /wp:list-item --></ul><!-- /wp:list -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">3. Цели обработки</h2><!-- /wp:heading -->
<!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>связаться со мной по моей заявке;</li><!-- /wp:list-item --><!-- wp:list-item --><li>ответить на вопросы и подобрать подходящий тур;</li><!-- /wp:list-item --><!-- wp:list-item --><li>уточнить программу, даты, стоимость и иные условия;</li><!-- /wp:list-item --><!-- wp:list-item --><li>совершить действия по моему запросу до возможного заключения договора;</li><!-- /wp:list-item --><!-- wp:list-item --><li>обеспечить безопасность и работоспособность формы.</li><!-- /wp:list-item --></ul><!-- /wp:list -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">4. Действия и способ обработки</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Разрешаю сбор, запись, систематизацию, накопление, хранение, уточнение, извлечение, использование, предоставление доступа лицам, обеспечивающим работу Сайта, хостинга и электронной почты, блокирование, удаление и уничтожение данных. Обработка может быть автоматизированной, неавтоматизированной или смешанной, с передачей по внутренней сети и сети Интернет в пределах, необходимых для указанных целей.</p><!-- /wp:paragraph -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">5. Срок действия и отзыв</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Согласие действует до достижения целей обработки или его отзыва, но, как правило, не более трёх лет с даты последнего взаимодействия, если иной срок не требуется законом или договором. Я вправе в любое время отозвать согласие письмом на <a href="mailto:Oksana.oksana.74@mail.ru">Oksana.oksana.74@mail.ru</a> с темой «Отзыв согласия на обработку персональных данных».</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>После получения отзыва Оператор прекращает обработку и удаляет данные в срок до 30 дней, если их дальнейшее хранение не требуется или не допускается законодательством. Отзыв не влияет на законность обработки, выполненной до его получения.</p><!-- /wp:paragraph -->

<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">6. Подтверждения пользователя</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Я подтверждаю, что указанные сведения принадлежат мне, являются достоверными и предоставляются добровольно; я ознакомился(лась) с <a href="/privacy-policy/">Политикой конфиденциальности и обработки персональных данных</a>; понимаю цели, перечень данных, действия с ними, срок обработки и порядок отзыва согласия.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>Настоящее согласие не распространяется на рекламные и информационные рассылки: для них при необходимости запрашивается отдельное согласие.</p><!-- /wp:paragraph -->
HTML;

$definitions = array(
	'privacy-policy' => array(
		'title'   => 'Политика конфиденциальности и обработки персональных данных',
		'kicker'  => 'Документы · защита данных',
		'lead'    => 'Как мы получаем, используем, храним и защищаем сведения, которые вы добровольно оставляете на сайте.',
		'article' => $policy_article,
	),
	'personal-data-consent' => array(
		'title'   => 'Согласие на обработку персональных данных',
		'kicker'  => 'Документы · ваше согласие',
		'lead'    => 'Условия обработки данных, которые вы подтверждаете перед заполнением и отправкой формы заявки.',
		'article' => $consent_article,
	),
);

$page_ids = array();
foreach ( $definitions as $slug => $definition ) {
	$content  = $wrap_page( $definition['title'], $definition['kicker'], $definition['lead'], $definition['article'] );
	$existing = get_page_by_path( $slug, OBJECT, 'page' );

	if ( $existing instanceof WP_Post ) {
		$managed_version = (int) get_post_meta( $existing->ID, '_turkey_signature_legal_seed_version', true );
		if ( $version !== $managed_version ) {
			throw new RuntimeException( sprintf( 'The existing page "%s" is not managed by legal migration v40 and was not overwritten.', $slug ) );
		}
		$page_id = $existing->ID;
	} else {
		$page_id = wp_insert_post(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'post_title'     => $definition['title'],
				'post_name'      => $slug,
				'post_content'   => wp_slash( $content ),
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			),
			true
		);
		if ( is_wp_error( $page_id ) ) {
			throw new RuntimeException( $page_id->get_error_message() );
		}
		update_post_meta( $page_id, '_turkey_signature_legal_seed_version', $version );
		update_post_meta( $page_id, '_turkey_signature_legal_seed_sha256', hash( 'sha256', $content ) );
	}

	$page = get_post( $page_id );
	if ( ! $page instanceof WP_Post || 'publish' !== $page->post_status ) {
		throw new RuntimeException( sprintf( 'The legal page "%s" was not published.', $slug ) );
	}
	if ( false !== strpos( $page->post_content, 'templateLock' ) || false !== strpos( $page->post_content, '"lock"' ) ) {
		throw new RuntimeException( sprintf( 'The legal page "%s" contains an editing lock.', $slug ) );
	}
	$page_ids[ $slug ] = (int) $page_id;
}

update_option( 'wp_page_for_privacy_policy', $page_ids['privacy-policy'] );
update_option(
	$migration_key,
	array(
		'version'      => $version,
		'page_ids'     => $page_ids,
		'operator_mail'=> $operator_mail,
		'completed_at' => gmdate( DATE_ATOM ),
	),
	false
);

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( 'Editable legal pages v40 are published and the WordPress privacy page is configured.' );
}
