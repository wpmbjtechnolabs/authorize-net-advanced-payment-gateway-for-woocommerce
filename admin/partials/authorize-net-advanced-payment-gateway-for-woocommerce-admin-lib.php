<?php

/**
 * @class       MBJ_Authorize_Advanced_Payment_Gateway_For_WooCommerce_Lib
 * @version	1.0.0
 * @package	mbj_auth_gateway
 * @category	Class
 * @author      Jignesh Kaila <info@mbjtechnolabs.com>
 */
class MBJ_Authorize_Advanced_Payment_Gateway_For_WooCommerce_Lib extends WC_Payment_Gateway {

    /**
     * Constructor for the gateway.
     */
    function __construct() {

        $this->id = "mbj_auth_gateway";
        $this->method_title = __("Authorize.net", 'mbj_auth_gateway');
        $this->method_description = __("Authorize.net payment gateway to provide secure and powerful payment processing for your Online store", 'mbj_auth_gateway');
        $this->title = __("Authorize.net", 'mbj_auth_gateway');
        $this->icon = null;
        $this->has_fields = true;
        $this->supports = array('default_credit_card_form');

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        $this->debug = $this->get_option('debug');
        // Logs
        if ('yes' == $this->debug) {
            $this->log = new WC_Logger();
        }

        foreach ($this->settings as $setting_key => $value) {
            $this->$setting_key = $value;
        }
        if (is_admin()) {
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }
    }

    
    /**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @access public
	 * @return void
	 */
  	public function admin_options() {
		?>
		<h3><?php _e( 'Authorize.net Advanced Payment Gateway', 'mbj_auth_gateway' ); ?></h3>

		<?php if ( empty( $this->public_key ) ) : ?>
			<div class="simplify-commerce-banner updated">
				<a target="_blank" href="http://reseller.authorize.net/application/?resellerId=27457"><img src="http://www.authorize.net/images/reseller/oap_sign_up.gif" height="38" width="135" border="0" /></a>
				<p class="main"><strong><?php _e( 'Getting started', 'mbj_auth_gateway' ); ?></strong></p>
				<p><?php _e( 'Authorize.net is one of the most popular payment processors around for a reason: they offer easy, safe, and affordable credit card processing. This advanced payment gateway integration offers powerful fraud prevention tools, along with easy-to-use online account access. View your transactions online and quickly make changes to customer profiles as needed. All Authorize.net accounts include free award-winning support via phone, chat, and email.', 'mbj_auth_gateway' ); ?></p>

				<p><a href="http://reseller.authorize.net/application/?resellerId=27457"" target="_blank" class="button button-primary"><?php _e( 'Sign Up Now', 'mbj_auth_gateway' ); ?></a> <a href="http://reseller.authorize.net/application/?resellerId=27457" target="_blank" class="button"><?php _e( 'Learn more', 'mbj_auth_gateway' ); ?></a></p>

			</div>
		<?php else : ?>
			<p><?php _e( 'Simplify Commerce is your merchant account and payment gateway all rolled into one. Choose Simplify Commerce as your WooCommerce payment gateway to get access to your money quickly with a powerful, secure payment engine backed by MasterCard.', 'mbj_auth_gateway' ); ?></p>
		<?php endif; ?>

		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
			
		</table>
		<?php
  	}

