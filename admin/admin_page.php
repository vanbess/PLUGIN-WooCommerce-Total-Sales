<?php

defined('ABSPATH') ?: exit();

// include required function
include __DIR__ . '/get_sales_data.php';
include __DIR__ . '/render_sales_table.php';

/**
 * Enqueue jQuery and tablesorter scripts
 *
 * @return void
 */
function enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('tablesorter', SBWC_SALES_URI . 'assets/tablesorter.min.js', array('jquery'), '2.31.3', true);
}
add_action('admin_enqueue_scripts', 'enqueue_scripts');

/**
 * Add a custom admin menu
 *
 * @return void
 */
function add_custom_menu() {
    add_menu_page(
        __('SBWC Sales Report', 'sbwc-sales'),
        __('SBWC Sales Report', 'sbwc-sales'),
        'manage_options',
        'sales-report',
        'sales_report_page',
        'dashicons-chart-bar',
        25
    );
}
add_action('admin_menu', 'add_custom_menu');

/**
 * Render the sales report page
 *
 * @return void
 */
function sales_report_page() {
?>
    <div class="wrap">

        <!-- heading -->
        <h1 class="wp-heading-inline sbwc-sales-headng">SBWC Sales Report</h1>

        <!-- nav wrap -->
        <nav class="nav-tab-wrapper">

            <!-- last 24 hours -->
            <a id="last_day_link" href="?page=sales-report&period=day" class="nav-tab <?php echo (isset($_GET['period']) && $_GET['period'] == 'day' && !isset($_GET['type'])) ? 'nav-tab-active' : ''; ?>">
                <?php _e('Last 24 Hours', 'sbwc-sales'); ?>
            </a>

            <!-- last 7 days -->
            <a id="last_week_link" href="?page=sales-report&period=week" class="nav-tab <?php echo (isset($_GET['period']) && $_GET['period'] == 'week' && !isset($_GET['type'])) ? 'nav-tab-active' : ''; ?>">
                <?php _e('Last 7 Days', 'sbwc-sales'); ?>
            </a>

            <!-- last month -->
            <a id="last_month_link" href="?page=sales-report&period=month" class="nav-tab <?php echo (isset($_GET['period']) && $_GET['period'] == 'month' && !isset($_GET['type'])) ? 'nav-tab-active' : ''; ?>">
                <?php _e('Last Month', 'sbwc-sales'); ?>
            </a>

            <!-- per country -->
            <a id="per_country_link" href="?page=sales-report&period=month&type=per_country" class="nav-tab <?php echo (isset($_GET['period']) && $_GET['period'] == 'month' && $_GET['type'] == 'per_country') ? 'nav-tab-active' : ''; ?>">
                <?php _e('Per Country', 'sbwc-sales'); ?>
            </a>
        </nav>

        <?php

        $country_data = [];

        // Get sales data based on selected period
        switch ($_GET['period']) {
            case 'day':
                $sales_data = get_sales_data('last_day');
                break;
            case 'week':
                $sales_data = get_sales_data('last_week');
                break;
            case 'month':
                $sales_data = get_sales_data('last_month');
                break;
            default:
                $sales_data = get_sales_data('last_day');
                break;
        }
        // Render the sales report table (standard sales)
        render_sales_table($sales_data);
        ?>
    </div>
<?php
}
