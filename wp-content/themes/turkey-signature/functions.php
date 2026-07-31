<?php
/**
 * Turkey Signature theme setup.
 *
 * @package TurkeySignature
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	static function () {
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/site.css' );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		$theme_version = wp_get_theme()->get( 'Version' );

		wp_enqueue_style(
			'turkey-signature-site',
			get_theme_file_uri( 'assets/css/site.css' ),
			array(),
			$theme_version
		);

		wp_enqueue_script(
			'turkey-signature-site',
			get_theme_file_uri( 'assets/js/site.js' ),
			array(),
			$theme_version,
			true
		);
	}
);

add_action(
	'init',
	static function () {
		register_block_pattern_category(
			'turkey-signature',
			array( 'label' => __( 'Turkey Signature', 'turkey-signature' ) )
		);
	}
);

add_action(
	'init',
	static function () {
		$page_type = get_post_type_object( 'page' );

		if ( ! $page_type || ! empty( $page_type->template ) ) {
			return;
		}

		$page_type->template = array(
			array(
				'core/template-part',
				array(
					'slug'      => 'header',
					'theme'     => 'turkey-signature',
					'tagName'   => 'header',
					'className' => 'site-header',
				),
			),
			array(
				'core/group',
				array(
					'align'     => 'full',
					'className' => 'section-shell page-starter',
					'layout'    => array( 'type' => 'constrained' ),
				),
				array(
					array(
						'core/paragraph',
						array(
							'className'   => 'section-index',
							'placeholder' => __( 'Раздел страницы', 'turkey-signature' ),
						),
					),
					array(
						'core/heading',
						array(
							'level'       => 1,
							'fontSize'    => 'xl',
							'placeholder' => __( 'Заголовок страницы', 'turkey-signature' ),
						),
					),
					array(
						'core/paragraph',
						array(
							'fontSize'    => 'lg',
							'placeholder' => __( 'Добавьте содержание страницы…', 'turkey-signature' ),
						),
					),
				),
			),
			array(
				'core/template-part',
				array(
					'slug'      => 'footer',
					'theme'     => 'turkey-signature',
					'tagName'   => 'footer',
					'className' => 'site-footer',
				),
			),
		);
	}
);
