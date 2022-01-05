<?php

/**
 * Save update chart data via AJAX
 *
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Chart_Save
{

    public static function vvic_save_updated_chart()
    {

        check_ajax_referer('save updated chart data');

        // setup chart data
        $prod_id      = $_POST['prod_id'];
        $chart_header = $_POST['header_data'];
        $body_data    = $_POST['body_data'];

        // chart data array which will hold final translated/parsed chart data
        $combined_chart_data = [];

        // deleted original post meta
        delete_post_meta($prod_id, 'sb_chart_pre_header');
        delete_post_meta($prod_id, 'sb_chart_pre_body');
        delete_post_meta($prod_id, 'sbarray_chart_data');

        // update review status
        update_post_meta($prod_id, '_vvic_chart_reviewed', 'Yes');

        // push enhanced data to $combined_chart_data
        array_push($combined_chart_data, $chart_header);

        // update product chart header meta
        update_post_meta($prod_id, 'sb_chart_pre_header', maybe_serialize($chart_header));

        // push to $combined chart data
        array_push($combined_chart_data, $body_data);

        //       print_r($combined_chart_data);

        // update product chart body meta
        update_post_meta($prod_id, 'sb_chart_pre_body', maybe_serialize($body_data));

        // update product chart combined header and body meta
        $chart_updated = update_post_meta($prod_id, 'sbarray_chart_data', maybe_serialize($combined_chart_data));

        if ($chart_updated) :
            wp_send_json($chart_updated);
        endif;

        wp_die();
    }
}
