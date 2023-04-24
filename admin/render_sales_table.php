<?php

defined('ABSPATH') ?: exit();

// include country data function
include_once __DIR__ . '/get_sales_data_country.php';

// custom comparison function
function sbwc_sales_compare($a, $b) {
    if ($a['total_qty'] == $b['total_qty']) {
        return 0;
    }

    return ($a['total_qty'] > $b['total_qty']) ? -1 : 1;
}

/**
 * Country data related JS
 *
 * @return void
 */
function swbc_sales_countries_js() { ?>

    <!-- replace fetch link placeholders/enable/disable fetch button -->
    <script id="countries_period_select_js">
        jQuery(document).ready(function($) {

            // make sure our Per Country tab still has an active class
            if (!$('#per_country_link').hasClass('nav-tab-active')) {
                $('#per_country_link').addClass('nav-tab-active')
            }

            var baseUrl = '?page=sales-report&type=per_country';

            // check for both period and country vals on intial page load
            if ($('#period').val() !== '' && $('#country').val() !== '') {

                // Get the selected values
                var country = $('#country').val();
                var period = $('#period').val();

                // Update the href attribute of the link
                var url = baseUrl + '&country=' + country + '&period=' + period;
                $('#sales-report-link').attr('href', url).removeClass('button-disabled');

            } else {
                $('#sales-report-link').attr('href', '#').addClass('button-disabled');
            }

            // country/period select on change
            $('#country, #period').on('change', function() {

                var country = $('#country').val();
                var period = $('#period').val();

                if (country !== '' && period !== '') {
                    var url = baseUrl + '&country=' + country + '&period=' + period;
                    $('#sales-report-link').attr('href', url).removeClass('button-disabled');
                } else {
                    $('#sales-report-link').attr('href', '#').addClass('button-disabled');
                }
            });

        });
    </script>
    <?php }

/**
 * Render the sales report table
 *
 * @param array $sales_data
 * @return void
 */
