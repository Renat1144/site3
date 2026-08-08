<?php
/**
 * Build a GitHub Pages snapshot from published WordPress pages.
 *
 * The script runs inside the WordPress container and exports the front page
 * plus every top-level published page to a matching static directory.
 */

declare( strict_types=1 );

if ( PHP_SAPI !== 'cli' ) {
	fwrite( STDERR, "This exporter can only run from the command line.\n" );
	exit( 1 );
}

$options    = getopt( '', array( 'source-url:', 'output:' ) );
$source_url = rtrim( (string) ( $options['source-url'] ?? 'http://localhost:8082' ), '/' );
$output     = (string) ( $options['output'] ?? '/var/www/html/index.html' );
$host       = (string) parse_url( $source_url, PHP_URL_HOST );
$port       = parse_url( $source_url, PHP_URL_PORT );

if ( '' === $host ) {
	fwrite( STDERR, "The source URL is invalid.\n" );
	exit( 1 );
}

$host_header = $host . ( null !== $port ? ':' . $port : '' );
$context     = stream_context_create(
	array(
		'http' => array(
			'method'        => 'GET',
			'header'        => "Host: {$host_header}\r\nConnection: close\r\n",
			'ignore_errors' => true,
			'timeout'       => 10,
		),
	)
);

$fetch_local = static function ( string $path, int $attempts = 1 ) use ( $context ) {
	$body        = false;
	$status_code = 0;
	$path        = '/' . ltrim( $path, '/' );

	for ( $attempt = 0; $attempt < $attempts; ++$attempt ) {
		$http_response_header = array();
		$body                 = @file_get_contents( 'http://127.0.0.1' . $path, false, $context );
		$status_line          = $http_response_header[0] ?? '';

		if ( preg_match( '/\s(\d{3})\s/', $status_line, $matches ) ) {
			$status_code = (int) $matches[1];
		}

		if ( false !== $body && 200 === $status_code ) {
			return $body;
		}

		if ( $attempt + 1 < $attempts ) {
			sleep( 1 );
		}
	}

	throw new RuntimeException( sprintf( 'WordPress path "%s" did not return HTTP 200.', $path ) );
};

$navigation_css_path = '/var/www/html/wp-includes/blocks/navigation/style.min.css';
$navigation_css      = @file_get_contents( $navigation_css_path );

if ( false === $navigation_css || '' === trim( $navigation_css ) ) {
	fwrite( STDERR, "The WordPress navigation stylesheet was not found.\n" );
	exit( 1 );
}

$transform_page = static function ( string $html, string $relative_base ) use ( $navigation_css, $source_url ): string {
	$navigation_pattern = '~<link\b(?=[^>]*\bid=["\']wp-block-navigation-css["\'])[^>]*>\s*~i';
	$html               = preg_replace_callback(
		$navigation_pattern,
		static fn() => "<style id=\"wp-block-navigation-css\">\n{$navigation_css}\n</style>\n",
		$html,
		1,
		$navigation_replacements
	);

	if ( 0 === $navigation_replacements ) {
		$html = preg_replace(
			'~</head>~i',
			"<style id=\"wp-block-navigation-css\">\n{$navigation_css}\n</style>\n</head>",
			$html,
			1,
			$navigation_injections
		);
		if ( 1 !== $navigation_injections ) {
			throw new RuntimeException( 'The navigation stylesheet could not be embedded in a page.' );
		}
	} elseif ( 1 !== $navigation_replacements ) {
		throw new RuntimeException( 'The WordPress navigation stylesheet appeared more than once in a page.' );
	}

	$remove_patterns = array(
		'~<link\s+rel=["\']alternate["\'][^>]*>\s*~i',
		'~<link\s+rel=["\']https://api\.w\.org/["\'][^>]*>\s*~i',
		'~<link\s+rel=["\']EditURI["\'][^>]*>\s*~i',
		'~<meta\s+name=["\']generator["\'][^>]*>\s*~i',
		'~<script\b[^>]*type=["\']importmap["\'][^>]*>.*?</script>\s*~is',
		'~<link\b[^>]*rel=["\']modulepreload["\'][^>]*>\s*~i',
		'~<script\b[^>]*type=["\']speculationrules["\'][^>]*>.*?</script>\s*~is',
		'~<script\b[^>]*type=["\']module["\'][^>]*>.*?</script>\s*~is',
		'~<script\b[^>]*id=["\']wp-emoji-settings["\'][^>]*>.*?</script>\s*~is',
	);

	foreach ( $remove_patterns as $pattern ) {
		$html = preg_replace( $pattern, '', $html );
	}

	// GitHub Pages has no WordPress mail endpoint, so omit the live form while
	// preserving every CTA. Static CTAs are redirected to the contact section
	// on the exported home page instead of pointing at the absent modal.
	$html = preg_replace( '~<!-- ts-contact-static-omit:start -->.*?<!-- ts-contact-static-omit:end -->\s*~is', '', $html );
	$html = preg_replace( '~<link\b(?=[^>]*turkey-signature-contact)[^>]*>\s*~is', '', $html );
	$html = preg_replace( '~<style\b(?=[^>]*turkey-signature-contact)[^>]*>.*?</style>\s*~is', '', $html );
	$html = preg_replace( '~<script\b(?=[^>]*turkey-signature-contact)[^>]*>.*?</script>\s*~is', '', $html );

	// Root-relative fragment links work in local WordPress but escape the
	// /site3/ project path on GitHub Pages. Keep them relative to the exported
	// home page: ./#section from index.html and ../#section from tour pages.
	$html = preg_replace_callback(
		'~\bhref=(["\'])/\#([^"\']+)\1~i',
		static fn( $matches ) => 'href=' . $matches[1] . $relative_base . '#' . $matches[2] . $matches[1],
		$html
	);
	// Keep links to exported WordPress pages inside the GitHub Pages project
	// path instead of sending visitors to the domain root.
	$html = preg_replace_callback(
		'~\bhref=(["\'])/(privacy-policy|personal-data-consent)/?\1~i',
		static fn( $matches ) => 'href=' . $matches[1] . $relative_base . $matches[2] . '/' . $matches[1],
		$html
	);
	$html = preg_replace_callback(
		'~\bhref=(["\'])#contact(?:-form)?\1~i',
		static fn( $matches ) => 'href=' . $matches[1] . $relative_base . '#contact' . $matches[1],
		$html
	);

	$html = str_replace( $source_url . '/', $relative_base, $html );
	$html = str_replace( $source_url, rtrim( $relative_base, '/' ), $html );
	$html = preg_replace( '/<html\b/', '<html data-static-export="true"', $html, 1 );
	$html = preg_replace_callback( '/^[ \t]+/m', static fn( $match ) => str_replace( "\t", '  ', $match[0] ), $html );
	$html = preg_replace( '/[ \t]+$/m', '', $html );
	$html = preg_replace(
		'/<!DOCTYPE html>\s*/i',
		"<!DOCTYPE html>\n<!-- Generated from WordPress by script-static-export.php. -->\n",
		$html,
		1
	);

	if ( str_contains( $html, 'localhost:' ) ) {
		throw new RuntimeException( 'A localhost URL remains in a generated page.' );
	}

	if ( preg_match( '~\bhref=["\']/\#~i', $html ) ) {
		throw new RuntimeException( 'A root-relative fragment link remains in a generated page.' );
	}

	if ( preg_match( '~<(?:script|link)\b[^>]*(?:wp-includes/js|wp-json|xmlrpc\.php)~i', $html ) ) {
		throw new RuntimeException( 'A server-only WordPress dependency remains in a generated page.' );
	}

	if ( ! str_contains( $html, 'data-static-export="true"' ) || ! str_contains( $html, 'turkey-signature-site-css' ) ) {
		throw new RuntimeException( 'A generated page is missing required static markers or theme styles.' );
	}

	return $html;
};

