<?php

defined('ABSPATH') ?: exit();

/**
 * Renders table head for standard time based sales data
 *
 * @return void
 */
function ts_table_head_normal() { ?>
    <tr>
        <!-- product ID -->
        <th scope="col" class="manage-column column-ID" title="<?php _e('Click to sort by ID', 'sbwc-sales'); ?>">
            <?php _e('Product ID', 'sbwc-sales'); ?>
        </th>

        <!-- product SKU -->
        <th scope="col" class="manage-column column-SKU" title="<?php _e('Click to sort by SKU', 'sbwc-sales'); ?>">
            <?php _e('SKU', 'sbwc-sales'); ?>
        </th>

        <!-- product Image -->
        <th scope="col" class="manage-column column-image" title="<?php _e('Click to sort by image', 'sbwc-sales'); ?>">
            <?php _e('Product Image', 'sbwc-sales'); ?>
        </th>

        <!-- product name -->
        <th scope="col" class="manage-column column-product-name" title="<?php _e('Click to sort by product name', 'sbwc-sales'); ?>">
            <?php _e('Product Name', 'sbwc-sales'); ?>
        </th>

        <!-- total sales qty -->
        <th scope="col" class="manage-column column-total-sales-qty" title="<?php _e('Click to sort by sales QTY', 'sbwc-sales'); ?>">
            <?php _e('Total Sales QTY', 'sbwc-sales'); ?>
        </th>

    </tr>
<?php }