function render_sales_table($sales_data) {

    // sort standard sales data from high to low
    uasort($sales_data, 'sbwc_sales_compare');

    // if Per Country tab is active, render country select and period dropdowns
    if (isset($_GET['type'])) :

        // retrieve WC countries list
        $country_class = new WC_Countries();
        $country_list  = $country_class->get_countries();
    ?>

        <div id="sbwc_sales_selectors_cont">

            <!-- country -->
            <label for="country"><?php _e('Select country:', 'sbwc-sales'); ?></label>
            <select name="country" id="country">
                <option value=""><?php _e('All', 'sbwc-sales'); ?></option>
                <?php foreach ($country_list as $code => $country) : ?>
                    <option value="<?php echo $code; ?>" <?php echo $_GET['country'] == $code ? 'selected' : '' ?>><?php echo $country; ?></option>
                <?php endforeach; ?>
            </select>

            <!-- period -->
            <label for="period"><?php _e('Select time period:', 'sbwc-sales'); ?></label>
            <select name="period" id="period" required>
                <option value=""><?php _e('please select...', 'sbwc-sales'); ?></option>
                <option value="day" <?php echo $_GET['period'] == 'day' ? 'selected' : '' ?>><?php _e('Last 24 hours', 'sbwc-sales'); ?></option>
                <option value="week" <?php echo $_GET['period'] == 'week' ? 'selected' : '' ?>><?php _e('Last 7 days', 'sbwc-sales'); ?></option>
                <option value="month" <?php echo $_GET['period'] == 'month' ? 'selected' : '' ?>><?php _e('Last month', 'sbwc-sales'); ?></option>
            </select>

            <!-- submit -->
            <a id="sales-report-link" href="?page=sales-report&type=per_country&country={country}&period={period}" class="button button-primary button-disabled" value="<?php _e('Fetch', 'sbwc-sales'); ?>"><?php _e('Fetch', 'sbwc-sales'); ?></a>

        </div>

        <?php swbc_sales_countries_js(); ?>

    <?php endif; ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <!-- product ID -->
                <th scope="col" class="manage-column column-ID sortable" data-sort="id" title="<?php _e('Click to sort by ID', 'sbwc-sales'); ?>">
                    <?php _e('Product ID', 'sbwc-sales'); ?>
                </th>

                <!-- product SKU -->
                <th scope="col" class="manage-column column-ID sortable" data-sort="id" title="<?php _e('Click to sort by SKU', 'sbwc-sales'); ?>">
                    <?php _e('SKU', 'sbwc-sales'); ?>
                </th>

                <!-- product Image -->
                <th scope="col" class="manage-column column-image" title="<?php _e('Click to sort by image', 'sbwc-sales'); ?>">
                    <?php _e('Product Image', 'sbwc-sales'); ?>
                </th>

                <!-- product name -->
                <th scope="col" class="manage-column column-product-name sortable" data-sort="product-name" title="<?php _e('Click to sort by product name', 'sbwc-sales'); ?>">
                    <?php _e('Product Name', 'sbwc-sales'); ?>
                </th>

                <!-- total sales qty -->
                <th scope="col" class="manage-column column-total-sales-qty sortable" data-sort="total-sales-qty" title="<?php _e('Click to sort by sales QTY', 'sbwc-sales'); ?>">
                    <?php _e('Total Sales QTY', 'sbwc-sales'); ?>
                </th>

            </tr>
        </thead>
        <tbody id="sbwc-sales-body">
            <?php

            // if not per country sales report
            if (!isset($_GET['type'])) :

                $arg_count = count($_GET);

                if ($arg_count === 1) : ?>
                    <script>
                        jQuery(document).ready(function($) {
                            $('#last_day_link').addClass('nav-tab-active');
                        });
                    </script>
                <?php endif;

                // if $sales_data not empty, loop and display in table 
                if (!empty($sales_data)) : ?>
                    <?php foreach ($sales_data as $pid => $data) : ?>
                        <tr>
                            <td class="id"><?php echo $pid; ?></td>
                            <td class="sku"><?php echo get_post_meta($pid, '_sku', true) ? get_post_meta($pid, '_sku', true) : 'N/A'; ?></td>
                            <td class="image"><img src="<?php echo get_the_post_thumbnail_url($pid, 'thumbnail'); ?>" alt="Product Image"></td>
                            <td class="product-name"><?php echo get_the_title($pid); ?></td>
                            <td class="total-sales-qty"><?php echo $data['total_qty']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php

                // if $sales_data empty
                else : ?>
                    <tr>
                        <td colspan="5">No sales data found for the specified time period.</td>
                    </tr>
                <?php endif; ?>
                <?php

                /*********************
                 * Country based data
                 *********************/
            else :

                // retrieve sales
                $sales = sbwc_ts_return_country_orders();

                // retrieve product related data via loop
                $country_data = array();

                foreach ($sales as $order) :

                    foreach ($order->get_items() as $item) :

                        // get the product object
                        $product = $item->get_product();

                        // check if the product is a variation
                        if ($product->is_type('variation')) {
                            // if it's a variation, get the variation ID
                            $product_id   = $item->get_variation_id();
                            $product_name = get_the_title($product_id);
                        } else {
                            // if it's not a variation, get the product ID directly
                            $product_id   = $product->get_id();
                            $product_name = get_the_title($product_id);
                        }

                        $product_image = wp_get_attachment_url(get_post_thumbnail_id($product_id));
                        $qty_sold      = $item->get_quantity();

                        if (isset($country_data[$product_id])) :
                            $country_data[$product_id]['total_qty'] += $qty_sold;
                        else :
                            $country_data[$product_id] = array(
                                'product_id'    => $product_id,
                                'sku'           => $product->get_sku(),
                                'product_title' => $product_name,
                                'product_image' => $product_image,
                                'total_qty'     => $qty_sold,
                            );
                        endif;

                    endforeach;
                endforeach;

                // sort sales data
                uasort($country_data, function ($a, $b) {
                    return $b['total_qty'] - $a['total_qty'];
                });

                // if $country_data not empty
                if (!empty($country_data)) : ?>
                    <?php foreach ($country_data as $pid => $data) : ?>
                        <tr>
                            <td class="id"><?php echo $pid; ?></td>
                            <td class="sku"><?php echo $data['sku']; ?></td>
                            <td class="image"><img src="<?php echo get_the_post_thumbnail_url($pid, 'thumbnail'); ?>" alt="Product Image"></td>
                            <td class="product-name"><?php echo get_the_title($pid); ?></td>
                            <td class="total-sales-qty"><?php echo $data['total_qty']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php

                // if $country_data empty
                else : ?>
                    <tr>
                        <td colspan="5">No sales data found for the specified time period and country.</td>
                    </tr>
                <?php endif; ?>

            <?php endif; ?>
        </tbody>
    </table>
    <script>
        jQuery(document).ready(function($) {
            // Add table sorting functionality
            $('.wp-list-table').tablesorter({
                headers: {
                    '.sortable': {
                        sorter: 'text'
                    }
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
    </style>
<?php }
?>