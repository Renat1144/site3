<?php
/**
 * Title: Cinematic Turkey landing page
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

  <!-- wp:group {"align":"full","className":"hero-cinematic","layout":{"type":"default"}} -->
  <div class="wp-block-group alignfull hero-cinematic">
    <!-- wp:group {"className":"hero-media","layout":{"type":"default"}} -->
    <div class="wp-block-group hero-media">
      <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"hero-slide is-active"} --><figure class="wp-block-image size-full hero-slide is-active"><img src="<?php echo esc_url( $images_uri . '/istanbul-golden-hour.png' ); ?>" alt="Стамбул и Босфор в золотой час" /></figure><!-- /wp:image -->
      <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"hero-slide"} --><figure class="wp-block-image size-full hero-slide"><img src="<?php echo esc_url( $images_uri . '/cappadocia-balloons.png' ); ?>" alt="Воздушные шары над Каппадокией" /></figure><!-- /wp:image -->
      <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"hero-slide"} --><figure class="wp-block-image size-full hero-slide"><img src="<?php echo esc_url( $images_uri . '/turquoise-yacht.png' ); ?>" alt="Яхта у бирюзового побережья Турции" /></figure><!-- /wp:image -->
    </div>
    <!-- /wp:group -->
    <!-- wp:group {"align":"wide","className":"hero-stage","layout":{"type":"constrained"}} -->
    <div class="wp-block-group alignwide hero-stage">
      <!-- wp:heading {"level":1,"fontSize":"hero","className":"hero-title"} --><h1 class="wp-block-heading hero-title has-hero-font-size">Турция,<br><em>которую</em><br>чувствуют.</h1><!-- /wp:heading -->
      <!-- wp:group {"className":"hero-bottom","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
      <div class="wp-block-group hero-bottom">
        <!-- wp:paragraph {"fontSize":"lg","className":"hero-lead"} --><p class="hero-lead has-lg-font-size">Не коллекция достопримечательностей, а путешествие с правильным ритмом: вода, город, вкус и места, которые раскрываются не сразу.</p><!-- /wp:paragraph -->
        <!-- wp:buttons {"className":"hero-actions"} -->
        <div class="wp-block-buttons hero-actions"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#dates">Посмотреть даты <span>↗</span></a></div><!-- /wp:button --><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#route">Почувствовать маршрут</a></div><!-- /wp:button --></div>
        <!-- /wp:buttons -->
      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","className":"journey-ribbon","layout":{"type":"flex","flexWrap":"nowrap"}} -->
  <div class="wp-block-group alignfull journey-ribbon"><!-- wp:paragraph --><p>Стамбул <span>✦</span> Босфор <span>✦</span> Каппадокия <span>✦</span> Эгейское море <span>✦</span> Ликийское побережье <span>✦</span> Стамбул <span>✦</span> Босфор <span>✦</span> Каппадокия <span>✦</span></p><!-- /wp:paragraph --></div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"impressions","className":"section-shell manifesto-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="impressions" class="wp-block-group alignfull section-shell manifesto-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"top","className":"section-heading-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-top section-heading-grid">
      <!-- wp:column {"verticalAlignment":"top"} --><div class="wp-block-column is-vertically-aligned-top"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Не посмотреть страну.<br><em>Прожить её.</em></h2><!-- /wp:heading --><!-- wp:paragraph {"fontSize":"lg","className":"section-intro"} --><p class="section-intro has-lg-font-size">Премиальность для нас — не показная роскошь, а внимание к ритму, деталям и вашему личному пространству.</p><!-- /wp:paragraph --></div><!-- /wp:column -->
    </div>
    <!-- /wp:columns -->

    <!-- wp:group {"align":"wide","className":"manifesto-collage","layout":{"type":"grid","columnCount":12,"minimumColumnWidth":null}} -->
    <div class="wp-block-group alignwide manifesto-collage">
      <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"manifesto-image manifesto-image-a"} --><figure class="wp-block-image size-full manifesto-image manifesto-image-a"><img src="<?php echo esc_url( $images_uri . '/istanbul-breakfast.jpg' ); ?>" alt="Турецкий завтрак на улице Стамбула" /></figure><!-- /wp:image -->
      <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"manifesto-image manifesto-image-b"} --><figure class="wp-block-image size-full manifesto-image manifesto-image-b"><img src="<?php echo esc_url( $images_uri . '/pamukkale-terraces.png' ); ?>" alt="Белые травертиновые террасы Памуккале" /></figure><!-- /wp:image -->
      <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"manifesto-image manifesto-image-c"} --><figure class="wp-block-image size-full manifesto-image manifesto-image-c"><img src="<?php echo esc_url( $images_uri . '/galata-stairs-editorial.jpg' ); ?>" alt="Редакционный вид лестниц и улиц Галаты" /></figure><!-- /wp:image -->
    </div>
    <!-- /wp:group -->

    <!-- wp:group {"align":"wide","className":"manifesto-promise","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
    <div class="wp-block-group alignwide manifesto-promise"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Шесть дней.<br><em>Пространство для впечатлений.</em></h3><!-- /wp:heading --><!-- wp:paragraph --><p>Достаточно событий, чтобы почувствовать место. Достаточно воздуха, чтобы путешествие осталось вашим.</p><!-- /wp:paragraph --></div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"route","className":"destination-section","layout":{"type":"default"}} -->
  <div id="route" class="wp-block-group alignfull destination-section">
    <!-- wp:group {"className":"destination-sticky","layout":{"type":"default"}} -->
    <div class="wp-block-group destination-sticky">
      <!-- wp:group {"align":"wide","className":"destination-heading","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
      <div class="wp-block-group alignwide destination-heading"><!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Одна страна.<br><em>Несколько состояний.</em></h2><!-- /wp:heading --></div><!-- /wp:group --><!-- wp:paragraph {"className":"destination-hint"} --><p class="destination-hint">Продолжайте прокрутку<br>для движения по маршруту →</p><!-- /wp:paragraph --></div>
      <!-- /wp:group -->
      <!-- wp:group {"className":"destination-viewport","layout":{"type":"default"}} -->
      <div class="wp-block-group destination-viewport">
        <!-- wp:group {"className":"destination-track","layout":{"type":"flex","flexWrap":"nowrap"}} -->
        <div class="wp-block-group destination-track">
          <!-- wp:group {"className":"destination-card","layout":{"type":"default"}} --><div class="wp-block-group destination-card"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="<?php echo esc_url( $images_uri . '/istanbul-golden-hour.png' ); ?>" alt="Панорама Стамбула с воды" /></figure><!-- /wp:image --><!-- wp:group {"className":"destination-card-copy","layout":{"type":"constrained"}} --><div class="wp-block-group destination-card-copy"><!-- wp:paragraph --><p>01 · Marmara</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Стамбул</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Город, где утро начинается на воде, а вечер — между двумя континентами.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:group -->
          <!-- wp:group {"className":"destination-card","layout":{"type":"default"}} --><div class="wp-block-group destination-card"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="<?php echo esc_url( $images_uri . '/cappadocia-balloons.png' ); ?>" alt="Каппадокия на рассвете" /></figure><!-- /wp:image --><!-- wp:group {"className":"destination-card-copy","layout":{"type":"constrained"}} --><div class="wp-block-group destination-card-copy"><!-- wp:paragraph --><p>02 · Anatolia</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Каппадокия</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Каменные долины, тишина рассвета и небо, которое становится частью маршрута.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:group -->
          <!-- wp:group {"className":"destination-card","layout":{"type":"default"}} --><div class="wp-block-group destination-card"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="<?php echo esc_url( $images_uri . '/kaputas-coast.png' ); ?>" alt="Бирюзовая бухта Капуташ" /></figure><!-- /wp:image --><!-- wp:group {"className":"destination-card-copy","layout":{"type":"constrained"}} --><div class="wp-block-group destination-card-copy"><!-- wp:paragraph --><p>03 · Mediterranean</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Ликийский берег</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Сосновые склоны, прозрачная вода и маленькие бухты без городского шума.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:group -->
          <!-- wp:group {"className":"destination-card","layout":{"type":"default"}} --><div class="wp-block-group destination-card"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="<?php echo esc_url( $images_uri . '/ephesus-library.png' ); ?>" alt="Античная библиотека в Эфесе" /></figure><!-- /wp:image --><!-- wp:group {"className":"destination-card-copy","layout":{"type":"constrained"}} --><div class="wp-block-group destination-card-copy"><!-- wp:paragraph --><p>04 · Aegean</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Эгейская античность</h3><!-- /wp:heading --><!-- wp:paragraph --><p>История без музейной дистанции — камень, свет и города, пережившие эпохи.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:group -->
        </div>
        <!-- /wp:group -->
      </div>
      <!-- /wp:group -->
      <!-- wp:group {"align":"wide","className":"destination-progress","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} --><div class="wp-block-group alignwide destination-progress"><!-- wp:paragraph --><p>01</p><!-- /wp:paragraph --><!-- wp:separator --><hr class="wp-block-separator has-alpha-channel-opacity"/><!-- /wp:separator --><!-- wp:paragraph --><p>04</p><!-- /wp:paragraph --></div><!-- /wp:group -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"program","className":"section-shell program-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="program" class="wp-block-group alignfull section-shell program-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"top","className":"program-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-top program-grid">
      <!-- wp:column {"verticalAlignment":"top","width":"46%","className":"program-visual"} --><div class="wp-block-column is-vertically-aligned-top program-visual" style="flex-basis:46%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="<?php echo esc_url( $images_uri . '/turquoise-yacht.png' ); ?>" alt="Яхта у побережья Турции" /></figure><!-- /wp:image --></div><!-- /wp:column -->
      <!-- wp:column {"verticalAlignment":"top","className":"program-copy"} -->
      <div class="wp-block-column is-vertically-aligned-top program-copy">
        <!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Шесть дней.<br><em>У каждого — свой вкус.</em></h2><!-- /wp:heading -->
        <!-- wp:paragraph {"className":"program-intro"} --><p class="program-intro">Первый маршрут посвящён Стамбулу. Он насыщенный, но не изматывающий: важные места, локальные адреса и время для собственного города.</p><!-- /wp:paragraph -->
        <!-- wp:group {"className":"program-list","layout":{"type":"constrained"}} -->
        <div class="wp-block-group program-list">
          <!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary><span>01</span> Босфор встречает</summary><!-- wp:paragraph --><p>Встречаемся в отеле, знакомимся и выходим к воде. Первый вечер — мягкий свет над Босфором и ужин, с которого начинается общая история.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
          <!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary><span>02</span> Город империй</summary><!-- wp:paragraph --><p>Айя-София, Голубая мечеть и скрытые детали исторического центра — раньше основной толпы и с паузой на кофе во внутреннем дворике.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
          <!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary><span>03</span> Вверх к Галате</summary><!-- wp:paragraph --><p>Каракёй, старые пассажи, лестницы Галаты и дегустации по пути. Вечером — свободное время для rooftop-бара, хаммама или прогулки.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
          <!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary><span>04</span> Между двух берегов</summary><!-- wp:paragraph --><p>Пересекаем Босфор на пароме, исследуем Кадыкёй и Моду, пробуем современную турецкую кухню и встречаем закат на азиатской стороне.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
          <!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary><span>05</span> Свой Стамбул</summary><!-- wp:paragraph --><p>День без обязательной программы. Поможем выбрать идею: Принцевы острова, дворцы Босфора, арт-кварталы или неспешный шопинг.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
          <!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary><span>06</span> До скорой встречи</summary><!-- wp:paragraph --><p>Завтракаем, обмениваемся фотографиями и впечатлениями. Трансфер в аэропорт или продолжение путешествия — подскажем варианты.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
        </div>
        <!-- /wp:group -->
      </div>
      <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","className":"section-shell moments-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div class="wp-block-group alignfull section-shell moments-section reveal-on-scroll">
    <!-- wp:group {"align":"wide","className":"moments-heading","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} --><div class="wp-block-group alignwide moments-heading"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">То, что остаётся<br><em>после путешествия.</em></h2><!-- /wp:heading --></div><!-- /wp:group -->
    <!-- wp:group {"align":"wide","className":"moments-grid","layout":{"type":"grid","columnCount":12,"minimumColumnWidth":null}} -->
    <div class="wp-block-group alignwide moments-grid">
      <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"moment moment-a"} --><figure class="wp-block-image size-full moment moment-a"><img src="<?php echo esc_url( $images_uri . '/galata-stairs-editorial.jpg' ); ?>" alt="Каменные лестницы Галаты" /></figure><!-- /wp:image -->
      <!-- wp:group {"className":"moment-quote","layout":{"type":"constrained"}} --><div class="wp-block-group moment-quote"><!-- wp:paragraph {"className":"quote-mark"} --><p class="quote-mark">“</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Лучшие впечатления не нужно догонять. Для них нужно оставить место.</h3><!-- /wp:heading --></div><!-- /wp:group -->
      <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"moment moment-b"} --><figure class="wp-block-image size-full moment moment-b"><img src="<?php echo esc_url( $images_uri . '/antalya-harbor.png' ); ?>" alt="Старый порт Антальи в вечернем свете" /></figure><!-- /wp:image -->
      <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"moment moment-c"} --><figure class="wp-block-image size-full moment moment-c"><img src="<?php echo esc_url( $images_uri . '/tea-by-bosphorus.jpg' ); ?>" alt="Чай и симит у Босфора" /></figure><!-- /wp:image -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"team","className":"section-shell team-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="team" class="wp-block-group alignfull section-shell team-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"bottom","className":"team-heading-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-bottom team-heading-grid">
      <!-- wp:column {"verticalAlignment":"bottom","width":"68%"} --><div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:68%"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Люди, которые<br><em>создают путешествие.</em></h2><!-- /wp:heading --></div><!-- /wp:column -->
      <!-- wp:column {"verticalAlignment":"bottom"} --><div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:paragraph {"className":"team-intro"} --><p class="team-intro">Здесь появятся организаторы, эксперты и сопровождающие маршрута. Фотографии и реальные данные можно заменить прямо в редакторе WordPress.</p><!-- /wp:paragraph --></div><!-- /wp:column -->
    </div>
    <!-- /wp:columns -->

    <!-- wp:group {"align":"wide","className":"team-grid","layout":{"type":"grid","columnCount":3,"minimumColumnWidth":null}} -->
    <div class="wp-block-group alignwide team-grid">
      <!-- wp:group {"className":"team-card","layout":{"type":"constrained"}} -->
      <div class="wp-block-group team-card">
        <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"team-portrait"} --><figure class="wp-block-image size-full team-portrait"><img src="<?php echo esc_url( $images_uri . '/team-placeholder.svg' ); ?>" alt="Место для фотографии первого участника команды" /></figure><!-- /wp:image -->
        <!-- wp:group {"className":"team-card-meta","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} --><div class="wp-block-group team-card-meta"><!-- wp:paragraph {"className":"team-number"} --><p class="team-number">01</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"team-role"} --><p class="team-role">Роль в команде</p><!-- /wp:paragraph --></div><!-- /wp:group -->
        <!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Имя участника</h3><!-- /wp:heading -->
        <!-- wp:paragraph {"className":"team-bio"} --><p class="team-bio">Короткое описание опыта, специализации и того, за какую часть путешествия отвечает этот человек.</p><!-- /wp:paragraph -->
        <!-- wp:paragraph {"className":"team-contact-placeholder"} --><p class="team-contact-placeholder">Ссылка / социальная сеть <span>↗</span></p><!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->

      <!-- wp:group {"className":"team-card","layout":{"type":"constrained"}} -->
      <div class="wp-block-group team-card">
        <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"team-portrait"} --><figure class="wp-block-image size-full team-portrait"><img src="<?php echo esc_url( $images_uri . '/team-placeholder.svg' ); ?>" alt="Место для фотографии второго участника команды" /></figure><!-- /wp:image -->
        <!-- wp:group {"className":"team-card-meta","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} --><div class="wp-block-group team-card-meta"><!-- wp:paragraph {"className":"team-number"} --><p class="team-number">02</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"team-role"} --><p class="team-role">Роль в команде</p><!-- /wp:paragraph --></div><!-- /wp:group -->
        <!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Имя участника</h3><!-- /wp:heading -->
        <!-- wp:paragraph {"className":"team-bio"} --><p class="team-bio">Короткое описание опыта, специализации и того, за какую часть путешествия отвечает этот человек.</p><!-- /wp:paragraph -->
        <!-- wp:paragraph {"className":"team-contact-placeholder"} --><p class="team-contact-placeholder">Ссылка / социальная сеть <span>↗</span></p><!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->

      <!-- wp:group {"className":"team-card","layout":{"type":"constrained"}} -->
      <div class="wp-block-group team-card">
        <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"team-portrait"} --><figure class="wp-block-image size-full team-portrait"><img src="<?php echo esc_url( $images_uri . '/team-placeholder.svg' ); ?>" alt="Место для фотографии третьего участника команды" /></figure><!-- /wp:image -->
        <!-- wp:group {"className":"team-card-meta","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} --><div class="wp-block-group team-card-meta"><!-- wp:paragraph {"className":"team-number"} --><p class="team-number">03</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"team-role"} --><p class="team-role">Роль в команде</p><!-- /wp:paragraph --></div><!-- /wp:group -->
        <!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Имя участника</h3><!-- /wp:heading -->
        <!-- wp:paragraph {"className":"team-bio"} --><p class="team-bio">Короткое описание опыта, специализации и того, за какую часть путешествия отвечает этот человек.</p><!-- /wp:paragraph -->
        <!-- wp:paragraph {"className":"team-contact-placeholder"} --><p class="team-contact-placeholder">Ссылка / социальная сеть <span>↗</span></p><!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"dates","className":"dates-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="dates" class="wp-block-group alignfull dates-section reveal-on-scroll">
    <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"dates-background"} --><figure class="wp-block-image size-full dates-background"><img src="<?php echo esc_url( $images_uri . '/nemrut-sunrise.png' ); ?>" alt="Рассвет на горе Немрут" /></figure><!-- /wp:image -->
    <!-- wp:columns {"align":"wide","verticalAlignment":"stretch","className":"dates-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-stretch dates-grid">
      <!-- wp:column {"verticalAlignment":"stretch","width":"48%","className":"dates-intro"} --><div class="wp-block-column is-vertically-aligned-stretch dates-intro" style="flex-basis:48%"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Ваш Стамбул<br><em>уже близко.</em></h2><!-- /wp:heading --><!-- wp:paragraph --><p>Точная стоимость появится после согласования отелей, перелёта и состава программы.</p><!-- /wp:paragraph --></div><!-- /wp:column -->
      <!-- wp:column {"verticalAlignment":"stretch","className":"offer-card"} -->
      <div class="wp-block-column is-vertically-aligned-stretch offer-card">
        <!-- wp:group {"className":"offer-top","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} --><div class="wp-block-group offer-top"><!-- wp:paragraph {"className":"offer-status"} --><p class="offer-status">Ближайший тур</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>6 дней / 5 ночей</p><!-- /wp:paragraph --></div><!-- /wp:group -->
        <!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Даты уточняются</h3><!-- /wp:heading -->
        <!-- wp:paragraph {"className":"offer-price"} --><p class="offer-price">от — ₽</p><!-- /wp:paragraph -->
        <!-- wp:separator --><hr class="wp-block-separator has-alpha-channel-opacity"/><!-- /wp:separator -->
        <!-- wp:paragraph {"className":"offer-included-title"} --><p class="offer-included-title">Планируем включить</p><!-- /wp:paragraph -->
        <!-- wp:list {"className":"offer-list"} --><ul class="wp-block-list offer-list"><li>проживание в центральном районе;</li><li>трансферы по программе;</li><li>экскурсии и локальные впечатления;</li><li>сопровождение тур-лидера.</li></ul><!-- /wp:list -->
        <!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"width":100} --><div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link wp-element-button" href="#contact">Получить программу первым <span>↗</span></a></div><!-- /wp:button --></div><!-- /wp:buttons -->
      </div>
      <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","className":"section-shell faq-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div class="wp-block-group alignfull section-shell faq-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"top","className":"faq-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-top faq-grid"><!-- wp:column {"verticalAlignment":"top","width":"42%"} --><div class="wp-block-column is-vertically-aligned-top" style="flex-basis:42%"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Перед тем<br><em>как решиться.</em></h2><!-- /wp:heading --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"top","className":"faq-list"} --><div class="wp-block-column is-vertically-aligned-top faq-list">
      <!-- wp:details --><details class="wp-block-details"><summary>Нужна ли виза?</summary><!-- wp:paragraph --><p>Для граждан России действует безвизовый режим. Перед публикацией сайта этот текст нужно проверить по актуальным официальным правилам.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details --><details class="wp-block-details"><summary>Входит ли перелёт?</summary><!-- wp:paragraph --><p>Сейчас этот пункт не определён. Здесь будет точное условие тура и помощь с подбором удобного рейса.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details --><details class="wp-block-details"><summary>Где мы будем жить?</summary><!-- wp:paragraph --><p>Название и описание отеля появятся после утверждения. Планируем удобную локацию с быстрым доступом к маршруту.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details --><details class="wp-block-details"><summary>Можно ли поехать одному?</summary><!-- wp:paragraph --><p>Да. Формат небольшой группы подходит самостоятельным путешественникам — знакомство начинается ещё в общем чате до поездки.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
    </div><!-- /wp:column --></div>
    <!-- /wp:columns -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"contact","className":"contact-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="contact" class="wp-block-group alignfull contact-section reveal-on-scroll">
    <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"contact-background"} --><figure class="wp-block-image size-full contact-background"><img src="<?php echo esc_url( $images_uri . '/istanbul-golden-hour.png' ); ?>" alt="Стамбул на закате" /></figure><!-- /wp:image -->
    <!-- wp:group {"align":"wide","className":"contact-inner","layout":{"type":"constrained"}} -->
    <div class="wp-block-group alignwide contact-inner"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Оставьте место<br><em>для нового.</em></h2><!-- /wp:heading --><!-- wp:paragraph {"fontSize":"lg"} --><p class="has-lg-font-size">Расскажем о программе, датах и стоимости, когда они будут утверждены.</p><!-- /wp:paragraph -->
      <!-- wp:group {"className":"contact-placeholder","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} --><div class="wp-block-group contact-placeholder"><!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:paragraph {"className":"placeholder-label"} --><p class="placeholder-label">Форма будет подключена</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Имя · телефон · удобный способ связи</p><!-- /wp:paragraph --></div><!-- /wp:group --><!-- wp:paragraph {"className":"placeholder-note"} --><p class="placeholder-note">Нужны получатель заявок и согласованный текст обработки персональных данных.</p><!-- /wp:paragraph --></div><!-- /wp:group -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

</main>
<!-- /wp:group -->
