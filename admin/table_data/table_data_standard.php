<?php

defined('ABSPATH') ?: exit();

/**
 * Renders bog standard historical sales data per day/week/month
 *
 * @param array $sales_data - sales data to be rendered
 * @return void
 */
function ts_table_data_standard($sales_data) {

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
<?php endif;
}

?>