<?php

/**
 * Plugin Name:       SBWC Sales Report
 * Description:       Sales figures for products based on various selectable time periods
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            WC Bessinger
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sbwc-sales
 */

defined('ABSPATH') || exit();

add_action('plugins_loaded', function () {
    
    define('SBWC_SALES_PATH', plugin_dir_path(__FILE__));
    define('SBWC_SALES_URI', plugin_dir_url(__FILE__));

    if(is_plugin_active('woocommerce/woocommerce.php')):
    
        // admin
        include SBWC_SALES_PATH . 'admin/admin_page.php';
        
    endif;

});
