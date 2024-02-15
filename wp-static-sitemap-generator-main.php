<?php

use GpsLab\Component\Sitemap\Render\PlainTextSitemapIndexRender;
use GpsLab\Component\Sitemap\Render\PlainTextSitemapRender;
use GpsLab\Component\Sitemap\Sitemap\Sitemap;
use GpsLab\Component\Sitemap\Stream\WritingSplitIndexStream;
use GpsLab\Component\Sitemap\Stream\WritingStream;
use GpsLab\Component\Sitemap\Url\ChangeFrequency;
use GpsLab\Component\Sitemap\Url\Url;
use GpsLab\Component\Sitemap\Writer\TempFileWriter;

if ( ! function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

if ( ! defined( 'WP_STATIC_SITEMAP_GENERATOR_PATH' ) ) {
    define( 'WP_STATIC_SITEMAP_GENERATOR_PATH', plugin_dir_path( WP_STATIC_SITEMAP_GENERATOR_FILE ) );
}

if ( ! defined( 'WP_STATIC_SITEMAP_GENERATOR_BASENAME' ) ) {
    define( 'WP_STATIC_SITEMAP_GENERATOR_BASENAME', plugin_basename( WP_STATIC_SITEMAP_GENERATOR_FILE ) );
}

$autoload_file = WP_STATIC_SITEMAP_GENERATOR_PATH . 'vendor/autoload.php';

if ( is_readable( $autoload_file ) ) {
    $autoloader = require $autoload_file;
}

function wp_static_sitemap_generator_activate()
{

    _wp_static_sitemap_generator_activate();

    // This is done so that the 'uninstall_{$file}' is triggered.
    register_uninstall_hook( WP_STATIC_SITEMAP_GENERATOR_FILE, '__return_false' );
}

function _wp_static_sitemap_generator_activate()
{
    do_action( 'wp_static_sitemap_generator_activate' );
    wp_static_sitemap_generate_sitemap();
}

function wp_static_sitemap_generate_sitemap()
{
    $args = array(
        'post_type' => array('post','page'),
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'ignore_sticky_posts' => true,
    );
    $qry = new WP_Query($args);

    $urls = [];

    foreach ($qry->posts as $post) {
        $url = Url::create(
            get_permalink($post->ID), // loc
            new \DateTimeImmutable(date('c', get_post_timestamp($post->ID))), // lastmod
            ChangeFrequency::always(), // changefreq
            10 // priority
        );

        $urls[] = $url;
    }


    // file into which we will write a sitemap
    $index_filename = ABSPATH .'static-sitemap.xml';
    $part_filename = ABSPATH .'xml-sitemap/sitemap%d.xml';
    $part_web_path = home_url().'/xml-sitemap/sitemap%d.xml';

    $index_render = new PlainTextSitemapIndexRender();
    $index_writer = new TempFileWriter();

    $part_render = new PlainTextSitemapRender();
    $part_writer = new TempFileWriter();

    $stream = new WritingSplitIndexStream(
        $index_render,
        $part_render,
        $index_writer,
        $part_writer,
        $index_filename,
        $part_filename,
        $part_web_path
    );

    // build sitemap
    $stream->open();
    foreach ($urls as $url) {
        $stream->push($url);
    }

    $stream->close();
}

register_activation_hook( WP_STATIC_SITEMAP_GENERATOR_FILE, 'wp_static_sitemap_generator_activate' );

/*add_action("wp_loaded","wp_static_sitemap_generator_loaded");
function wp_static_sitemap_generator_loaded(){
    require_once plugin_dir_path( __FILE__ ) . 'inc/admin.php';
}*/

add_action( 'publish_post', 'wp_static_sitemap_generate_sitemap', 10, 3 );
add_action( 'delete_post', 'wp_static_sitemap_generate_sitemap', 10, 2 );