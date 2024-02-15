<?php
/**
 * Static XML sitemap Plugin.
 *
 * @wordpress-plugin
 * Plugin Name: Static XML sitemap
 * Version:     1.0.0
 * Description: Generates static xml files.
 * Author:      Aleksandr Shatskikh
 * Author URI:  https://errand.ru
 * Text Domain: static-sitemap
 * License:     GPL v3
 * Requires at least: 6.3
 * Requires PHP: 7.2.5
 *
 */

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! defined( 'STATIC_SITEMAP_FILE' ) ) {
	define( 'STATIC_SITEMAP_FILE', __FILE__ );
}

// Load the Yoast SEO plugin.
require_once dirname( STATIC_SITEMAP_FILE ) . '/static-sitemap-main.php';
