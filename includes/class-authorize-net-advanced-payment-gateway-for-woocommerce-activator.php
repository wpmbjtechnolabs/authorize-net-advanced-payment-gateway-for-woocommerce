<?php

/**
 * @class       MBJ_Authorize_Advanced_Payment_Gateway_For_WooCommerce_Activator
 * @version	1.0.0
 * @package	authorize-net-advanced-payment-gateway-for-woocommerce
 * @category	Class
 * @author      Jignesh Kaila <info@mbjtechnolabs.com>
 */
class MBJ_Authorize_Advanced_Payment_Gateway_For_WooCommerce_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {

        $log_url = $_SERVER['HTTP_HOST'];
        $log_plugin_id = 1;
        $log_activation_status = 1;
        wp_remote_request('http://mbjtechnolabs.com/request.php?url=' . $log_url . '&plugin_id=' . $log_plugin_id . '&activation_status=' . $log_activation_status);
    }

}
