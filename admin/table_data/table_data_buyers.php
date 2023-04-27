<?php

defined('ABSPATH') ?: exit();

/**
 * Fetch and return complete customer order count based on customer email address
 *
 * @param string $customer_email - billing email for customer
 * @return void
 */
function ts_query_customer_order_count_complete($customer_email) {

    $customer_orders_args = array(
        'post_type'      => 'shop_order',
        'post_status'    => ['wc-completed', 'wc-processing'],
        'meta_key'       => '_billing_email',
        'meta_value'     => $customer_email,
        'posts_per_page' => -1,
    );

    $customer_orders_query = new WP_Query($customer_orders_args);

    return $customer_orders_query->post_count;
}

/**
 * Fetch and return customer order count for last month based on customer email address
 *
 * @param string $customer_email - billing email for customer
 * @return void
 */
function ts_query_customer_order_count_last_month($customer_email) {

    $customer_orders_args = array(
        'post_type'      => 'shop_order',
        'post_status'    => ['wc-completed', 'wc-processing'],
        'meta_key'       => '_billing_email',
        'meta_value'     => $customer_email,
        'date_query'  => [
            [
                'after' => '30 days ago',
            ],
        ],
        'posts_per_page' => -1,
    );

    $customer_orders_query = new WP_Query($customer_orders_args);

    return $customer_orders_query->post_count;
}

/**
 * Renders table data related to return buyers (last month)
 *
 * @return void
 */
function ts_table_data_buyers() {

    // Shop order args - retrieve orders for last 30 days
    $args = [
        'post_type'   => 'shop_order',
        'post_status' => ['wc-completed', 'wc-processing'],
        'date_query'  => [
            [
                'after' => '30 days ago',
            ],
        ],
        'posts_per_page' => -1,
        'order'          => 'ASC',
        'orderby'        => 'date'
    ];

    $query = new WP_Query($args);

    // holds all results
    $results = [];

    // holds customer related data
    $customers = [];

    if ($query->have_posts()) :
        while ($query->have_posts()) :
            $query->the_post();

            // retrieve relevant order data
            $order_id       = get_the_ID();
            $order          = wc_get_order($order_id);
            $order_total    = $order->get_total();
            $order_currency = $order->get_currency();
            $order_date     = get_the_date('Y-m-d');
            $customer_id    = $order->get_customer_id();
            $customer_email = $order->get_billing_email();

            // generate customer key for storing customer metrics data
            $customer_key = $customer_email;

            if (!isset($customers[$customer_key])) {
                $customers[$customer_key] = [
                    'total_orders' => 0,
                    'last_order_date' => '',
                ];
            }

            // check if customer is return customer for month
            $is_return_customer_month = $customers[$customer_key]['last_order_date'] !== '' && (strtotime($order_date) - strtotime($customers[$customer_key]['last_order_date'])) <= (30 * 24 * 60 * 60);

            // check if is repeat purchase
            $is_repeat_purchase = $customers[$customer_key]['total_orders'] > 0;

            // calculate order total in USD if needed
            $order_total_usd = ($order_currency !== 'USD') ? number_format($order_total / (get_option('alg_currency_switcher_exchange_rate_USD_' . $order_currency)), 2, '.', '') : $order_total;

            // init data array
            $results[$order_date] = $results[$order_date] ?? [
                'new_customer_count'                 => 0,
                'unique_return_customer_count_month' => 0,
                'total_repeat_purchases'             => 0,
                'gross_sales_usd'                    => 0,
                'order_count'                        => 0,
            ];

            // if is return customer for month and is not repeate purchase
            if ($is_return_customer_month && !$is_repeat_purchase) {
                $results[$order_date]['unique_return_customer_count_month']++;
            }

            // if is repeate purchase
            if ($is_repeat_purchase) {
                $results[$order_date]['total_repeat_purchases']++;
            }

            // if is not repeat purchase
            if (!$is_repeat_purchase) {
                $results[$order_date]['new_customer_count']++;
            }

            // push total orders and last order date
            $customers[$customer_key]['total_orders']++;
            $customers[$customer_key]['last_order_date'] = $order_date;

            // calc gross sales amount and order count
            $results[$order_date]['gross_sales_usd'] += $order_total_usd;
            $results[$order_date]['order_count']++;

        endwhile;
    endif;

    wp_reset_postdata();

    // Calculate average order value
    foreach ($results as $date => $data) :
        $results[$date]['average_order_value_usd'] = '~' . number_format(($data['gross_sales_usd'] / $data['order_count']), 2, '.', '');
    endforeach;

    // echo '<pre>';
    // print_r($results);
    // echo '</pre>';

?>

    <?php if (!empty($results)) : ?>

        <?php foreach ($results as $date => $data) : ?>
            <tr>
                <td class="date"><?php echo $date; ?></td>
                <td class="no_of_orders"><?php echo $data['order_count']; ?></td>
                <td class="gross_sales"><?php echo $data['gross_sales_usd']; ?></td>
                <td class="avg_order_value"><?php echo $data['average_order_value_usd']; ?></td>
                <td class="unique_customer_return_rate"><?php echo $data['unique_return_customer_count_month']; ?></td>
                <td class="repeat_purchases"><?php echo $data['total_repeat_purchases']; ?></td>
                <td class="new_customers"><?php echo $data['new_customer_count']; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="7">No data to display</td>
        </tr>
    <?php endif; ?>

<?php }
