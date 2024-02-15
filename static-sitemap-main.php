<?php

use GpsLab\Component\Sitemap\Render\PlainTextSitemapRender;
use GpsLab\Component\Sitemap\Stream\WritingStream;
use GpsLab\Component\Sitemap\Url\ChangeFrequency;
use GpsLab\Component\Sitemap\Url\Url;
use GpsLab\Component\Sitemap\Writer\TempFileWriter;

if ( ! function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

if ( ! defined( 'STATIC_SITEMAP_PATH' ) ) {
    define( 'STATIC_SITEMAP_PATH', plugin_dir_path( STATIC_SITEMAP_FILE ) );
}

if ( ! defined( 'STATIC_SITEMAP_BASENAME' ) ) {
    define( 'STATIC_SITEMAP_BASENAME', plugin_basename( STATIC_SITEMAP_FILE ) );
}

$autoload_file = STATIC_SITEMAP_PATH . 'vendor/autoload.php';

if ( is_readable( $autoload_file ) ) {
    $autoloader = require $autoload_file;
}

function static_sitemap_activate()
{

    _static_sitemap_activate();

    // This is done so that the 'uninstall_{$file}' is triggered.
    register_uninstall_hook( STATIC_SITEMAP_FILE, '__return_false' );
}

function _static_sitemap_activate()
{
    do_action( 'static_sitemap_activate' );
    generate_sitemap();
}

function generate_sitemap()
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
            new \DateTimeImmutable($post->post_date), // lastmod
            ChangeFrequency::always(), // changefreq
            10 // priority
        );

        $urls[] = $url;
    }


    // file into which we will write a sitemap
    $filename = get_home_path().'/sitemap.xml';

    // configure stream
    $render = new PlainTextSitemapRender();
    $writer = new TempFileWriter();
    $stream = new WritingStream($render, $writer, $filename);

    // build sitemap.xml
    $stream->open();
    foreach ($urls as $url) {
        $stream->push($url);
    }
    $stream->close();
}

register_activation_hook( STATIC_SITEMAP_FILE, 'static_sitemap_activate' );

add_action("wp_loaded","static_sitemap_loaded");
function static_sitemap_loaded(){
    require_once plugin_dir_path( __FILE__ ) . 'inc/admin.php';
}