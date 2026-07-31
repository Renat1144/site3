<?php
/**
 * Title: Premium Turkey landing page
 * Slug: turkey-signature/turkey-landing
 * Categories: turkey-signature, featured
 * Inserter: no
 *
 * @package TurkeySignature
 */

$images_uri = get_theme_file_uri( 'assets/images' );
?>
<!-- wp:group {"tagName":"main","align":"full","anchor":"start","className":"turkey-site","layout":{"type":"default"}} -->
<main id="start" class="wp-block-group alignfull turkey-site">

  <!-- wp:group {"align":"full","className":"hero-section","layout":{"type":"constrained"}} -->
  <div class="wp-block-group alignfull hero-section">
    <!-- wp:columns {"align":"wide","verticalAlignment":"stretch","className":"hero-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-stretch hero-grid">
      <!-- wp:column {"verticalAlignment":"stretch","width":"50%","className":"hero-copy"} -->
      <div class="wp-block-column is-vertically-aligned-stretch hero-copy" style="flex-basis:50%">
        <!-- wp:paragraph {"className":"eyebrow"} --><p class="eyebrow">Авторское путешествие · Стамбул</p><!-- /wp:paragraph -->
        <!-- wp:heading {"level":1,"fontSize":"hero","className":"hero-title"} --><h1 class="wp-block-heading hero-title has-hero-font-size">Турция,<br><em>которую</em><br>чувствуют.</h1><!-- /wp:heading -->
        <!-- wp:paragraph {"fontSize":"lg","className":"hero-lead"} --><p class="hero-lead has-lg-font-size">Шесть дней между древним городом и Босфором — с красивым ритмом, локальной кухней и местами, которые не находят случайно.</p><!-- /wp:paragraph -->
        <!-- wp:buttons {"className":"hero-actions"} -->
        <div class="wp-block-buttons hero-actions"><!-- wp:button {"className":"is-style-fill"} --><div class="wp-block-button is-style-fill"><a class="wp-block-button__link wp-element-button" href="#dates">Посмотреть даты →</a></div><!-- /wp:button --><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#program">Изучить маршрут</a></div><!-- /wp:button --></div>
        <!-- /wp:buttons -->
        <!-- wp:group {"className":"hero-facts","layout":{"type":"grid","columnCount":3,"minimumColumnWidth":null}} -->
        <div class="wp-block-group hero-facts">
          <!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:paragraph {"className":"fact-value"} --><p class="fact-value">6</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"fact-label"} --><p class="fact-label">дней без спешки</p><!-- /wp:paragraph --></div><!-- /wp:group -->
          <!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:paragraph {"className":"fact-value"} --><p class="fact-value">12</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"fact-label"} --><p class="fact-label">гостей максимум</p><!-- /wp:paragraph --></div><!-- /wp:group -->
          <!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:paragraph {"className":"fact-value"} --><p class="fact-value">1</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"fact-label"} --><p class="fact-label">город — сотни историй</p><!-- /wp:paragraph --></div><!-- /wp:group -->
        </div>
        <!-- /wp:group -->
      </div>
      <!-- /wp:column -->

      <!-- wp:column {"verticalAlignment":"stretch","width":"50%","className":"hero-visual"} -->
      <div class="wp-block-column is-vertically-aligned-stretch hero-visual" style="flex-basis:50%">
        <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"hero-image hero-image-main"} -->
        <figure class="wp-block-image size-full hero-image hero-image-main"><img src="<?php echo esc_url( $images_uri . '/bosphorus-yacht.jpg' ); ?>" alt="Яхта на Босфоре" /></figure>
        <!-- /wp:image -->
        <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"hero-image hero-image-detail"} -->
        <figure class="wp-block-image size-full hero-image hero-image-detail"><img src="<?php echo esc_url( $images_uri . '/hagia-sophia.jpg' ); ?>" alt="Айя-София в Стамбуле" /></figure>
        <!-- /wp:image -->
        <!-- wp:group {"className":"hero-seal","layout":{"type":"constrained"}} -->
        <div class="wp-block-group hero-seal"><!-- wp:paragraph --><p>IST<br><strong>41°01′</strong><br>BOSPHORUS</p><!-- /wp:paragraph --></div>
        <!-- /wp:group -->
        <!-- wp:paragraph {"className":"hero-photo-note"} --><p class="hero-photo-note">между Европой<br>и Азией</p><!-- /wp:paragraph -->
      </div>
      <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","className":"experience-strip","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
  <div class="wp-block-group alignfull experience-strip"><!-- wp:paragraph --><p>Босфор на закате</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Старый город до толпы</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Кухня по локальным адресам</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Два континента</p><!-- /wp:paragraph --></div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"impressions","className":"section-shell impressions-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="impressions" class="wp-block-group alignfull section-shell impressions-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"top","className":"section-heading-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-top section-heading-grid">
      <!-- wp:column {"verticalAlignment":"top","width":"24%"} --><div class="wp-block-column is-vertically-aligned-top" style="flex-basis:24%"><!-- wp:paragraph {"className":"section-index"} --><p class="section-index">01 · Впечатления</p><!-- /wp:paragraph --></div><!-- /wp:column -->
      <!-- wp:column {"verticalAlignment":"top"} --><div class="wp-block-column is-vertically-aligned-top"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Не экскурсия по списку.<br><em>Погружение в город.</em></h2><!-- /wp:heading --><!-- wp:paragraph {"fontSize":"lg","className":"section-intro"} --><p class="section-intro has-lg-font-size">Стамбул раскрывается в деталях: запахе каштанов, звоне паромов, прохладных каменных арках и вечернем свете над водой.</p><!-- /wp:paragraph --></div><!-- /wp:column -->
    </div>
    <!-- /wp:columns -->

    <!-- wp:columns {"align":"wide","className":"experience-cards"} -->
    <div class="wp-block-columns alignwide experience-cards">
      <!-- wp:column {"className":"experience-card experience-card-image"} --><div class="wp-block-column experience-card experience-card-image"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="<?php echo esc_url( $images_uri . '/galata-stairs.jpg' ); ?>" alt="Улицы района Галата" /></figure><!-- /wp:image --><!-- wp:paragraph {"className":"card-kicker"} --><p class="card-kicker">Город</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Сворачиваем с очевидного маршрута</h3><!-- /wp:heading --></div><!-- /wp:column -->
      <!-- wp:column {"className":"experience-card experience-card-color"} --><div class="wp-block-column experience-card experience-card-color"><!-- wp:paragraph {"className":"card-glyph"} --><p class="card-glyph">↗</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"card-kicker"} --><p class="card-kicker">Ритм</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Увидеть больше, не превращая день в марафон</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Утро начинаем раньше туристических групп, а после насыщенной части оставляем время для своего Стамбула.</p><!-- /wp:paragraph --></div><!-- /wp:column -->
      <!-- wp:column {"className":"experience-card experience-card-image"} --><div class="wp-block-column experience-card experience-card-image"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="<?php echo esc_url( $images_uri . '/turkish-tea.jpg' ); ?>" alt="Турецкий чай" /></figure><!-- /wp:image --><!-- wp:paragraph {"className":"card-kicker"} --><p class="card-kicker">Вкус</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Пробовать Турцию, а не туристическое меню</h3><!-- /wp:heading --></div><!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"route","className":"section-shell route-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="route" class="wp-block-group alignfull section-shell route-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"route-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-center route-grid">
      <!-- wp:column {"verticalAlignment":"center","width":"44%"} -->
      <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:44%">
        <!-- wp:paragraph {"className":"section-index section-index-light"} --><p class="section-index section-index-light">02 · Маршрут</p><!-- /wp:paragraph -->
        <!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Стамбул<br><em>вдоль и поперёк.</em></h2><!-- /wp:heading -->
        <!-- wp:paragraph {"className":"route-copy"} --><p class="route-copy">Исторический полуостров, Галата, Босфор, азиатский берег и один свободный день для личного открытия.</p><!-- /wp:paragraph -->
        <!-- wp:group {"className":"route-list","layout":{"type":"constrained"}} -->
        <div class="wp-block-group route-list">
          <!-- wp:paragraph --><p><span>01</span> Султанахмет и Айя-София</p><!-- /wp:paragraph -->
          <!-- wp:paragraph --><p><span>02</span> Галата и Каракёй</p><!-- /wp:paragraph -->
          <!-- wp:paragraph --><p><span>03</span> Босфор</p><!-- /wp:paragraph -->
          <!-- wp:paragraph --><p><span>04</span> Кадыкёй и Мода</p><!-- /wp:paragraph -->
        </div>
        <!-- /wp:group -->
      </div>
      <!-- /wp:column -->
      <!-- wp:column {"verticalAlignment":"center","className":"route-visual"} -->
      <div class="wp-block-column is-vertically-aligned-center route-visual"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="<?php echo esc_url( $images_uri . '/bosphorus-ferry.jpg' ); ?>" alt="Паром на Босфоре" /></figure><!-- /wp:image --><!-- wp:paragraph {"className":"route-curve"} --><p class="route-curve">Европа<br>〰〰〰<br>Азия</p><!-- /wp:paragraph --></div>
      <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"program","className":"section-shell program-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="program" class="wp-block-group alignfull section-shell program-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"bottom","className":"section-heading-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-bottom section-heading-grid"><!-- wp:column {"verticalAlignment":"bottom","width":"24%"} --><div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:24%"><!-- wp:paragraph {"className":"section-index"} --><p class="section-index">03 · Программа</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"bottom"} --><div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Шесть дней.<br><em>У каждого — свой вкус.</em></h2><!-- /wp:heading --></div><!-- /wp:column --></div>
    <!-- /wp:columns -->

    <!-- wp:group {"align":"wide","className":"program-list","layout":{"type":"constrained"}} -->
    <div class="wp-block-group alignwide program-list">
      <!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary><span>День 1</span> Босфор встречает</summary><!-- wp:paragraph --><p>Встречаемся в отеле, знакомимся и выходим к воде. Первый вечер — прогулка, мягкий свет над Босфором и ужин, на котором начинается наша общая история.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary><span>День 2</span> Город империй</summary><!-- wp:paragraph --><p>Айя-София, Голубая мечеть, ипподром и скрытые детали исторического центра. Выходим раньше основной толпы и оставляем время на кофе во внутреннем дворике.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary><span>День 3</span> Вверх к Галате</summary><!-- wp:paragraph --><p>Каракёй, старые пассажи, лестницы Галаты и дегустации по пути. Вечером — свободное время для rooftop-бара, хаммама или прогулки.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary><span>День 4</span> Между двух берегов</summary><!-- wp:paragraph --><p>Пересекаем Босфор на пароме, исследуем Кадыкёй и Моду, пробуем современную турецкую кухню и встречаем закат на азиатской стороне.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary><span>День 5</span> Свой Стамбул</summary><!-- wp:paragraph --><p>День без обязательной программы. Тур-лидер поможет выбрать идею: Принцевы острова, дворцы Босфора, арт-кварталы или неспешный шопинг.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary><span>День 6</span> До скорой встречи</summary><!-- wp:paragraph --><p>Завтракаем, обмениваемся фотографиями и впечатлениями. Трансфер в аэропорт или продолжение путешествия — подскажем варианты.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","className":"gallery-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div class="wp-block-group alignfull gallery-section reveal-on-scroll">
    <!-- wp:group {"align":"wide","className":"gallery-grid","layout":{"type":"grid","columnCount":3,"minimumColumnWidth":null}} -->
    <div class="wp-block-group alignwide gallery-grid">
      <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"gallery-tall"} --><figure class="wp-block-image size-full gallery-tall"><img src="<?php echo esc_url( $images_uri . '/stone-arcade.jpg' ); ?>" alt="Каменные арки старого Стамбула" /></figure><!-- /wp:image -->
      <!-- wp:group {"className":"gallery-quote","layout":{"type":"constrained"}} --><div class="wp-block-group gallery-quote"><!-- wp:paragraph {"className":"quote-mark"} --><p class="quote-mark">“</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Впечатления не нужно догонять. Для них нужно оставить место.</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Поэтому маршрут насыщенный, но не изматывающий.</p><!-- /wp:paragraph --></div><!-- /wp:group -->
      <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"gallery-wide"} --><figure class="wp-block-image size-full gallery-wide"><img src="<?php echo esc_url( $images_uri . '/roasted-chestnuts.jpg' ); ?>" alt="Жареные каштаны на улице Стамбула" /></figure><!-- /wp:image -->
      <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"gallery-sea"} --><figure class="wp-block-image size-full gallery-sea"><img src="<?php echo esc_url( $images_uri . '/bosphorus-cruise.jpg' ); ?>" alt="Прогулка по Босфору" /></figure><!-- /wp:image -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"dates","className":"section-shell dates-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="dates" class="wp-block-group alignfull section-shell dates-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"stretch","className":"dates-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-stretch dates-grid">
      <!-- wp:column {"verticalAlignment":"stretch","width":"43%","className":"dates-intro"} --><div class="wp-block-column is-vertically-aligned-stretch dates-intro" style="flex-basis:43%"><!-- wp:paragraph {"className":"section-index section-index-light"} --><p class="section-index section-index-light">04 · Даты и цена</p><!-- /wp:paragraph --><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Ваш Стамбул<br><em>уже близко.</em></h2><!-- /wp:heading --><!-- wp:paragraph --><p>Точная стоимость появится после согласования отелей, перелёта и состава программы.</p><!-- /wp:paragraph --></div><!-- /wp:column -->
      <!-- wp:column {"verticalAlignment":"stretch","className":"offer-card"} -->
      <div class="wp-block-column is-vertically-aligned-stretch offer-card">
        <!-- wp:group {"className":"offer-top","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} --><div class="wp-block-group offer-top"><!-- wp:paragraph {"className":"offer-status"} --><p class="offer-status">Ближайший тур</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>6 дней / 5 ночей</p><!-- /wp:paragraph --></div><!-- /wp:group -->
        <!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Даты уточняются</h3><!-- /wp:heading -->
        <!-- wp:paragraph {"className":"offer-price"} --><p class="offer-price">от — ₽</p><!-- /wp:paragraph -->
        <!-- wp:separator --><hr class="wp-block-separator has-alpha-channel-opacity"/><!-- /wp:separator -->
        <!-- wp:paragraph {"className":"offer-included-title"} --><p class="offer-included-title">В стоимость планируем включить</p><!-- /wp:paragraph -->
        <!-- wp:list {"className":"offer-list"} --><ul class="wp-block-list offer-list"><li>проживание в центральном районе;</li><li>трансферы по программе;</li><li>экскурсии и локальные впечатления;</li><li>сопровождение тур-лидера.</li></ul><!-- /wp:list -->
        <!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"width":100} --><div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link wp-element-button" href="#contact">Получить программу первым →</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
      </div>
      <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","className":"section-shell faq-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div class="wp-block-group alignfull section-shell faq-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"top","className":"faq-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-top faq-grid"><!-- wp:column {"verticalAlignment":"top","width":"40%"} --><div class="wp-block-column is-vertically-aligned-top" style="flex-basis:40%"><!-- wp:paragraph {"className":"section-index"} --><p class="section-index">05 · Вопросы</p><!-- /wp:paragraph --><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Перед тем<br><em>как решиться.</em></h2><!-- /wp:heading --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"top","className":"faq-list"} --><div class="wp-block-column is-vertically-aligned-top faq-list">
      <!-- wp:details --><details class="wp-block-details"><summary>Нужна ли виза?</summary><!-- wp:paragraph --><p>Для граждан России действует безвизовый режим. Перед публикацией сайта этот текст нужно проверить по актуальным официальным правилам.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details --><details class="wp-block-details"><summary>Входит ли перелёт?</summary><!-- wp:paragraph --><p>Сейчас этот пункт не определён. Здесь будет точное условие тура и помощь с подбором удобного рейса.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details --><details class="wp-block-details"><summary>Где мы будем жить?</summary><!-- wp:paragraph --><p>Здесь появится название и описание отеля после его утверждения. Планируем удобную локацию с быстрым доступом к маршруту.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details --><details class="wp-block-details"><summary>Можно ли поехать одному?</summary><!-- wp:paragraph --><p>Да. Формат небольшой группы подходит для самостоятельных путешественников — знакомство начинается ещё в общем чате до поездки.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
    </div><!-- /wp:column --></div>
    <!-- /wp:columns -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"contact","className":"section-shell contact-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="contact" class="wp-block-group alignfull section-shell contact-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"contact-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-center contact-grid">
      <!-- wp:column {"verticalAlignment":"center"} --><div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"className":"section-index"} --><p class="section-index">Обсудить путешествие</p><!-- /wp:paragraph --><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Оставьте место<br><em>для нового.</em></h2><!-- /wp:heading --><!-- wp:paragraph {"fontSize":"lg"} --><p class="has-lg-font-size">Расскажем о программе, датах и стоимости, когда они будут утверждены.</p><!-- /wp:paragraph --></div><!-- /wp:column -->
      <!-- wp:column {"verticalAlignment":"center","className":"contact-placeholder"} --><div class="wp-block-column is-vertically-aligned-center contact-placeholder"><!-- wp:paragraph {"className":"placeholder-label"} --><p class="placeholder-label">Здесь будет форма заявки</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Имя · телефон · удобный способ связи</p><!-- /wp:paragraph --><!-- wp:separator --><hr class="wp-block-separator has-alpha-channel-opacity"/><!-- /wp:separator --><!-- wp:paragraph {"className":"placeholder-note"} --><p class="placeholder-note">Форма будет подключена после выбора получателя заявок и подготовки политики обработки персональных данных.</p><!-- /wp:paragraph --></div><!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
  </div>
  <!-- /wp:group -->

</main>
<!-- /wp:group -->

