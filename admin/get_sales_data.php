<?php

defined('ABSPATH') ?: exit();

/**
 * Get sales data
 *
 * @param string $period
 * @return void
 */
function get_sales_data($period) {

    // Set the date range based on the selected period
    switch ($period) {
        case 'last_day':
            $days_before = 1;
            break;
        case 'last_week':
            $days_before = 7;
            break;
        case 'last_month':
            $days_before = 30;
            break;
        default:
            $days_before = 1;
            break;
    }

    global $wpdb;

    $start_date = date('Y-m-d H:i:s', strtotime('-' . $days_before . ' days'));  // X days before today
    $end_date   = date('Y-m-d H:i:s');                                           // Today's date

    // Prep query
    $query = $wpdb->prepare("
    SELECT
        COALESCE(oim_variation.meta_value, oim_product.meta_value) AS product_id,
        pm.meta_value AS sku,
        SUM(om.meta_value) AS total_items_sold
    FROM
        {$wpdb->prefix}woocommerce_order_items AS oi
        INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim ON oi.order_item_id = oim.order_item_id
        INNER JOIN {$wpdb->prefix}postmeta AS pm ON oim.meta_value = pm.post_id
        INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS om ON oi.order_item_id = om.order_item_id
        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim_variation ON oi.order_item_id = oim_variation.order_item_id
            AND oim_variation.meta_key = '_variation_id'
        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim_product ON oi.order_item_id = oim_product.order_item_id
            AND oim_product.meta_key = '_product_id'
    WHERE
    (oim_variation.meta_value > 0 OR oim_product.meta_value > 0)
        AND pm.meta_key = '_sku'
        AND om.meta_key = '_qty'
        AND oi.order_item_type = 'line_item'
        AND oi.order_id IN (
            SELECT ID
            FROM {$wpdb->prefix}posts
            WHERE post_type = 'shop_order'
                AND post_status IN ('wc-completed', 'wc-processing')
                AND post_date >= %s
                AND post_date <= %s
        )
    GROUP BY
        product_id, pm.meta_value
", $start_date, $end_date);

    // execute query
    try {
        //code...
        $shop_orders = $wpdb->get_results($query);

        // error_log('Shop orders returned: ' . print_r($shop_orders, true));
        // error_log('Last query string: ' . $wpdb->last_query);
        // error_log('Last query error: ' . $wpdb->last_error);
    } catch (\Throwable $th) {
        error_log('[SBWC TOTAL SALES] Error with $wpdb query for shop orders: ' . $th->getMessage());
    }

    // check for errors
    if (isset($shop_orders->request) && $wpdb->last_error) :
        error_log('[SBWC TOTAL SALES] Error with total sales data query: ' . $wpdb->last_error);
    endif;

    // holds product sales data to return
    $to_return = [];

    if (is_array($shop_orders) || is_object($shop_orders) && !empty($shop_orders)) :
        foreach ($shop_orders as $obj) :

            // Retrieve product id, thumb id, and thumb URL
            $product_id     = wc_get_product_id_by_sku($obj->sku);
            $prod_parent_id = wp_get_post_parent_id($product_id);
            $thumb_id       = get_post_meta($product_id, '_thumbnail_id', true) ? get_post_meta($product_id, '_thumbnail_id', true) : get_post_meta($prod_parent_id, '_thumbnail_id', true);
            $thumb_src      = $thumb_id ? wp_get_attachment_url($thumb_id) : null;

            // Check if the product is a variable product
            if (get_post_type($product_id) === 'product_variation') :

                // Variable product - include sales figures for variations only
                $existing_index = null;

                foreach ($to_return as $index => $item) :
                    if ($item['pid'] === $product_id) :
                        $existing_index = $index;
                        break;
                    endif;
                endforeach;

                // If 'pid' already exists, sum 'total_items_sold'
                if ($existing_index !== null) :
                    $to_return[$existing_index]['total_items_sold'] += $obj->total_items_sold;
                else :
                    // Push relevant data to return array
                    $to_return[] = [
                        'pid'              => $product_id,
                        'sku'              => $obj->sku,
                        'total_items_sold' => $obj->total_items_sold,
                        'thumb_src'        => $thumb_src,
                        'prod_title'       => html_entity_decode(get_the_title($product_id))
                    ];
                endif;
            elseif (get_post_type($product_id) === 'product') :

                // Simple product - include sales figures for the parent product only
                $existing_index = null;

                foreach ($to_return as $index => $item) :
                    if ($item['pid'] === $prod_parent_id) :
                        $existing_index = $index;
                        break;
                    endif;
                endforeach;

                // If 'pid' already exists, sum 'total_items_sold'
                if ($existing_index !== null) :
                    $to_return[$existing_index]['total_items_sold'] += $obj->total_items_sold;
                else :
                    // Push relevant data to return array
                    $to_return[] = [
                        'pid'              => $prod_parent_id,
                        'sku'              => $obj->sku,
                        'total_items_sold' => $obj->total_items_sold,
                        'thumb_src'        => $thumb_src,
                        'prod_title'       => html_entity_decode(get_the_title($prod_parent_id))
                    ];
                endif;
            endif;
        endforeach;
    endif;

    // error_log('Return product data: ' . print_r($to_return, true));

    return $to_return;
}
