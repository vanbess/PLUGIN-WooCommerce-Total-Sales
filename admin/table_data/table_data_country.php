<?php

defined('ABSPATH') ?: exit();

/**
 * Renders sales data related to country (default all countries + last month)
 *
 * @param array $sales_data - country based sales data to be rendered
 * @return void
 */
function ts_table_data_country($sales_data) {
    
    // if $sales_data not empty, loop and display in table 
    if (!empty($sales_data)) : ?>
        <?php foreach ($sales_data as $data) : ?>
            <?php if ($data['pid'] == 0 || $data['pid'] == '0' || is_null($data['pid'])) : ?>
                <?php continue; ?>
            <?php endif; ?>
            <tr>
                <td class="id"><?php echo $data['pid']; ?></td>
                <td class="sku"><?php echo $data['sku']; ?></td>
                <td class="image"><img src="<?php echo $data['thumb_src']; ?>" alt="Product Image"></td>
                <td class="product-name"><?php echo $data['prod_title']; ?></td>
                <td class="total-sales-qty"><?php echo $data['total_items_sold']; ?></td>
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