try {
	$front_html = $fetch_local( '/', 30 );
	$front_html = $transform_page( $front_html, './' );

	if ( false === file_put_contents( $output, $front_html ) ) {
		throw new RuntimeException( 'The static index could not be written.' );
	}

	$pages_json = $fetch_local( '/wp-json/wp/v2/pages?per_page=100&status=publish&_fields=id,link,slug,parent' );
	$pages      = json_decode( $pages_json, true, 512, JSON_THROW_ON_ERROR );
	$root_dir   = dirname( $output );
	$static_dir = $root_dir . '/static-pages';
	$page_count = 0;

	if ( ! is_dir( $static_dir ) && ! mkdir( $static_dir, 0775, true ) && ! is_dir( $static_dir ) ) {
		throw new RuntimeException( 'The static-pages directory could not be created.' );
	}

	foreach ( $pages as $page ) {
		$slug   = (string) ( $page['slug'] ?? '' );
		$parent = (int) ( $page['parent'] ?? 0 );
		$link   = (string) ( $page['link'] ?? '' );

		if ( 'home' === $slug || 0 !== $parent ) {
			continue;
		}

		if ( '' === $slug || ! preg_match( '/^[a-z0-9-]+$/', $slug ) ) {
			throw new RuntimeException( sprintf( 'Page slug "%s" is not safe for static export.', $slug ) );
		}

		$path = (string) parse_url( $link, PHP_URL_PATH );
		if ( '' === $path ) {
			throw new RuntimeException( sprintf( 'Page "%s" has no valid path.', $slug ) );
		}

		$page_html = $transform_page( $fetch_local( $path ), '../' );
		$page_dir  = $static_dir . '/' . $slug;

		if ( ! is_dir( $page_dir ) && ! mkdir( $page_dir, 0775, true ) && ! is_dir( $page_dir ) ) {
			throw new RuntimeException( sprintf( 'Static directory for "%s" could not be created.', $slug ) );
		}

		if ( false === file_put_contents( $page_dir . '/index.html', $page_html ) ) {
			throw new RuntimeException( sprintf( 'Static page "%s" could not be written.', $slug ) );
		}

		++$page_count;
	}

	if ( false === file_put_contents( $root_dir . '/.nojekyll', '' ) ) {
		throw new RuntimeException( 'The .nojekyll marker could not be written.' );
	}

	printf( "Static front page and %d published pages created.\n", $page_count );
} catch ( Throwable $error ) {
	fwrite( STDERR, $error->getMessage() . "\n" );
	exit( 1 );
}
