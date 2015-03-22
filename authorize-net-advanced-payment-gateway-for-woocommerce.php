<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       Authorize.net Advanced Payment Gateway For WooCommerce
 * Plugin URI:        http://mbjtechnolabs.com/
 * Description:       Authorize.net Advanced Payment Gateway For WooCommerce extends the functionality of WooCommerce to accept payments from credit/debit cards and stay on your site for the transaction.
 * Version:           1.0.2
 * Author:            Jignesh Kaila
 * Author URI:        http://mbjtechnolabs.com/
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       authorize-net-advanced-payment-gateway-for-woocommerce
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!defined('MBJ_AT_PLUGIN_BASENAME')) {
    define('MBJ_AT_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-authorize-net-advanced-payment-gateway-for-woocommerce-activator.php
 */
function activate_authorize_net_advanced_payment_gateway_for_woocommerce() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-authorize-net-advanced-payment-gateway-for-woocommerce-activator.php';
    MBJ_Authorize_Advanced_Payment_Gateway_For_WooCommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-authorize-net-advanced-payment-gateway-for-woocommerce-deactivator.php
 */
function deactivate_authorize_net_advanced_payment_gateway_for_woocommerce() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-authorize-net-advanced-payment-gateway-for-woocommerce-deactivator.php';
    MBJ_Authorize_Advanced_Payment_Gateway_For_WooCommerce_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_authorize_net_advanced_payment_gateway_for_woocommerce');
register_deactivation_hook(__FILE__, 'deactivate_authorize_net_advanced_payment_gateway_for_woocommerce');

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-authorize-net-advanced-payment-gateway-for-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_authorize_net_advanced_payment_gateway_for_woocommerce() {

    $plugin = new MBJ_Authorize_Advanced_Payment_Gateway_For_WooCommerce();
    $plugin->run();
}

run_authorize_net_advanced_payment_gateway_for_woocommerce();
