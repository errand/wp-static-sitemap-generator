<?php

add_action('admin_menu', 'static_sitemap_dashboard');

function static_sitemap_dashboard(){
    add_menu_page( 'static_sitemap_dashboard-dashboard', 'Static Sitemap', 'manage_options', 'static_sitemap_dashboard-dashboard', 'static_sitemap_dashboard_page','dashicons-networking',58);
}

function static_sitemap_dashboard_page(){
    include( WP_PLUGIN_DIR.'/'.plugin_dir_path(WP_STATIC_SITEMAP_GENERATOR_BASENAME) . 'views/dashboard.php');
}
