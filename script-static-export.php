<?php
/**
 * Build a GitHub Pages snapshot from the current WordPress front page.
 *
 * This script runs inside the WordPress container so it can reuse the exact
 * HTML and core block styles produced by the installed WordPress version.
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

$html        = false;
$status_code = 0;

for ( $attempt = 0; $attempt < 30; $attempt++ ) {
	$http_response_header = array();
	$html                 = @file_get_contents( 'http://127.0.0.1/', false, $context );
	$status_line          = $http_response_header[0] ?? '';

	if ( preg_match( '/\s(\d{3})\s/', $status_line, $matches ) ) {
		$status_code = (int) $matches[1];
	}

	if ( false !== $html && 200 === $status_code ) {
		break;
	}

	sleep( 1 );
}

if ( false === $html || 200 !== $status_code ) {
	fwrite( STDERR, "The WordPress front page did not return HTTP 200.\n" );
	exit( 1 );
}

$navigation_css_path = '/var/www/html/wp-includes/blocks/navigation/style.min.css';
$navigation_css      = @file_get_contents( $navigation_css_path );

if ( false === $navigation_css || '' === trim( $navigation_css ) ) {
	fwrite( STDERR, "The WordPress navigation stylesheet was not found.\n" );
	exit( 1 );
}

$navigation_pattern = '~<link\b(?=[^>]*\bid=["\']wp-block-navigation-css["\'])[^>]*>\s*~i';
$html               = preg_replace_callback(
	$navigation_pattern,
	static fn() => "<style id=\"wp-block-navigation-css\">\n{$navigation_css}\n</style>\n",
	$html,
	1,
	$navigation_replacements
);

if ( 1 !== $navigation_replacements ) {
	fwrite( STDERR, "The WordPress navigation stylesheet link was not found in the page.\n" );
	exit( 1 );
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

$html = str_replace( $source_url . '/', './', $html );
$html = str_replace( $source_url, '.', $html );
$html = preg_replace( '/<html\b/', '<html data-static-export="true"', $html, 1 );
$html = preg_replace( '/[ \t]+$/m', '', $html );
$html = preg_replace(
	'/<!DOCTYPE html>\s*/i',
	"<!DOCTYPE html>\n<!-- Generated from WordPress by script-static-export.php. -->\n",
	$html,
	1
);

if ( str_contains( $html, 'localhost:' ) ) {
	fwrite( STDERR, "A localhost URL remains in the generated page.\n" );
	exit( 1 );
}

if ( preg_match( '~<(?:script|link)\b[^>]*(?:wp-includes/js|wp-json|xmlrpc\.php)~i', $html ) ) {
	fwrite( STDERR, "A server-only WordPress dependency remains in the generated page.\n" );
	exit( 1 );
}

if ( ! str_contains( $html, 'data-static-export="true"' ) || ! str_contains( $html, 'turkey-signature-site-css' ) ) {
	fwrite( STDERR, "The generated page is missing required static markers or theme styles.\n" );
	exit( 1 );
}

if ( false === file_put_contents( $output, $html ) ) {
	fwrite( STDERR, "The static index could not be written.\n" );
	exit( 1 );
}

$nojekyll_path = dirname( $output ) . '/.nojekyll';
if ( false === file_put_contents( $nojekyll_path, '' ) ) {
	fwrite( STDERR, "The .nojekyll marker could not be written.\n" );
	exit( 1 );
}

printf( "Static page created: %s (%d bytes)\n", $output, strlen( $html ) );
