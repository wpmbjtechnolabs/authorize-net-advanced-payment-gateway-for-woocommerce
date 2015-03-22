<?php

/**
 * @class       MBJ_Authorize_Advanced_Payment_Gateway_For_WooCommerce_Admin
 * @version	1.0.0
 * @package	authorize-net-advanced-payment-gateway-for-woocommerce
 * @category	Class
 * @author      Jignesh Kaila <info@mbjtechnolabs.com>
 */
class MBJ_Authorize_Advanced_Payment_Gateway_For_WooCommerce_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @var      string    $plugin_name       The name of this plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
		add_filter('woocommerce_paypal_args', array(__CLASS__, 'paypal_ipn_for_wordpress_standard_parameters'), 10, 1);
    }

    public function load_plugin_extend_lib() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Admin_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Admin_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        /**
         * The class responsible for defining all actions that occur in the Dashboard.
         */
        if (!class_exists('WC_Payment_Gateway'))
            return;
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/authorize-net-advanced-payment-gateway-for-woocommerce-admin-lib.php';
    }

    public function authorize_net_advanced_payment_gateway_for_woocommerce_add_gateway($methods) {
        $methods[] = 'MBJ_Authorize_Advanced_Payment_Gateway_For_WooCommerce_Lib';
        return $methods;
    }

    public function authorize_net_advanced_payment_gateway_for_woocommerce_action_links($links) {
        $plugin_links = array(
            '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=mbj_authorize_advanced_payment_gateway_for_woocommerce_lib') . '">' . __('Settings', 'authorize-net-advanced-payment-gateway-for-woocommerce') . '</a>',
        );

        // Merge our new link with the default ones
        return array_merge($plugin_links, $links);
    }
	
	 public static function paypal_ipn_for_wordpress_standard_parameters($paypal_args){
        $paypal_args['bn'] = 'mbjtechnolabs_SP';
        return $paypal_args;
    }

}
