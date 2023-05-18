<?php

defined('ABSPATH') ?: exit();

/**
 * Renders country selectors
 *
 * @return void
 */
function ts_render_country_selectors() {

    // if Per Country tab is active, render country select and period dropdowns
    if (isset($_GET['type']) && $_GET['type'] === 'per_country') :

        // retrieve WC countries list
        $country_class = new WC_Countries();
        $country_list  = $country_class->get_countries();
?>

        <div id="sbwc_sales_selectors_cont">

            <!-- country -->
            <label for="country"><?php _e('Select country:', 'sbwc-sales'); ?></label>
            <select name="country" id="country">
                <option value=""><?php _e('All', 'sbwc-sales'); ?></option>
                <?php foreach ($country_list as $code => $country) : ?>
                    <option value="<?php echo $country; ?>" <?php echo $_GET['country'] == $country ? 'selected' : '' ?>><?php echo $country; ?></option>
                <?php endforeach; ?>
            </select>

            <!-- period -->
            <label for="period"><?php _e('Select time period:', 'sbwc-sales'); ?></label>
            <select name="period" id="period" required>
                <option value=""><?php _e('please select...', 'sbwc-sales'); ?></option>
                <option value="day" <?php echo $_GET['period'] == 'day' ? 'selected' : '' ?>><?php _e('Last 24 hours', 'sbwc-sales'); ?></option>
                <option value="week" <?php echo $_GET['period'] == 'week' ? 'selected' : '' ?>><?php _e('Last 7 days', 'sbwc-sales'); ?></option>
                <option value="month" <?php echo $_GET['period'] == 'month' ? 'selected' : '' ?>><?php _e('Last month', 'sbwc-sales'); ?></option>
            </select>

            <!-- submit -->
            <a id="sales-report-link" href="?page=sales-report&type=per_country&country={country}&period={period}" class="button button-primary button-disabled" value="<?php _e('Fetch', 'sbwc-sales'); ?>"><?php _e('Fetch', 'sbwc-sales'); ?></a>

        </div>

        <script id="countries_period_select_js">
            jQuery(document).ready(function($) {

                // make sure our Per Country tab still has an active class
                if (!$('#per_country_link').hasClass('nav-tab-active')) {
                    $('#per_country_link').addClass('nav-tab-active')
                }

                var baseUrl = '?page=sales-report&type=per_country';

                // check for both period and country vals on intial page load
                if ($('#period').val() !== '' && $('#country').val() !== '') {

                    // Get the selected values
                    var country = $('#country').val();
                    var period = $('#period').val();

                    // Update the href attribute of the link
                    var url = baseUrl + '&country=' + country + '&period=' + period;
                    $('#sales-report-link').attr('href', url).removeClass('button-disabled');

                } else {
                    $('#sales-report-link').attr('href', '#').addClass('button-disabled');
                }

                // country/period select on change
                $('#country, #period').on('change', function() {

                    var country = $('#country').val();
                    var period = $('#period').val();

                    if (country !== '' && period !== '') {
                        var url = baseUrl + '&country=' + country + '&period=' + period;
                        $('#sales-report-link').attr('href', url).removeClass('button-disabled');
                    } else {
                        $('#sales-report-link').attr('href', '#').addClass('button-disabled');
                    }
                });

            });
        </script>

<?php endif;
}
