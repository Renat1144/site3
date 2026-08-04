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
		'slug'       => 'mesopotamiya',
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
		'slug'       => 'karadeiz',
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
		'slug'       => 'vostochnaya-skazka',
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
		'card_facts' => array( '5 дней', 'До 8 человек', '102 904 ₽' ),
		'cta'        => 'Оставить заявку',
	),
	array(
		'image'      => 'deti.png',
		'slug'       => 'tur-dlya-detey',
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
		'slug'       => 'mnogogrannost-stambula',
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
	array(
		'image'      => 'kapadokia.png',
		'slug'       => 'stambul-i-kappadokiya',
		'class'      => 'tour-card-cappadocia-comfort',
		'alt'        => 'Пара встречает рассвет с завтраком и воздушными шарами в Каппадокии',
		'title'      => 'Стамбул и Каппадокия: комфорт-тур',
		'kicker'     => 'Комфорт-тур · Стамбул и Каппадокия',
		'lead'       => 'Каппадокия — одно из тех редких мест, которые стоит посетить хотя бы раз в жизни. Это страна сказок — таинственная и чарующая.',
		'paragraphs' => array(
			'Стамбул — город прошлого, настоящего и будущего. Он не только соединяет континенты, но и объединяет культуры и людей.',
			'В Стамбуле вас ждут прогулка по Босфору, хамам, Гранд-базар, дворцы, старые кварталы, частная яхта и камерные рестораны. В Каппадокии — полёт на воздушном шаре, красивые рассветы, долины, подземные города, скальные монастыри, винодельни и отель с видом на марсианские пейзажи.',
			'Посетите Стамбул и Каппадокию с нами и почувствуйте гармонию противоположностей.',
		),
	),
	array(
		'image'      => 'deti2.png',
		'slug'       => 'gorod-shkatulka-dlya-detey',
		'class'      => 'tour-card-city-box',
		'alt'        => 'Дети кормят чаек во время прогулки по Босфору',
		'title'      => 'Город-шкатулка для детей',
		'kicker'     => 'Семейный тур · Стамбул',
		'lead'       => 'Для тех, кто скучает по ярким путешествиям и желает провести выходные насыщенно, необычно и в весёлой компании. Детям нужно!',
		'paragraphs' => array(
			'Посмотрите на мир глазами ребёнка — он очень красив.',
			'Дети способны открыть для нас красоту в мелочах и увидеть волшебство в обыденном. Мы, родители, можем помочь им познакомиться с возможностями своего внутреннего мира, жить с распростёртой душой и стремиться навстречу новым открытиям. А значит — познавать мир, разглядывать его в разных ракурсах, открывать для себя новые горизонты и питаться тёплой, жизненной энергией от всего, что нас окружает.',
		),
	),
	array(
		'image'      => 'stambul2.png',
		'slug'       => 'sliyanie-imperiy-stambul',
		'class'      => 'tour-card-empires',
		'alt'        => 'Собор Святой Софии, Босфор и силуэт Стамбула в золотой час',
		'title'      => 'Слияние империй: Стамбул',
		'kicker'     => 'Исторический тур · Стамбул',
		'lead'       => 'Стамбул — уникальный город на стыке двух частей света — Европы и Азии — и трёх великих империй: Римской, Византийской и Османской.',
		'paragraphs' => array(
			'За свою долгую историю он носил имена Византий, Константинополь и Царьград, объединяя разные культуры, религии и архитектурные стили. В этом туре вы прикоснётесь к символам роскоши Османской империи и шедеврам византийской архитектуры.',
			'Поскольку участники прилетают из разных городов, рекомендую прибыть до 14:00. Я стараюсь встречать всех в аэропорту. Если рейс очень ранний или поздний, вы получите максимально подробную инструкцию о том, как самостоятельно добраться до отеля.',
			'Из аэропорта нас заберёт комфортабельный трансфер и привезёт прямо в отель, где можно будет отдохнуть после перелёта.',
		),
		'program'    => array(
			array(
				'title'      => 'День 1 · Первое знакомство с городом',
				'paragraphs' => array(
					'Вечером отправимся на пешеходную прогулку по историческому центру Стамбула.',
					'Уверена, уже в этот момент вы оцените, насколько прекрасен город.',
					'После прогулки я предложу вам ужин в уютном кафе с потрясающим видом.',
				),
			),
		),
	),
);
$primary_tour = $tours[2];
$other_tours  = array_values(
	array_filter(
		$tours,
		static function ( array $tour ): bool {
			return 'vostochnaya-skazka' !== $tour['slug'];
		}
	)
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
      <!-- wp:heading {"level":1,"fontSize":"hero","className":"hero-title hero-title-brand"} --><h1 class="wp-block-heading hero-title hero-title-brand has-hero-font-size">Авторские туры<br><em>по Турции</em></h1><!-- /wp:heading -->
      <!-- wp:group {"className":"hero-bottom","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
      <div class="wp-block-group hero-bottom">
        <!-- wp:group {"className":"hero-copy","layout":{"type":"constrained"}} --><div class="wp-block-group hero-copy"><!-- wp:paragraph {"fontSize":"lg","className":"hero-lead"} --><p class="hero-lead has-lg-font-size">Продуманные маршруты по Стамбулу, Каппадокии и другим регионам — с комфортным ритмом, личным сопровождением и местами, которые редко входят в обычные программы.</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"hero-assurance"} --><p class="hero-assurance">Оставьте контакты — поможем выбрать подходящий маршрут и ответим на вопросы.</p><!-- /wp:paragraph --></div><!-- /wp:group -->
        <!-- wp:buttons {"className":"hero-actions"} -->
        <div class="wp-block-buttons hero-actions"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact-form">Подобрать тур <span>↗</span></a></div><!-- /wp:button --><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#primary-tour">Смотреть главный тур</a></div><!-- /wp:button --></div>
        <!-- /wp:buttons -->
      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"primary-tour","className":"section-shell primary-tour-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="primary-tour" class="wp-block-group alignfull section-shell primary-tour-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"stretch","className":"primary-tour-layout"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-stretch primary-tour-layout">
      <!-- wp:column {"verticalAlignment":"stretch","width":"58%","className":"primary-tour-visual"} --><div class="wp-block-column is-vertically-aligned-stretch primary-tour-visual" style="flex-basis:58%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="<?php echo esc_url( $images_uri . '/' . $primary_tour['image'] ); ?>" alt="<?php echo esc_attr( $primary_tour['alt'] ); ?>" /></figure><!-- /wp:image --></div><!-- /wp:column -->
      <!-- wp:column {"verticalAlignment":"stretch","className":"primary-tour-copy"} -->
      <div class="wp-block-column is-vertically-aligned-stretch primary-tour-copy">
        <!-- wp:paragraph {"className":"eyebrow primary-tour-eyebrow"} --><p class="eyebrow primary-tour-eyebrow">Главный маршрут · Стамбул</p><!-- /wp:paragraph -->
        <!-- wp:heading {"level":2,"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Восточная<br><em>сказка.</em></h2><!-- /wp:heading -->
        <!-- wp:paragraph {"className":"primary-tour-lead"} --><p class="primary-tour-lead"><?php echo esc_html( $primary_tour['lead'] ); ?></p><!-- /wp:paragraph -->
        <!-- wp:group {"className":"primary-tour-story","layout":{"type":"constrained"}} --><div class="wp-block-group primary-tour-story">
<?php foreach ( $primary_tour['paragraphs'] as $tour_paragraph ) : ?>
          <!-- wp:paragraph --><p><?php echo esc_html( $tour_paragraph ); ?></p><!-- /wp:paragraph -->
<?php endforeach; ?>
        </div><!-- /wp:group -->
      </div>
      <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
    <!-- wp:group {"align":"wide","className":"primary-tour-footer","layout":{"type":"default"}} --><div class="wp-block-group alignwide primary-tour-footer">
        <!-- wp:group {"className":"primary-tour-facts","layout":{"type":"grid","columnCount":5,"minimumColumnWidth":null}} --><div class="wp-block-group primary-tour-facts">
          <!-- wp:paragraph --><p><strong>5 дней</strong><span>продолжительность</span></p><!-- /wp:paragraph -->
          <!-- wp:paragraph --><p><strong>До 8</strong><span>участников</span></p><!-- /wp:paragraph -->
          <!-- wp:paragraph --><p><strong>5/5</strong><span>уровень комфорта</span></p><!-- /wp:paragraph -->
          <!-- wp:paragraph --><p><strong>3/5</strong><span>средняя активность</span></p><!-- /wp:paragraph -->
          <!-- wp:paragraph {"className":"primary-tour-fact-wide"} --><p class="primary-tour-fact-wide"><strong>Русский</strong><span>язык тура</span></p><!-- /wp:paragraph -->
        </div><!-- /wp:group -->
        <!-- wp:buttons {"className":"primary-tour-actions","layout":{"type":"flex","justifyContent":"right"}} --><div class="wp-block-buttons primary-tour-actions"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#program">Смотреть программу <span>↓</span></a></div><!-- /wp:button --><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#contact-form">Оставить заявку</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
    </div><!-- /wp:group -->
  </div>
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
        <div class="wp-block-buttons welcome-actions"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#program">Посмотреть программу <span>↓</span></a></div><!-- /wp:button --></div>
        <!-- /wp:buttons -->
      </div>
      <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"program","className":"section-shell program-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="program" class="wp-block-group alignfull section-shell program-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"top","className":"program-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-top program-grid">
      <!-- wp:column {"verticalAlignment":"top","width":"46%","className":"program-visual"} --><div class="wp-block-column is-vertically-aligned-top program-visual" style="flex-basis:46%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="<?php echo esc_url( $images_uri . '/turquoise-yacht.png' ); ?>" alt="Яхта у побережья Турции" /></figure><!-- /wp:image --></div><!-- /wp:column -->
      <!-- wp:column {"verticalAlignment":"top","className":"program-copy"} -->
      <div class="wp-block-column is-vertically-aligned-top program-copy">
        <!-- wp:paragraph {"className":"eyebrow program-eyebrow"} --><p class="eyebrow program-eyebrow">Программа · Восточная сказка</p><!-- /wp:paragraph -->
        <!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Пять дней.<br><em>Один живой Стамбул.</em></h2><!-- /wp:heading -->
        <!-- wp:paragraph {"className":"program-intro"} --><p class="program-intro">От первой встречи с Босфором до последнего спокойного утра — программа сохраняет баланс истории, локальных впечатлений и времени для отдыха.</p><!-- /wp:paragraph -->
        <!-- wp:group {"className":"program-list","layout":{"type":"constrained"}} -->
        <div class="wp-block-group program-list">
<?php foreach ( $primary_tour['program'] as $day_index => $day ) : ?>
          <!-- wp:details {"className":"program-day"} --><details class="wp-block-details program-day"><summary><span><?php echo esc_html( sprintf( '%02d', $day_index + 1 ) ); ?></span> <?php echo esc_html( preg_replace( '/^День\s+\d+\s+·\s*/u', '', $day['title'] ) ); ?></summary>
<?php foreach ( $day['paragraphs'] as $day_paragraph ) : ?>
            <!-- wp:paragraph --><p><?php echo esc_html( $day_paragraph ); ?></p><!-- /wp:paragraph -->
<?php endforeach; ?>
          </details><!-- /wp:details -->
<?php endforeach; ?>
        </div>
        <!-- /wp:group -->
      </div>
      <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"dates","className":"dates-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="dates" class="wp-block-group alignfull dates-section reveal-on-scroll">
    <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"dates-background"} --><figure class="wp-block-image size-full dates-background"><img src="<?php echo esc_url( $images_uri . '/nemrut-sunrise.png' ); ?>" alt="Рассвет на горе Немрут" /></figure><!-- /wp:image -->
    <!-- wp:columns {"align":"wide","verticalAlignment":"stretch","className":"dates-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-stretch dates-grid">
      <!-- wp:column {"verticalAlignment":"stretch","width":"48%","className":"dates-intro"} --><div class="wp-block-column is-vertically-aligned-stretch dates-intro" style="flex-basis:48%"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Восточная<br><em>сказка.</em></h2><!-- /wp:heading --><!-- wp:paragraph --><p>Пять дней в Стамбуле с программой, личным сопровождением и камерной группой до восьми человек.</p><!-- /wp:paragraph --></div><!-- /wp:column -->
      <!-- wp:column {"verticalAlignment":"stretch","className":"offer-card"} -->
      <div class="wp-block-column is-vertically-aligned-stretch offer-card">
        <!-- wp:group {"className":"offer-top","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} --><div class="wp-block-group offer-top"><!-- wp:paragraph {"className":"offer-status"} --><p class="offer-status">Даты уточняются</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>5 дней · до 8 участников</p><!-- /wp:paragraph --></div><!-- /wp:group -->
        <!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Стоимость тура</h3><!-- /wp:heading -->
        <!-- wp:group {"className":"offer-price-row","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} --><div class="wp-block-group offer-price-row"><!-- wp:paragraph {"className":"offer-price"} --><p class="offer-price"><?php echo esc_html( $primary_tour['price']['current'] ); ?></p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"offer-price-old"} --><p class="offer-price-old"><s><?php echo esc_html( $primary_tour['price']['old'] ); ?></s> <mark><?php echo esc_html( $primary_tour['price']['discount'] ); ?></mark></p><!-- /wp:paragraph --></div><!-- /wp:group -->
        <!-- wp:separator --><hr class="wp-block-separator has-alpha-channel-opacity"/><!-- /wp:separator -->
        <!-- wp:paragraph {"className":"offer-note"} --><p class="offer-note"><?php echo esc_html( $primary_tour['price']['note'] ); ?></p><!-- /wp:paragraph -->
        <!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"width":100} --><div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link wp-element-button" href="#contact-form">Оставить заявку <span>↗</span></a></div><!-- /wp:button --></div><!-- /wp:buttons -->
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
      <!-- wp:group {"align":"wide","className":"destination-topline","layout":{"type":"default"}} --><div class="wp-block-group alignwide destination-topline"><!-- wp:group {"className":"destination-heading","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} --><div class="wp-block-group destination-heading"><!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Другие авторские туры</h2><!-- /wp:heading --></div><!-- /wp:group --></div><!-- /wp:group --></div><!-- /wp:group -->
      <!-- wp:group {"className":"destination-viewport","layout":{"type":"default"}} --><div class="wp-block-group destination-viewport">
        <!-- wp:group {"className":"destination-track destination-track-other","layout":{"type":"grid","columnCount":2,"minimumColumnWidth":null}} --><div class="wp-block-group destination-track destination-track-other">
<?php foreach ( $other_tours as $tour ) : ?>
          <!-- wp:group {"className":"destination-card <?php echo esc_attr( $tour['class'] ); ?>","layout":{"type":"default"}} -->
          <div class="wp-block-group destination-card <?php echo esc_attr( $tour['class'] ); ?>">
            <!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="<?php echo esc_url( $images_uri . '/' . $tour['image'] ); ?>" alt="<?php echo esc_attr( $tour['alt'] ); ?>" /></figure><!-- /wp:image -->
            <!-- wp:group {"className":"destination-card-copy","layout":{"type":"constrained"}} --><div class="wp-block-group destination-card-copy"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading"><?php echo esc_html( $tour['title'] ); ?></h3><!-- /wp:heading --></div><!-- /wp:group -->
            <!-- wp:buttons {"className":"tour-details-trigger"} --><div class="wp-block-buttons tour-details-trigger"><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/' . $tour['slug'] . '/' ) ); ?>">Подробнее</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
          </div>
          <!-- /wp:group -->
<?php endforeach; ?>
        </div><!-- /wp:group -->
      </div><!-- /wp:group -->
    </div><!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"faq","className":"section-shell faq-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="faq" class="wp-block-group alignfull section-shell faq-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"top","className":"faq-grid"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-top faq-grid"><!-- wp:column {"verticalAlignment":"top","width":"42%","className":"faq-intro"} --><div class="wp-block-column is-vertically-aligned-top faq-intro" style="flex-basis:42%"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Перед тем<br><em>как решиться.</em></h2><!-- /wp:heading --><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"faq-image"} --><figure class="wp-block-image size-full faq-image"><img src="<?php echo esc_url( $images_uri . '/tea-by-bosphorus.jpg' ); ?>" alt="Чай у Босфора" /></figure><!-- /wp:image --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"top","className":"faq-list"} --><div class="wp-block-column is-vertically-aligned-top faq-list">
      <!-- wp:details --><details class="wp-block-details"><summary>Нужна ли виза и какие документы потребуются?</summary><!-- wp:paragraph --><p>Правила зависят от гражданства и могут меняться. До бронирования мы подскажем, какие требования нужно проверить в официальных источниках именно для вашей поездки.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details --><details class="wp-block-details"><summary>Что входит в стоимость тура?</summary><!-- wp:paragraph --><p>Состав включённых и дополнительных услуг у каждого маршрута свой. Все подтверждённые условия будут указаны на странице тура и зафиксированы до оплаты; если какой-то пункт ещё уточняется, менеджер сообщит об этом заранее.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details --><details class="wp-block-details"><summary>Входит ли перелёт и поможете ли подобрать рейс?</summary><!-- wp:paragraph --><p>Статус перелёта указывается для каждого тура отдельно. Если информации на странице пока нет, уточним её до бронирования и поможем сориентироваться по удобным вариантам перелёта.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details --><details class="wp-block-details"><summary>Где мы будем жить?</summary><!-- wp:paragraph --><p>Отель, категория номера и формат размещения подтверждаются для конкретного выезда. Эти данные вы получите до оплаты; при необходимости обсудим одноместное размещение и другие пожелания.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details --><details class="wp-block-details"><summary>Можно ли поехать одному?</summary><!-- wp:paragraph --><p>Да, можно оставить заявку без компании. Мы расскажем о составе группы, вариантах размещения и поможем понять, насколько выбранный формат подходит именно вам.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details --><details class="wp-block-details"><summary>Сколько человек будет в группе?</summary><!-- wp:paragraph --><p>Если размер группы уже определён, он указан на странице тура. Для маршрутов в подготовке точное количество участников подтверждается до бронирования.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details --><details class="wp-block-details"><summary>Подойдёт ли тур детям и нужна ли физическая подготовка?</summary><!-- wp:paragraph --><p>Это зависит от маршрута, возраста ребёнка и уровня активности. Расскажите нам о составе поездки и возможных ограничениях — мы честно подскажем подходящий вариант.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
      <!-- wp:details --><details class="wp-block-details"><summary>Как проходит бронирование и что будет при отмене?</summary><!-- wp:paragraph --><p>Размер предоплаты, сроки и правила отмены должны быть указаны в документах конкретного тура. До перевода денег внимательно ознакомьтесь с условиями; мы ответим на вопросы по каждому пункту.</p><!-- /wp:paragraph --></details><!-- /wp:details -->
    </div><!-- /wp:column --></div>
    <!-- /wp:columns -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"reviews","className":"section-shell reviews-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="reviews" class="wp-block-group alignfull section-shell reviews-section reveal-on-scroll">
    <!-- wp:columns {"align":"wide","verticalAlignment":"bottom","className":"reviews-heading"} --><div class="wp-block-columns alignwide are-vertically-aligned-bottom reviews-heading"><!-- wp:column {"verticalAlignment":"bottom","width":"64%"} --><div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:64%"><!-- wp:paragraph {"className":"eyebrow reviews-eyebrow"} --><p class="eyebrow reviews-eyebrow">Отзывы путешественников</p><!-- /wp:paragraph --><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Слова, которые<br><em>хочется сохранить.</em></h2><!-- /wp:heading --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"bottom"} --><div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:paragraph {"className":"reviews-disclaimer"} --><p class="reviews-disclaimer"><strong>До публикации замените примеры реальными отзывами.</strong> Эти тексты показывают будущий формат секции и не являются отзывами клиентов.</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns -->
    <!-- wp:group {"align":"wide","className":"reviews-grid","layout":{"type":"grid","columnCount":3,"minimumColumnWidth":null}} --><div class="wp-block-group alignwide reviews-grid">
      <!-- wp:group {"className":"review-card","layout":{"type":"constrained"}} --><div class="wp-block-group review-card"><!-- wp:paragraph {"className":"review-card-label"} --><p class="review-card-label">Пример формата · спокойный ритм</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"review-card-quote"} --><p class="review-card-quote">«Я боялась, что пять дней превратятся в бесконечную гонку. Вместо этого мы увидели главное, пили кофе в местах без туристических вывесок и оставляли время для себя. Я вернулась не уставшей, а наполненной».</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"review-card-author"} --><p class="review-card-author">Имя путешественника <span>· демонстрационный текст</span></p><!-- /wp:paragraph --></div><!-- /wp:group -->
      <!-- wp:group {"className":"review-card review-card-featured","layout":{"type":"constrained"}} --><div class="wp-block-group review-card review-card-featured"><!-- wp:paragraph {"className":"review-card-label"} --><p class="review-card-label">Пример формата · поездка вдвоём</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"review-card-quote"} --><p class="review-card-quote">«Больше всего запомнилось ощущение заботы: трансфер, прогулки, рестораны и яхта сложились в одну историю. Не приходилось каждый вечер решать, куда идти завтра, — оставалось просто проживать Стамбул вдвоём».</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"review-card-author"} --><p class="review-card-author">Имена путешественников <span>· демонстрационный текст</span></p><!-- /wp:paragraph --></div><!-- /wp:group -->
      <!-- wp:group {"className":"review-card","layout":{"type":"constrained"}} --><div class="wp-block-group review-card"><!-- wp:paragraph {"className":"review-card-label"} --><p class="review-card-label">Пример формата · путешествие соло</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"review-card-quote"} --><p class="review-card-quote">«Ехала одна и переживала, что буду чувствовать себя лишней. Уже в первый вечер мы стали маленькой компанией, а к финалу казалось, что путешествуем вместе давно. Очень камерный и тёплый формат».</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"review-card-author"} --><p class="review-card-author">Имя путешественника <span>· демонстрационный текст</span></p><!-- /wp:paragraph --></div><!-- /wp:group -->
    </div><!-- /wp:group -->
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
<?php for ( $team_index = 1; $team_index <= 3; $team_index++ ) : ?>
      <!-- wp:group {"className":"team-card","layout":{"type":"constrained"}} -->
      <div class="wp-block-group team-card">
        <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"team-portrait"} --><figure class="wp-block-image size-full team-portrait"><img src="<?php echo esc_url( $images_uri . '/team-placeholder.svg' ); ?>" alt="Место для фотографии участника команды" /></figure><!-- /wp:image -->
        <!-- wp:group {"className":"team-card-meta","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} --><div class="wp-block-group team-card-meta"><!-- wp:paragraph {"className":"team-number"} --><p class="team-number"><?php echo esc_html( sprintf( '%02d', $team_index ) ); ?></p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"team-role"} --><p class="team-role">Роль в команде</p><!-- /wp:paragraph --></div><!-- /wp:group -->
        <!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Имя участника</h3><!-- /wp:heading -->
        <!-- wp:paragraph {"className":"team-bio"} --><p class="team-bio">Короткое описание опыта, специализации и того, за какую часть путешествия отвечает этот человек.</p><!-- /wp:paragraph -->
        <!-- wp:paragraph {"className":"team-contact-placeholder"} --><p class="team-contact-placeholder">Ссылка / социальная сеть <span>↗</span></p><!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->
<?php endfor; ?>
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"align":"full","anchor":"contact","className":"contact-section reveal-on-scroll","layout":{"type":"constrained"}} -->
  <div id="contact" class="wp-block-group alignfull contact-section reveal-on-scroll">
    <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"contact-background"} --><figure class="wp-block-image size-full contact-background"><img src="<?php echo esc_url( $images_uri . '/istanbul-golden-hour.png' ); ?>" alt="Стамбул на закате" /></figure><!-- /wp:image -->
    <!-- wp:group {"align":"wide","className":"contact-inner","layout":{"type":"constrained"}} -->
    <div class="wp-block-group alignwide contact-inner"><!-- wp:heading {"fontSize":"xl"} --><h2 class="wp-block-heading has-xl-font-size">Найдём тур,<br><em>который вам подходит.</em></h2><!-- /wp:heading --><!-- wp:paragraph {"fontSize":"lg"} --><p class="has-lg-font-size">Оставьте номер телефона — ответим на вопросы и поможем выбрать маршрут без навязчивых звонков.</p><!-- /wp:paragraph -->
      <!-- wp:turkey-signature/contact-form /-->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

</main>
<!-- /wp:group -->
