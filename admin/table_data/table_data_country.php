<?php

defined('ABSPATH') ?: exit();

/**
 * Renders sales data related to country (default all countries + last month)
 *
 * @param array $sales - country based sales data to be rendered
 * @return void
 */
function ts_table_data_country($sales) {
    
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
<?php endif;
}
