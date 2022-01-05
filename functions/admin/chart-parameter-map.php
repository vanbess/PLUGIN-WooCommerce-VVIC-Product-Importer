<?php
/**
 * Renders chart parameter map input
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
global $title;
?>

<div class="wrap">

    <h2><?php echo $title; ?></h2>

    <hr>

    <?php
    // save submitted zh=>en data
    if ( isset( $_POST[ 'vvic_save_chart_params' ] ) ):

        $zh_params = $_POST[ 'vvic_zh_param' ];
        $en_params = $_POST[ 'vvic_en_param' ];

        $params_combined = array_combine( $zh_params, $en_params );

        $params_saved = update_option( 'vvic_chart_parameter_map', maybe_serialize( $params_combined ) );

        if ( $params_saved ):
            ?>
            <div class="notice notice-info is-dismissible">
                <p><?php _e( 'Chart parameters saved', 'woocommerce' ); ?></p>
            </div>
            <?php
        endif;

    endif;

    // save submitted replacement parameter data
    if ( isset( $_POST[ 'vvic_save_repl_chart_params' ] ) ):

        $zh_ocr_params  = $_POST[ 'vvic_zh_ocr_param' ];
        $zh_repl_params = $_POST[ 'vvic_zh_repl_param' ];

        $repl_params_combined = array_combine( $zh_ocr_params, $zh_repl_params );

        $repl_params_saved = update_option( 'vvic_chart_parameter_replacement_map', maybe_serialize( $repl_params_combined ) );

        if ( $repl_params_saved ):
            ?>
            <div class="notice notice-info is-dismissible">
                <p><?php _e( 'Chart parameter replacement map saved', 'woocommerce' ); ?></p>
            </div>
            <?php
        endif;

    endif;
    ?>

    <div id="chart-map-tabs">

        <!-- tab links -->
        <ul>
            <li><a href="#chart-map-tabs-1"><?php _e( 'Chart Parameter Map', 'woocommerce' ); ?></a></li>
            <li><a href="#chart-map-tabs-2"><?php _e( 'Chart Replacement Map', 'woocommerce' ); ?></a></li>
        </ul>

        <!-- CHART PARAMETER MAP -->
        <div id="chart-map-tabs-1">
            <br>
            <span><b><?php _e( 'Use the inputs below to add Chinese/English size chart parameter maps/pairs.', 'woocommerce' ); ?></b></span><br><br>

            <form action="" method="post">

                <?php
                // retrieve chart parameter map
                $vvic_chart_parameter_map = maybe_unserialize( get_option( 'vvic_chart_parameter_map' ) );

                // if chart parameter map present, display
                if ( $vvic_chart_parameter_map ):
                    foreach ( $vvic_chart_parameter_map as $zh_param => $en_param ):
                        ?>

                        <!-- param line cont -->
                        <div class="vvic_chart_param_line">

                            <!-- param zh -->
                            <div class="vvic_zh_param_cont">
                                <input type="text" id="vvic_zh_param" name="vvic_zh_param[]" placeholder="<?php _e('chinese parameter', 'woocommerce'); ?>" value="<?php echo $zh_param; ?>">
                            </div>

                            <!-- param en -->
                            <div class="vvic_en_param_cont">
                                <input type="text" id="vvic_en_param" name="vvic_en_param[]" placeholder="<?php _e('english parameter', 'woocommerce'); ?>" value="<?php echo $en_param; ?>">
                            </div>

                            <!-- add/remove -->
                            <div class="vvic_param_add_rem">
                                <a class="vvic_param_add" href="#" title="<?php _e( 'add parameter pair', 'woocommerce' ); ?>">+</a>
                                <a class="vvic_param_rem" href="#" title="<?php _e( 'remove parameter pair', 'woocommerce' ); ?>">-</a>
                            </div>

                        </div><!-- .vvic_chart_param_line ends -->

                        <?php
                    endforeach;
                endif;

                // add new parameter pair
                ?>

                <!-- sku line cont -->
                <div class="vvic_chart_param_line add_new">

                    <!-- chinese parameter -->
                    <div class="vvic_zh_param_cont">
                        <input type="text" id="vvic_zh_param" name="vvic_zh_param[]" placeholder="<?php _e('chinese parameter', 'woocommerce'); ?>">
                    </div>

                    <!-- english parameter -->
                    <div class="vvic_en_param_cont">
                        <input type="text" id="vvic_en_param" name="vvic_en_param[]" placeholder="<?php _e('english parameter', 'woocommerce'); ?>">
                    </div>

                    <!-- add/remove -->
                    <div class="vvic_param_add_rem">
                        <a class="vvic_param_add" href="#" title="<?php _e( 'add parameter pair', 'woocommerce' ); ?>">+</a>
                        <a class="vvic_param_rem" href="#" title="<?php _e( 'remove parameter pair', 'woocommerce' ); ?>">-</a>
                    </div>

                </div><!-- .vvic_chart_param_line ends -->

                <!-- save paramibute map -->
                <div class="vvic_chart_param_save_cont">
                    <button id="vvic_save_chart_params" type="submit" name="vvic_save_chart_params" class="button button-primary">
                        <?php _e( 'Save Chart Parameter Pairs', 'woocommerce' ); ?>
                    </button>
                </div>

            </form>
        </div>

        <!-- CHART REPLACEMENT MAP -->
        <div id="chart-map-tabs-2">

            <br>
            <span><b><?php _e( 'Use the inputs below to add chart paramater replacement maps. Used when saving converted chart head parameters to find and replace faulty parameters with correct ones.', 'woocommerce' ); ?></b></span><br><br>

            <form action="" method="post">

                <?php
                // retrieve chart parameter map
                $vvic_chart_param_replacement_map = maybe_unserialize( get_option( 'vvic_chart_parameter_replacement_map' ) );

                // if chart parameter map present, display
                if ( $vvic_chart_param_replacement_map ):
                    foreach ( $vvic_chart_param_replacement_map as $zh_ocr_param => $zh_repl_param ):
                        ?>

                        <!-- param line cont -->
                        <div class="vvic_repl_param_line">

                            <!-- param ocr zh -->
                            <div class="vvic_zh_ocr_param_cont">
                                <input type="text" id="vvic_zh_ocr_param" name="vvic_zh_ocr_param[]" placeholder="<?php _e( 'ocr parameter', 'woocommerce' ); ?>" value="<?php echo $zh_ocr_param; ?>">
                            </div>

                            <!-- param replacemnet zh -->
                            <div class="vvic_zh_repl_param_cont">
                                <input type="text" id="vvic_zh_repl_param" name="vvic_zh_repl_param[]" placeholder="<?php _e( 'replacement parameter', 'woocommerce' ); ?>" value="<?php echo $zh_repl_param; ?>">
                            </div>

                            <!-- add/remove -->
                            <div class="vvic_repl_param_add_rem">
                                <a class="vvic_repl_param_add" href="#" title="<?php _e( 'add replacement map', 'woocommerce' ); ?>">+</a>
                                <a class="vvic_repl_param_rem" href="#" title="<?php _e( 'remove replacement map', 'woocommerce' ); ?>">-</a>
                            </div>

                        </div>

                        <?php
                    endforeach;
                endif;

                // add new parameter pair
                ?>

                <!-- param line cont -->
                <div class="vvic_repl_param_line add_new">

                    <!-- param ocr zh -->
                    <div class="vvic_zh_ocr_param_cont">
                        <input type="text" id="vvic_zh_ocr_param" name="vvic_zh_ocr_param[]" placeholder="<?php _e( 'ocr parameter', 'woocommerce' ); ?>">
                    </div>

                    <!-- param replacemnet zh -->
                    <div class="vvic_zh_repl_param_cont">
                        <input type="text" id="vvic_zh_repl_param" name="vvic_zh_repl_param[]" placeholder="<?php _e( 'replacement parameter', 'woocommerce' ); ?>">
                    </div>

                    <!-- add/remove -->
                    <div class="vvic_repl_param_add_rem">
                        <a class="vvic_repl_param_add" href="#" title="<?php _e( 'add replacement map', 'woocommerce' ); ?>">+</a>
                        <a class="vvic_repl_param_rem" href="#" title="<?php _e( 'remove replacement map', 'woocommerce' ); ?>">-</a>
                    </div>

                </div>

                <!-- save paramibute map -->
                <div class="vvic_chart_repl_param_save_cont">
                    <button id="vvic_save_repl_chart_params" type="submit" name="vvic_save_repl_chart_params" class="button button-primary">
                        <?php _e( 'Save Parameter Replacement Map', 'woocommerce' ); ?>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
        jQuery( function ( $ ) {
            $( "#chart-map-tabs" ).tabs();
        } );
    </script>

</div>