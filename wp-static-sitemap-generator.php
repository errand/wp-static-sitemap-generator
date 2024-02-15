<?php
/**
 * Static Static XML sitemap generator Plugin.
 *
 * @wordpress-plugin
 * Plugin Name: Static XML sitemap generator
 * Version:     1.0.0
 * Description: Generates static xml files.
 * Author:      Aleksandr Shatskikh
 * Author URI:  https://errand.ru
 * Text Domain: wp_static-sitemap_generator
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

if ( ! defined( 'WP_STATIC_SITEMAP_GENERATOR_FILE' ) ) {
	define( 'WP_STATIC_SITEMAP_GENERATOR_FILE', __FILE__ );
}

require_once dirname(WP_STATIC_SITEMAP_GENERATOR_FILE) . '/wp-static-sitemap-generator-main.php';
