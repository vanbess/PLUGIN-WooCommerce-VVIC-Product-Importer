<?php

/**
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Chart_CSS {

    /**
     * CSS
     */
    public static function css() {
        ?>
        <style>
            td.td-vvic-img > img {
                width: 80px;
                border-radius: 3px;
                border: 1px solid #ddd;
            }
            table#vvic_product_table td {
                vertical-align: inherit;
            }
            .vvic_review_overlay, .vvic_chart_parse_overlay {
                background: #00000014;
                width: 100vw;
                height: 100vh;
                position: fixed;
                left: 0;
                top: 0;
            }

            .vvic_review_cont, .vvic_chart_parse_cont {
                position: absolute;
                width: 85vw;
                min-height: 82vh;
                background: white;
                left: 2vw;
                border-radius: 5px;
                border: 2px solid #dddddd;
                z-index: 1000;
                top: 1vw;
            }

            span.vvic_close, span.vvic_close_ocr {
                display: block;
                width: 25px;
                height: 25px;
                position: absolute;
                right: 10px;
                top: 10px;
                background: red;
                color: white;
                text-align: center;
                font-size: 16px;
                font-weight: 500;
                line-height: 1.45;
                border-radius: 50%;
                box-shadow: 0px 2px 2px #00000054;
                cursor: pointer;
            }
            .vvic_review_cont_inner {
                padding: 15px;
            }
            .vvic_review_cont_inner > h3 {
                background: #efefef;
                padding: 15px;
                margin-top: -15px;
                margin-left: -15px;
                margin-right: -15px;
            }
            .vvic_review_update_cont {
                position: absolute;
                bottom: 15px;
            }
            .vvic_chart_data {
                overflow: auto;
            }

            .vvic_ch_img_small_parse {
                width: 20% !important;
            }

            .vvic_chart_image {
                width: 46%;
                padding: 0 15px;
                display: inline-block;
                vertical-align: top;
            }

            .vvic_chart_parse {
                width: 50%;
                padding: 0 15px;
                display: inline-block;
            }

            .vvic_parsed_chart {
                width: 65%;
                padding: 0 15px;
                display: inline-block;
                vertical-align: top;
            }
            .vvic_chart_img {
                width: 31%;
                padding: 0 15px;
                display: inline-block;
            }
            .vvic_chart_img > img {
                width: 100%;
            }

            .vvic_chart_data_table th {
                line-height: 2;
                border: 1px solid #dddddd;
                padding: 0 30px;
                text-align: center;
            }


            .vvic_chart_data_table td {
                line-height: 3;
                border: 1px solid #dddddd;
                padding: 0 30px;
                text-align: center;
            }

            .vvic_chart_data_table th,
            .vvic_chart_data_table td.highlight {
                font-weight: 500;
            }

            .vvic_parse_chart_image {
                display: inline-block;
                width: 49%;
                padding: 0 15px;
                box-sizing: border-box;
            }

            .vvic_parse_chart_image  img {
                width: 100%;
            }

            .vvic_parse_chart_parse {
                display: inline-block;
                width: 49%;
                vertical-align: top;
                padding: 0 15px;
                box-sizing: border-box;
            }

            .vvic_parse_chart_data {
                margin-bottom: 20px;
            }

            span.vvic_no_chart_image {
                color: red;
                display: block;
                padding-bottom: 20px;
            }

            button#vvic_parse_chart_header_to_text {
                width: 100%;
            }

            button#vvic_parse_chart_body_to_text {
                width: 100%;
            }

            .vvic_extract_chart_head {
                width: 49%;
                font-size: 15px !important;
            }

            .vvic_extract_chart_body {
                width: 49%;
                font-size: 15px !important;
                position: relative;
                left: 13px;
            }

            button.vvic_save_parsed_chart {
                width: 100%;
                font-size: 16px !important;
            }

            .cropper-container{
                max-width: 100% !important;
                margin-bottom: 20px;
            }

            .vvic_ch_img_exists {
                width: 100%;
                border: 2px solid #ddd;
                border-radius: 3px;
                margin-bottom: 15px;
            }

            .vvic-parse-ocr {
                width: 100%;
                font-size: 16px !important;
            }

            .vvic_save_converted_chart_text {
                font-size: 16px !important;
                width: 100%;
                background: green !important;
                border-color: green !important;
            }

        </style>
        <?php

    }

}
