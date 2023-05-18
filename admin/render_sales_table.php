<?php

defined('ABSPATH') ?: exit();

// include country data function
include_once __DIR__ . '/get_sales_data_country.php';
include_once __DIR__ . '/table_data/country_selectors.php';
include_once __DIR__ . '/table_data/table_head_normal.php';
include_once __DIR__ . '/table_data/table_head_buyers.php';
include_once __DIR__ . '/table_data/table_data_standard.php';
include_once __DIR__ . '/table_data/table_data_country.php';
include_once __DIR__ . '/table_data/table_data_buyers.php';

/**
 * Render the sales report table
 *
 * @param array $sales_data
 * @return void
 */
function render_sales_table($sales_data) {

    // sort standard sales data from high to low
    uasort($sales_data, function ($a, $b) {
        if ($a['total_items_sold'] == $b['total_items_sold']) {
            return 0;
        }
        return ($a['total_items_sold'] > $b['total_items_sold']) ? -1 : 1;
    });

    // country based data filter dropdowns
    ts_render_country_selectors();

?>

    <table class="wp-list-table widefat fixed striped">

        <thead>
            <?php
            // table head for data not related to buyers
            if ($_GET['type'] !== 'return_buyers') :
                ts_table_head_normal();

            // buyer related table head
            else :
                ts_table_head_buyers();
            endif;
            ?>
        </thead>

        <tbody id="sbwc-sales-body">

            <?php

            /**********************
             * Standard sales data
             **********************/
            if (!isset($_GET['type'])) :

                ts_table_data_standard($sales_data);

                /*********************
                 * Country based data
                 *********************/
            elseif (isset($_GET['type']) && $_GET['type'] === 'per_country') :

                // retrieve country based sales
                $country_data = sbwc_ts_return_country_orders();

                // sort standard sales data from high to low
                uasort($country_data, function ($a, $b) {
                    if ($a['total_items_sold'] == $b['total_items_sold']) {
                        return 0;
                    }
                    return ($a['total_items_sold'] > $b['total_items_sold']) ? -1 : 1;
                });

                // render sales data
                ts_table_data_country($country_data);

                /*******************
                 * Buyer based data
                 *******************/
            else :

                ts_table_data_buyers();

            endif; ?>
        </tbody>
    </table>
    <script>
        jQuery(document).ready(function($) {
            // Add table sorting functionality
            $('.wp-list-table').tablesorter({
                headers: {
                    sorter: 'text'
                }
            });
        });
    </script>

    <style>
        .tablesorter-header-inner {
            text-align: center;
            color: #2271b1;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            line-height: 2;
        }

        a.nav-tab.nav-tab-active {
            background: #2271b1;
            color: white;
            border: 1px solid #2271b1;
        }

        h1.wp-heading-inline.sbwc-sales-headng {
            width: 99.2%;
            background: white;
            padding: 0.6em 1.2em;
            margin-top: -10px;
            margin-left: -22px;
            margin-bottom: 0.75em;
            margin-right: 0;
            box-shadow: 0px 2px 5px lightgrey;
            text-shadow: 0px 0px 2px lightgrey;
        }

        td.country p {
            font-size: 14px;
        }

        tbody#sbwc-sales-body tr td {
            font-size: 14px;
            vertical-align: middle;
        }

        tbody#sbwc-sales-body {
            text-align: center;
        }

        td.image>img {
            max-width: 100px;
            border: 1px solid #dddddd;
            border-radius: 3px;
        }

        div#sbwc_sales_selectors_cont {
            padding: 1em;
        }

        select#period {
            margin-right: 1em;
        }

        select#country {
            margin-right: 1em;
        }

        div#sbwc_sales_selectors_cont label {
            font-weight: 500;
            font-style: italic;
            margin-right: 1em;
        }

        th.buyers-data .tablesorter-header-inner {
            font-size: 13px !important;
        }

        span.help-tip {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1px solid;
            text-align: center;
            line-height: 1;
            border-radius: 100%;
            margin-left: 4px;
            color: #666;
        }
    </style>
<?php }
?>