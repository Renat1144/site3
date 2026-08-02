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
$tours      = array(
	array(
		'image'      => 'meso.png',
		'class'      => 'tour-card-mesopotamia',
		'alt'        => 'Каменный город Месопотамии на востоке Турции',
		'title'      => 'Месопотамия',
		'kicker'     => 'Тематический тур · Восток Турции',
		'lead'       => 'Турция напоминает шкатулку, в которую, если заглянуть, можно найти всё. Приглашаю вас в тематический тур на восток Турции. Восток звучит не только как ориентир: здесь действительно больше арабских ноток, несмотря на то, что в этом крае сохранилось много храмов, монастырей и крепостей византийских времён.',
		'paragraphs' => array(
			'Месопотамия — древнее место, будто спустившееся со страниц книги восточных сказок. Здесь находятся города-музеи под открытым небом. Несмотря на весьма бурную и богатую событиями историю, им удалось пронести сквозь века свой неповторимый архитектурный облик и уникальное многообразие различных культур, языков и религий.',
			'Здесь сохранились старинные особняки, крепости, медресе, мечети и минареты, в антураже которых с удовольствием проводят съёмки целого ряда фильмов.',
			'Кроме того, восточная кухня Турции славится своей уникальностью, и мы будем познавать её тайны, а также пробовать гастрономические изыски.',
		),
	),
	array(
		'image'      => 'karadeis.png',
		'class'      => 'tour-card-karadeiz',
		'alt'        => 'Чайные плантации, горное озеро и облака Карадениза',
		'title'      => 'Карадеиз',
		'kicker'     => 'Природный тур · Черноморье',
		'lead'       => 'Путешествие на Карадениз поможет вам отключиться от суеты больших городов и курортов, а заодно откроет страну с необычного ракурса.',
		'paragraphs' => array(
			'Мы с вами отправимся на родину турецкого чая, уникальной черноморской кухни, национальных заповедников, объятых облаками гор, густых девственных самшитовых лесов, кристально чистых горных озёр, водопадов и бесконечных полей.',
			'Грандиозные каньоны, альпийские пейзажи и горные озёра, скальные монастыри и памятники османской эпохи, аутентичные крепости и деревушки — черноморское побережье Турции способно удивить даже искушённых путешественников.',
		),
	),
	array(
		'image'      => 'skazka.png',
		'class'      => 'tour-card-fairytale',
		'alt'        => 'Турецкий кофе, восточная лавка и яхта на Босфоре',
		'title'      => 'Восточная сказка',
		'kicker'     => 'Атмосферный тур · Стамбул',
		'lead'       => 'Погрузитесь в атмосферу восточной сказки, неспешной суеты и максимального комфорта. Наш тур — это погружение в совершенно другой мир, притягивающий своим неповторимым колоритом.',
		'paragraphs' => array(
			'Вы окунётесь в культуру и историю Османской империи, увидите средневековые мечети и минареты, заглянете в мастерские и антикварные лавки, научитесь готовить традиционный турецкий кофе.',
			'Здесь воздух пропитан запахом кофе и специй, дворцы и мечети помнят султанов и женщин гаремов, а каждый район города скрывает интересные истории.',
			'Вас ждут релакс на термальных водах и эксклюзивная прогулка на частной яхте класса люкс.',
		),
		'facts'      => array(
			array( 'value' => '5/5', 'label' => 'Комфорт · уникальное жильё' ),
			array( 'value' => '3/5', 'label' => 'Активность · средняя' ),
			array( 'value' => 'До 8', 'label' => 'участников' ),
			array( 'value' => '5 дней', 'label' => 'продолжительность' ),
			array( 'value' => 'Русский', 'label' => 'язык тура' ),
		),
		'program'    => array(
			array(
				'title'      => 'День 1 · Встреча со Стамбулом',
				'paragraphs' => array(
					'Комфортабельный трансфер встретит вас в аэропорту и отвезёт в отель. Поскольку участники прилетают из разных городов, рекомендую прибыть в Стамбул не позднее 15:00, чтобы не терять первый день.',
					'После заселения и небольшого отдыха отправимся на неспешную прогулку-знакомство с городом.',
					'Вечером нас ждёт прогулка по Босфору на комфортабельной частной яхте. В качестве комплимента предложат бокал шампанского и лёгкие закуски. Первый закат в Стамбуле встретим очень романтично, а затем увидим город со стороны пролива — уже в ярких ночных огнях.',
					'Завершить вечер предложу ужином в хорошем кафе с панорамным видом.',
				),
			),
			array(
				'title'      => 'День 2 · Нулевой километр и история города',
				'paragraphs' => array(
					'Этот день посвятим истории многовекового города. Отправимся в сердце исторического Стамбула — к месту, откуда в византийскую эпоху отсчитывали расстояния и где Константин Великий создавал новую столицу империи.',
					'Увидим собор Святой Софии — грандиозный памятник Византии и один из главных образцов мировой архитектуры; Голубую мечеть с шестью минаретами; дворец Топкапы — многовековую резиденцию османских султанов; и Цистерну Базилику — впечатляющее подземное водохранилище Константинополя.',
					'После обеда нас ждёт фотосессия с чайками. По желанию можно выбрать платья и костюмы в османском стиле. Затем посмотрим танец кружащихся дервишей.',
					'Вечером предложу ужин в традиционном турецком ресторане, где национальные блюда готовят по собственным рецептам и подают с красивым панорамным видом.',
				),
			),
			array(
				'title'      => 'День 3 · Ремесленные кварталы и турецкий кофе',
				'paragraphs' => array(
					'Оставим парадные площади позади и отправимся в кварталы, где особенно хорошо чувствуется повседневная жизнь Стамбула: старые фасады, мастерские, маленькие галереи и антикварные лавки.',
					'Познакомимся с традициями приготовления турецкого кофе: узнаем, как выбирают помол, работают с джезвой и читают настроение напитка. По пути сделаем остановки для фотографий и локальных вкусов.',
					'Вечер оставим свободным — для прогулки, хамама, покупок или выбранного вместе ресторана.',
				),
			),
			array(
				'title'      => 'День 4 · Термальные воды и отдых',
				'paragraphs' => array(
					'После насыщенных городских дней сменим ритм и отправимся к термальным водам. Конкретную локацию выберем с учётом сезона и условий поездки.',
					'Этот день — про восстановление, спокойствие и максимальный комфорт: время для термального комплекса, отдыха и неспешного общения.',
					'Вернувшись в Стамбул, встретим вечер без спешки и подведём итоги путешествия за общим ужином.',
				),
			),
			array(
				'title'      => 'День 5 · Последнее утро и до скорой встречи',
				'paragraphs' => array(
					'После завтрака оставим время для последней прогулки, сувениров или любимого места, к которому захочется вернуться.',
					'Выезд из отеля организуем в спокойном темпе, оставив необходимый запас времени на дорогу в аэропорт. Уезжаем с фотографиями, новыми знакомствами и своим личным Стамбулом.',
				),
			),
		),
		'price'      => array(
			'old'      => '144 066 ₽',
			'current'  => '102 904 ₽',
			'discount' => '−29%',
			'note'     => 'Стоимость указана по предоставленному материалу. Актуальность цены, состав включённых услуг и условия бронирования уточняются перед записью.',
		),
		'cta'        => 'Записаться на тур',
	),
	array(
		'image'      => 'deti.png',
		'class'      => 'tour-card-children',
		'alt'        => 'Семья с детьми путешествует по Стамбулу',
		'title'      => 'Тур для детей',
		'kicker'     => 'Семейный тур · Стамбул',
		'lead'       => 'Для тех, кто скучает по ярким путешествиям и желает провести выходные насыщенно, необычно и в весёлой компании. Детям нужно!',
		'paragraphs' => array(
			'Посмотрите на мир глазами ребёнка — он очень красив.',
			'Дети способны открыть для нас красоту в мелочах, видеть волшебство в обыденном. Мы, родители, можем помочь им познакомиться с возможностями своего внутреннего мира, жить с распростёртой душой и стремиться навстречу новым открытиям. А значит — познавать мир, разглядывать его в разных ракурсах, открывать для себя новые горизонты и питаться тёплой, жизненной энергией от всего, что нас окружает.',
			'Впечатления останутся яркими и незабываемыми!',
		),
	),
	array(
		'image'      => 'stambul.png',
		'class'      => 'tour-card-istanbul',
		'alt'        => 'Красный трамвай, Босфор и повседневная жизнь Стамбула',
		'title'      => 'Многогранность Стамбула',
		'kicker'     => 'Городской тур · Стамбул',
		'lead'       => 'Стамбул манит, притягивает и влюбляет в себя каждого, кто хоть раз побывал в этом уникальном городе.',
		'paragraphs' => array(
			'Город мечты ждёт вас с широко распахнутыми лазурными объятиями Босфора, свежим бризом Золотого Рога, ароматами симитов на набережной и свежепойманной хамсы и ставриды, буйством цветущих каштанов и роз, перекличками азана с мечетей, звенящими трамваями, вездесущими кошками и прохладой парков.',
			'Город, где восток встречается с западом, а древность переплетается с современностью. Стамбул — город с многовековой историей и богатой культурой, в котором Европа и Азия сливаются воедино.',
			'В Стамбуле можно познакомиться с консервативными привычками и свободной светской молодёжью, сойти с ума от толп на Галатском мосту и насладиться одиночеством на нетуристической набережной.',
			'Это великолепный город, который олицетворяет сочетание восточного очарования и западного утончённого стиля. Стамбул — это заботливые руки банщиц хамама, крепкий турецкий кофе, свежеиспечённая пахлава в медовом сиропе и приветливые жители города.',
			'Погрузитесь в безумную атмосферу города, проживите незабываемые эмоции и наполните свою копилку яркими впечатлениями.',
		),
	),
);
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

  <!-- wp:group {"align":"full","anchor":"impressions","className":"section-shell manifesto-section welcome-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="impressions" class="wp-block-group alignfull section-shell manifesto-section welcome-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"top","className":"welcome-layout"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-top welcome-layout">
      <!-- wp:column {"verticalAlignment":"top","width":"64%","className":"welcome-visual"} -->
      <div class="wp-block-column is-vertically-aligned-top welcome-visual" style="flex-basis:64%">
        <!-- wp:group {"className":"manifesto-collage","layout":{"type":"grid","columnCount":12,"minimumColumnWidth":null}} -->
        <div class="wp-block-group manifesto-collage">
          <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"manifesto-image manifesto-image-a"} --><figure class="wp-block-image size-full manifesto-image manifesto-image-a"><img src="<?php echo esc_url( $images_uri . '/istanbul-breakfast.jpg' ); ?>" alt="Турецкий завтрак на улице Стамбула" /></figure><!-- /wp:image -->
          <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"manifesto-image manifesto-image-b"} --><figure class="wp-block-image size-full manifesto-image manifesto-image-b"><img src="<?php echo esc_url( $images_uri . '/pamukkale-terraces.png' ); ?>" alt="Белые травертиновые террасы Памуккале" /></figure><!-- /wp:image -->
          <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"manifesto-image manifesto-image-c"} --><figure class="wp-block-image size-full manifesto-image manifesto-image-c"><img src="<?php echo esc_url( $images_uri . '/galata-stairs-editorial.jpg' ); ?>" alt="Редакционный вид лестниц и улиц Галаты" /></figure><!-- /wp:image -->
        </div>
        <!-- /wp:group -->
      </div>
      <!-- /wp:column -->

      <!-- wp:column {"verticalAlignment":"top","width":"36%","className":"welcome-copy"} -->
      <div class="wp-block-column is-vertically-aligned-top welcome-copy" style="flex-basis:36%">
        <!-- wp:heading {"fontSize":"xl","className":"welcome-title"} --><h2 class="wp-block-heading welcome-title has-xl-font-size">Приветствую вас, дорогие<br>путешественники</h2><!-- /wp:heading -->
        <!-- wp:paragraph {"className":"welcome-text"} --><p class="welcome-text">и гости необыкновенного, многовекового города! Стамбул-великолепный город, который олицетворяет сочетание восточного очарования и западного утонченного стиля. Это место, где восток встречается с западом, и древность переплетается с современностью! Успешно занимаюсь туризмом более 20 лет, в том числе организацией и проведением как групповых, так индивидуальных туров. Стамбул занимает отдельное место в моем сердце. Благодаря знаниям и опыту работы, я смогу показать вам лучшие места в Стамбуле и открыть многие его секреты. И, конечно, мы обязательно прочувствуем удивительную атмосферу города и оценим его важный вклад в мировую культуру. Каждый из моих туристов - для меня дорогой гость. Поэтому я всегда адаптирую экскурсии с учетом ваших личных интересов и предпочтений.</p><!-- /wp:paragraph -->
        <!-- wp:buttons {"className":"welcome-actions"} -->
        <div class="wp-block-buttons welcome-actions"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#route">Выбрать направление <span>↓</span></a></div><!-- /wp:button --></div>
        <!-- /wp:buttons -->
      </div>
      <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"route","className":"destination-section","layout":{"type":"default"}} -->
  <div id="route" class="wp-block-group alignfull destination-section">
    <!-- wp:group {"className":"destination-sticky","layout":{"type":"default"}} -->
    <div class="wp-block-group destination-sticky">
      <!-- wp:group {"align":"wide","className":"destination-topline","layout":{"type":"default"}} -->
      <div class="wp-block-group alignwide destination-topline">
        <!-- wp:group {"className":"destination-heading","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
        <div class="wp-block-group destination-heading"><!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Все туры</h2><!-- /wp:heading --></div><!-- /wp:group --></div>
        <!-- /wp:group -->
        <!-- wp:group {"className":"destination-progress","layout":{"type":"flex","flexWrap":"nowrap"}} -->
        <div class="wp-block-group destination-progress">
          <!-- wp:paragraph {"className":"destination-arrow destination-arrow-prev"} --><p class="destination-arrow destination-arrow-prev">←</p><!-- /wp:paragraph -->
          <!-- wp:group {"className":"destination-range","layout":{"type":"flex","flexWrap":"nowrap"}} -->
          <div class="wp-block-group destination-range"><!-- wp:separator {"className":"destination-slider"} --><hr class="wp-block-separator has-alpha-channel-opacity destination-slider"/><!-- /wp:separator --></div>
          <!-- /wp:group -->
          <!-- wp:paragraph {"className":"destination-arrow destination-arrow-next"} --><p class="destination-arrow destination-arrow-next">→</p><!-- /wp:paragraph -->
        </div>
        <!-- /wp:group -->
      </div>
      <!-- /wp:group -->
      <!-- wp:group {"className":"destination-viewport","layout":{"type":"default"}} -->
      <div class="wp-block-group destination-viewport">
        <!-- wp:group {"className":"destination-track","layout":{"type":"flex","flexWrap":"nowrap"}} -->
        <div class="wp-block-group destination-track">
<?php foreach ( $tours as $tour ) : ?>
          <!-- wp:group {"className":"destination-card <?php echo esc_attr( $tour['class'] ); ?>","layout":{"type":"default"}} -->
          <div class="wp-block-group destination-card <?php echo esc_attr( $tour['class'] ); ?>">
            <!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="<?php echo esc_url( $images_uri . '/' . $tour['image'] ); ?>" alt="<?php echo esc_attr( $tour['alt'] ); ?>" /></figure><!-- /wp:image -->
            <!-- wp:group {"className":"destination-card-copy","layout":{"type":"constrained"}} --><div class="wp-block-group destination-card-copy"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading"><?php echo esc_html( $tour['title'] ); ?></h3><!-- /wp:heading --></div><!-- /wp:group -->
            <!-- wp:buttons {"className":"tour-details-trigger"} --><div class="wp-block-buttons tour-details-trigger"><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#">Подробнее</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
          </div>
          <!-- /wp:group -->
<?php endforeach; ?>
        </div>
        <!-- /wp:group -->
      </div>
      <!-- /wp:group -->
      <!-- wp:group {"anchor":"tour-details","className":"tour-details-library","layout":{"type":"grid","columnCount":2,"minimumColumnWidth":null}} -->
      <div id="tour-details" class="wp-block-group tour-details-library">
<?php foreach ( $tours as $tour ) : ?>
        <!-- wp:group {"className":"tour-details","layout":{"type":"constrained"}} -->
        <div class="wp-block-group tour-details">
          <!-- wp:paragraph {"className":"tour-detail-kicker"} --><p class="tour-detail-kicker"><?php echo esc_html( $tour['kicker'] ); ?></p><!-- /wp:paragraph -->
          <!-- wp:heading {"level":3} --><h3 class="wp-block-heading"><?php echo esc_html( $tour['title'] ); ?></h3><!-- /wp:heading -->
          <!-- wp:paragraph {"className":"tour-detail-lead"} --><p class="tour-detail-lead"><?php echo esc_html( $tour['lead'] ); ?></p><!-- /wp:paragraph -->
<?php foreach ( $tour['paragraphs'] as $paragraph ) : ?>
          <!-- wp:paragraph --><p><?php echo esc_html( $paragraph ); ?></p><!-- /wp:paragraph -->
<?php endforeach; ?>
<?php if ( ! empty( $tour['facts'] ) ) : ?>
          <!-- wp:group {"className":"tour-detail-facts tour-detail-facts--five","layout":{"type":"grid","columnCount":5,"minimumColumnWidth":null}} -->
          <div class="wp-block-group tour-detail-facts tour-detail-facts--five">
<?php foreach ( $tour['facts'] as $fact ) : ?>
            <!-- wp:paragraph --><p><strong><?php echo esc_html( $fact['value'] ); ?></strong><br><?php echo esc_html( $fact['label'] ); ?></p><!-- /wp:paragraph -->
<?php endforeach; ?>
          </div>
          <!-- /wp:group -->
<?php endif; ?>
<?php if ( ! empty( $tour['program'] ) ) : ?>
          <!-- wp:group {"className":"tour-detail-program","layout":{"type":"constrained"}} -->
          <div class="wp-block-group tour-detail-program">
            <!-- wp:heading {"level":4} --><h4 class="wp-block-heading">Программа тура</h4><!-- /wp:heading -->
<?php foreach ( $tour['program'] as $day ) : ?>
            <!-- wp:group {"className":"tour-detail-day","layout":{"type":"constrained"}} -->
            <div class="wp-block-group tour-detail-day">
              <!-- wp:heading {"level":4} --><h4 class="wp-block-heading"><?php echo esc_html( $day['title'] ); ?></h4><!-- /wp:heading -->
<?php foreach ( $day['paragraphs'] as $day_paragraph ) : ?>
              <!-- wp:paragraph --><p><?php echo esc_html( $day_paragraph ); ?></p><!-- /wp:paragraph -->
<?php endforeach; ?>
            </div>
            <!-- /wp:group -->
<?php endforeach; ?>
          </div>
          <!-- /wp:group -->
<?php endif; ?>
<?php if ( ! empty( $tour['price'] ) ) : ?>
          <!-- wp:group {"className":"tour-detail-price","layout":{"type":"constrained"}} -->
          <div class="wp-block-group tour-detail-price">
            <!-- wp:paragraph {"className":"tour-detail-price-label"} --><p class="tour-detail-price-label">Стоимость тура</p><!-- /wp:paragraph -->
            <!-- wp:paragraph {"className":"tour-detail-price-old"} --><p class="tour-detail-price-old"><s><?php echo esc_html( $tour['price']['old'] ); ?></s> <mark><?php echo esc_html( $tour['price']['discount'] ); ?></mark></p><!-- /wp:paragraph -->
            <!-- wp:paragraph {"className":"tour-detail-price-current"} --><p class="tour-detail-price-current"><strong><?php echo esc_html( $tour['price']['current'] ); ?></strong></p><!-- /wp:paragraph -->
            <!-- wp:paragraph {"className":"tour-detail-note"} --><p class="tour-detail-note"><?php echo esc_html( $tour['price']['note'] ); ?></p><!-- /wp:paragraph -->
          </div>
          <!-- /wp:group -->
<?php endif; ?>
          <!-- wp:buttons {"className":"tour-detail-cta"} --><div class="wp-block-buttons tour-detail-cta"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact"><?php echo esc_html( $tour['cta'] ?? 'Обсудить этот тур' ); ?></a></div><!-- /wp:button --></div><!-- /wp:buttons -->
        </div>
        <!-- /wp:group -->
<?php endforeach; ?>
      </div>
      <!-- /wp:group -->
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
