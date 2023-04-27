<?php

defined('ABSPATH') ?: exit();

/**
 * Renders table head for buyer specific data
 *
 * @return void
 */
function ts_table_head_buyers() { ?>

    <tr>
        <!-- date -->
        <th scope="col" class="manage-column buyers-data column-date" title="<?php _e('Click to sort by Date', 'sbwc-sales'); ?>">
            <?php _e('Date', 'sbwc-sales'); ?>
        </th>

        <!-- number of orders -->
        <th scope="col" class="manage-column buyers-data column-total-orders" title="<?php _e('Click to sort by number of orders', 'sbwc-sales'); ?>">
            <?php _e('Number of Orders', 'sbwc-sales'); ?>
        </th>

        <!-- gross sales -->
        <th scope="col" class="manage-column buyers-data column-gross-sales" title="<?php _e('Click to sort by gross sales', 'sbwc-sales'); ?>">
            <?php _e('Gross Sales (USD)', 'sbwc-sales'); ?>
        </th>

        <!-- avg order value -->
        <th scope="col" class="manage-column buyers-data column-avg-order-value" title="<?php _e('Click to sort by average order value', 'sbwc-sales'); ?>">
            <?php _e('Average Order Value (USD)', 'sbwc-sales'); ?>
        </th>

        <!-- unique return customer count -->
        <th scope="col" class="manage-column buyers-data column-unique-return-customer-count" title="<?php _e('Click to sort by unique return customer count ', 'sbwc-sales'); ?>">
            <?php _e('Unique Return Customer Count', 'sbwc-sales'); ?>
            <span class="help-tip" title="<?php _e('Total unique customers who returned to store on a given date, regardless of repeat purchases','sbwc-sales') ?>">?</span>
        </th>

        <!-- total repeate purchases -->
        <th scope="col" class="manage-column buyers-data column-total-repeat-purchases" title="<?php _e('Click to sort by total repeat purchases', 'sbwc-sales'); ?>">
            <?php _e('Total Repeat Purchases', 'sbwc-sales'); ?> 
            <span class="help-tip" title="<?php _e('Total repeat purcashes made on a given date','sbwc-sales') ?>">?</span>
        </th>
        
        <!-- new customer count -->
        <th scope="col" class="manage-column buyers-data column-new-customer-count " title="<?php _e('Click to sort by new customer count', 'sbwc-sales'); ?>">
            <?php _e('New Customer Count', 'sbwc-sales'); ?>
            <span class="help-tip" title="<?php _e('New customers who have purchased from store on a given date','sbwc-sales') ?>">?</span>
        </th>

    </tr>

<?php }