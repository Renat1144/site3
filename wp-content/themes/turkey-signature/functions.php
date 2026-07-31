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

