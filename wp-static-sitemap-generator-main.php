<?php

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

    if (!mkdir($concurrentDirectory = ABSPATH . 'xml-sitemap') && !is_dir($concurrentDirectory)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
    }
}

register_activation_hook( WP_STATIC_SITEMAP_GENERATOR_FILE, 'wp_static_sitemap_generator_activate' );

function wpssg_generator_run()
{
    require_once plugin_dir_path( __FILE__ ) . 'inc/class-wpssg.php';

    $generator = new WPSSG(
        ABSPATH .'static-sitemap.xml',
        ABSPATH .'xml-sitemap/sitemap%d.xml',
        home_url().'/xml-sitemap/sitemap%d.xml',
        1000,
        1,
    );

    $generator->generate();

    if ( class_exists( 'WP_CLI' ) ) {
        WP_CLI::success('Success.');
    }
}

add_action( 'wp_ajax_wpssg_ajax_generate', 'wpssg_ajax_generate' );

function wpssg_ajax_generate()
{
    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';

    if ( ! wp_verify_nonce( $nonce, 'wpssg-ajax' ) ) {
        wp_send_json( array(
            'status'  => 'error',
            'title'   => 'Error',
            'message' => 'Nonce verification failed',
        ) );
        wp_die();
    }

    wpssg_generator_run();

    wp_send_json( array(
        'status'  => 'success',
        'title'   => 'Success',
        'message' => 'Success.',
    ) );
    wp_die();
}


/**
 * Add Admin page
 */
add_action("wp_loaded", "wp_static_sitemap_generator_loaded");

function wp_static_sitemap_generator_loaded(){
    require_once plugin_dir_path( __FILE__ ) . 'inc/class-admin.php';
}

/**
 * Add Cron task
 */
if ( ! wp_next_scheduled( 'videostats_cron_hook' ) ) {
    wp_schedule_event( time(), 'daily', 'wpssg_cron_hook' );
}

add_action( 'wpssg_cron_hook', 'wpssg_cron_exec' );
function wpssg_cron_exec() {
    wpssg_generator_run();
}

if ( class_exists( 'WP_CLI' ) ) {
    WP_CLI::add_command('wpssg_generate', 'wpssg_generator_run');
}

//add_action( 'publish_post', 'wp_static_sitemap_generate_sitemap', 10, 3 );
//add_action( 'delete_post', 'wp_static_sitemap_generate_sitemap', 10, 2 );
