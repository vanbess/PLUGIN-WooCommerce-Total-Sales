<?php

defined('ABSPATH') ?: exit();

/**
 * Get sales data
 *
 * @param string $period
 * @return void
 */
function get_sales_data($period) {
    global $wpdb;

    // Set the date range based on the selected period
    switch ($period) {
        case 'last_day':
            $start_date = date('Y-m-d h:i:s', strtotime('-1 day'));
            break;
        case 'last_week':
            $start_date = date('Y-m-d h:i:s', strtotime('-1 week'));
            break;
        case 'last_month':
            $start_date = date('Y-m-d h:i:s', strtotime('-1 month'));
            break;
        default:
            $start_date = date('Y-m-d h:i:s', strtotime('-1 day'));
            break;
    }

    // define end date
    $end_date = date('Y-m-d h:i:s');

    // Query orders
    $shop_orders = new WP_Query([
        'post_type'      => 'shop_order',
        'posts_per_page' => 50,
        'post_status'    => ['wc-completed', 'wc-processing'],
        'fields'         => 'ids',
        'date_query' => [
            'after'     => $start_date,
            'before'    => $end_date,
            'inclusive' => true
        ]
    ]);

    // check for errors
    if (isset($shop_orders->request) && $wpdb->last_error) :
        error_log('Error with query: ' . $wpdb->last_error);
    endif;

    // holds order ids array
    $order_ids = [];

    // check if shop orders returned, else bail with empty results
    if ($shop_orders->have_posts()) :

        // error_log('Shop orders returned: '.print_r($shop_orders, true));

        $order_ids = $shop_orders->posts;

        // error_log('Order IDs returned: ' . print_r($order_ids, true));

        wp_reset_postdata();
    else :

        error_log('No orders returned: ' . $wpdb->last_query);

        if ($wpdb->last_error) :
            error_log('WPDB error returned: ' . $wpdb->last_error);
        endif;

    endif;

    // holds product sales data for specified period
    $products_sales_data = [];

    // if $order_ids not empty
    if (!empty($order_ids)) :

        foreach ($order_ids as $order_id) :

            // get order object
            $order_data = wc_get_order($order_id);

            // get order products
            $order_products = $order_data->get_items();

            // loop through products, retrieving required data and pushing said data to $products_sales_data
            foreach ($order_products as $item_id => $item) :

                // get the product object
                $product = $item->get_product();

                // check if the product is a variation
                if ($product->is_type('variation')) {
                    // if it's a variation, get the variation ID
                    $product_id = $item->get_variation_id();
                } else {
                    // if it's not a variation, get the product ID directly
                    $product_id = $product->get_id();
                }

                // get sold qty
                $sold_qty   = intval($item->get_quantity());

                if (isset($products_sales_data[$product_id])) :
                    $products_sales_data[$product_id]['total_qty'] += $sold_qty;
                else :
                    $products_sales_data[$product_id] = array(
                        'total_qty' => $sold_qty,
                    );
                endif;

            endforeach;

        endforeach;

    endif;

    // error_log('Product sales data: ' . print_r($products_sales_data, true));

    return $products_sales_data;
}
