<?php

// class to add admin submenu page

class WPSSG_Admin_Page {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'wpssg_admin_menu' ] );
        // enqueue admin assets
        add_action( 'admin_enqueue_scripts', [ $this, 'wpssg_admin_assets' ] );
    }

    /**
     * Enqueue admin scripts
     */
    public function wpssg_admin_assets($screen) {
        if( $screen === 'settings_page_wpssg' ){
            wp_enqueue_script( 'wpssg', plugins_url( 'assets/admin.js', __DIR__), array( 'jquery' ), '1.0.0', true );
            wp_localize_script( 'wpssg', 'WPSSG',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce'    => wp_create_nonce( 'wpssg-ajax' )
                )
            );
        }
    }

    /**
     * Add admin menu
     */
    public function wpssg_admin_menu() {
        add_submenu_page(
            'options-general.php',
            __( 'Static Sitemap Generator', 'wp_static-sitemap_generator' ),
            __( 'Static Sitemap Generator', 'wp_static-sitemap_generator' ),
            'manage_options',
            'wpssg',
            [ $this, 'wpssg_dashboard_page' ]
        );
    }

    /**
     * Admin page
     */
    function wpssg_dashboard_page(){
        include( WP_PLUGIN_DIR.'/'.plugin_dir_path(WP_STATIC_SITEMAP_GENERATOR_BASENAME) . 'views/dashboard.php');
    }
}

new WPSSG_Admin_Page();