    /**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable / Disable', 'mbj_auth_gateway'),
                'label' => __('Enable Authorize.net payment gateway', 'mbj_auth_gateway'),
                'type' => 'checkbox',
                'default' => 'yes',
            ),
            'title' => array(
                'title' => __('Title', 'mbj_auth_gateway'),
                'type' => 'text',
                'desc_tip' => __('This controls the title which the user sees during checkout.', 'mbj_auth_gateway'),
                'default' => __('Credit card', 'mbj_auth_gateway'),
            ),
            'description' => array(
                'title' => __('Description', 'mbj_auth_gateway'),
                'type' => 'textarea',
                'desc_tip' => __('This controls the description which the user sees during checkout.', 'mbj_auth_gateway'),
                'default' => __('Pay securely using your credit card.', 'mbj_auth_gateway'),
                'css' => 'max-width:350px;'
            ),
            'api_login' => array(
                'title' => __('Authorize.net API Login', 'mbj_auth_gateway'),
                'type' => 'text',
                'desc_tip' => __('This is the API Login provided by Authorize.net when you signed up for an account.', 'mbj_auth_gateway'),
            ),
            'trans_key' => array(
                'title' => __('Authorize.net Transaction Key', 'mbj_auth_gateway'),
                'type' => 'password',
                'desc_tip' => __('This is the Transaction Key provided by Authorize.net when you signed up for an account.', 'mbj_auth_gateway'),
            ),
            'environment' => array(
                'title' => __('Authorize.net Test Mode', 'mbj_auth_gateway'),
                'label' => __('Enable Test Mode', 'mbj_auth_gateway'),
                'type' => 'checkbox',
                'description' => __('Place the payment gateway in test mode.', 'mbj_auth_gateway'),
                'default' => 'no',
            ),
            'debug' => array(
                'title' => __('Debug Log', 'mbj_auth_gateway'),
                'type' => 'checkbox',
                'label' => __('Enable logging', 'mbj_auth_gateway'),
                'default' => 'no',
                'description' => sprintf(__('Log transaction, inside <code>%s</code>', 'mbj_auth_gateway'), wc_get_log_file_path('authorize.net'))
            )
        );
    }

    /**
     * Process the payment and return the result
     *
     * @param int $order_id
     * @return array
     */
    public function process_payment($order_id) {
        global $woocommerce;

        $customer_order = new WC_Order($order_id);

        if ('yes' == $this->debug) {
            $this->log->add('authorize.net', 'Generating authorize.net form for order ' . $customer_order->get_order_number());
        }

        $environment = ( $this->environment == "yes" ) ? 'TRUE' : 'FALSE';
        $environment_url = ( "FALSE" == $environment ) ? 'https://secure.authorize.net/gateway/transact.dll' : 'https://test.authorize.net/gateway/transact.dll';
        $payload = array(
            // Authorize.net Credentials and API Info
            "x_tran_key" => $this->trans_key,
            "x_login" => $this->api_login,
            "x_version" => "3.1",
            // Order total
            "x_amount" => $customer_order->order_total,
            // Credit Card Information
            "x_card_num" => str_replace(array(' ', '-'), '', $_POST['mbj_auth_gateway-card-number']),
            "x_card_code" => ( isset($_POST['mbj_auth_gateway-card-cvc']) ) ? $_POST['mbj_auth_gateway-card-cvc'] : '',
            "x_exp_date" => str_replace(array('/', ' '), '', $_POST['mbj_auth_gateway-card-expiry']),
            "x_type" => 'AUTH_CAPTURE',
            "x_invoice_num" => str_replace("#", "", $customer_order->get_order_number()),
            "x_test_request" => $environment,
            "x_delim_char" => '|',
            "x_encap_char" => '',
            "x_delim_data" => "TRUE",
            "x_relay_response" => "FALSE",
            "x_method" => "CC",
            // Billing Information
            "x_first_name" => $customer_order->billing_first_name,
            "x_last_name" => $customer_order->billing_last_name,
            "x_address" => $customer_order->billing_address_1,
            "x_city" => $customer_order->billing_city,
            "x_state" => $customer_order->billing_state,
            "x_zip" => $customer_order->billing_postcode,
            "x_country" => $customer_order->billing_country,
            "x_phone" => $customer_order->billing_phone,
            "x_email" => $customer_order->billing_email,
            // Shipping Information
            "x_ship_to_first_name" => $customer_order->shipping_first_name,
            "x_ship_to_last_name" => $customer_order->shipping_last_name,
            "x_ship_to_company" => $customer_order->shipping_company,
            "x_ship_to_address" => $customer_order->shipping_address_1,
            "x_ship_to_city" => $customer_order->shipping_city,
            "x_ship_to_country" => $customer_order->shipping_country,
            "x_ship_to_state" => $customer_order->shipping_state,
            "x_ship_to_zip" => $customer_order->shipping_postcode,
            // Some Customer Information
            "x_cust_id" => $customer_order->user_id,
            "x_customer_ip" => $_SERVER['REMOTE_ADDR'],
        );

        if ('yes' == $this->debug) {
            $this->log->add('authorize.net', 'authorize.net request parameter ' . print_r($payload, true));
        }

        // Post back to get a response
        $response = wp_remote_post($environment_url, array(
            'method' => 'POST',
            'body' => http_build_query($payload),
            'timeout' => 90,
            'sslverify' => false,
        ));

        if ('yes' == $this->debug) {
            $this->log->add('authorize.net', 'authorize.net response' . print_r($response, true));
        }

        if (is_wp_error($response))
            throw new Exception(__('We are currently experiencing problems trying to connect to this payment gateway. Sorry for the inconvenience.', 'mbj_auth_gateway'));

        if (empty($response['body']))
            throw new Exception(__('Authorize.net\'s Response was empty.', 'mbj_auth_gateway'));

        // Retrieve the body's resopnse if no errors found
        $response_body = wp_remote_retrieve_body($response);

        if ('yes' == $this->debug) {
            $this->log->add('authorize.net', 'authorize.net response_body' . print_r($response_body, true));
        }


        // Parse the response into something we can read
        foreach (preg_split("/\r?\n/", $response_body) as $line) {
            $resp = explode("|", $line);
        }

        // Get the values we need
        $r['response_code'] = $resp[0];
        $r['response_sub_code'] = $resp[1];
        $r['response_reason_code'] = $resp[2];
        $r['response_reason_text'] = $resp[3];

        // Test the code to know if the transaction went through or not.
        // 1 or 4 means the transaction was a success
        if (( $r['response_code'] == 1 ) || ( $r['response_code'] == 4 )) {
            // Payment has been successful
            $customer_order->add_order_note(__('Authorize.net payment completed.', 'mbj_auth_gateway'));

            // Mark order as Paid
            $customer_order->payment_complete();

            // Empty the cart (Very important step)
            $woocommerce->cart->empty_cart();

            // Redirect to thank you page
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($customer_order),
            );
        } else {
            // Transaction was not succesful
            // Add notice to the cart
            wc_add_notice($r['response_reason_text'], 'error');
            // Add note to the order for your reference
            $customer_order->add_order_note('Error: ' . $r['response_reason_text']);
        }
    }

    // Validate fields
    public function validate_fields() {
        return true;
    }

}