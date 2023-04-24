<?php

defined('ABSPATH') ?: exit();

/**
 * Get country based sales data
 */
function sbwc_ts_return_country_orders() {

    // grab subbed country data
    $country = isset($_GET['country']) ? $_GET['country'] : '';
    $period  = isset($_GET['period']) ? $_GET['period'] : '';

    // determine correct period to return
    switch ($period) {
        case 'day':
            $start_date = strtotime('-24 hours');
            break;
        case 'week':
            $start_date = strtotime('-1 week');
            break;
        case 'month':
            $start_date = strtotime('-1 month');
            break;
        default:
            $start_date = 0;
            break;
    }

    // setup request args
    $args = array(
        'limit'            => 50,
        'status'           => array('completed', 'processing'),
        'shipping_country' => $country,
        'date_query'       => array(
            array(
                'after' => date('Y-m-d', $start_date),
            ),
        ),
    );

    // retrieve sales
    $sales = wc_get_orders($args);

    // return sales if found, else false
    if (is_object($sales) || is_array($sales)) :
        return $sales;
    else :
        return false;
    endif;
}
