<?php

defined('ABSPATH') ?: exit();

// custom comparison function
function sbwc_sales_compare($a, $b) {
    if ($a['total_qty'] == $b['total_qty']) {
        return 0;
    }

    return ($a['total_qty'] > $b['total_qty']) ? -1 : 1;
}

/**
 * Render the sales report table
 *
 * @param array $sales_data
 * @return void
 */
function render_sales_table($sales_data) {

    uasort($sales_data, 'sbwc_sales_compare');

?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <!-- product ID -->
                <th scope="col" class="manage-column column-ID sortable" data-sort="id" title="<?php _e('Click to sort by ID', 'sbwc-sales'); ?>">
                    <?php _e('Product ID', 'sbwc-sales'); ?>
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

                <!-- sales by country -->
                <th scope="col" class="manage-column column-country sortable" data-sort="country" title="<?php _e('Click to sort by country', 'sbwc-sales'); ?>">
                    <?php _e('Sales by Country', 'sbwc-sales'); ?>
                </th>
            </tr>
        </thead>
        <tbody id="sbwc-sales-body">
            <?php if (!empty($sales_data)) : ?>
                <?php foreach ($sales_data as $pid => $data) : ?>
                    <tr>
                        <td class="id"><?php echo $pid; ?></td>
                        <td class="image"><img src="<?php echo get_the_post_thumbnail_url($pid, 'thumbnail'); ?>" alt="Product Image"></td>
                        <td class="product-name"><?php echo get_the_title($pid); ?></td>
                        <td class="total-sales-qty"><?php echo $data['total_qty']; ?></td>
                        <td class="country">
                            <?php

                            arsort($data);

                            // loop to display per country data
                            foreach ($data as $name => $qty) : ?>

                                <?php if ($name === 'total_qty') : ?>
                                    <?php continue; ?>
                                <?php endif; ?>

                                <?php $string = $name . ' - ' . $qty . ' units'; ?>

                                <p><?php _e($string, 'sbwc-sales') ?></p>

                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5">No sales data found for the specified time period.</td>
                </tr>
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
    </style>
<?php }
?>