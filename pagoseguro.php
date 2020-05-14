<?php
/*
 * Plugin Name: Pago Seguro Payment Gateway For WooCommerce
 * Plugin URI: https://pagosegurorepos.github.io/documentation/#/
 * Description: Pago Seguro Payment Gateway
 * Author: widres8
 * Author URI: http://widres8.github.io/
 * Developer: widres8
 * Developer URI: http://widres8.github.io/
 * Version: 1.0.1
 * WC requires at least: 2.2
 * WC tested up to: 2.3
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
if (in_array('woocommerce/woocommerce.php', $active_plugins)) {
    /*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
    add_filter('woocommerce_payment_gateways', 'pagoseguro_add_gateway_class');
    function pagoseguro_add_gateway_class($gateways)
    {
        $gateways[] = 'PagoSeguro_Gateway'; // your class name is here
        return $gateways;
    }

    /*
    * The class itself, please note that it is inside plugins_loaded action hook
    */
    add_action('plugins_loaded', 'pagoseguro_init_gateway_class');

    add_action('plugins_loaded', 'pagoseguro_load_plugin_textdomain');
    function pagoseguro_load_plugin_textdomain()
    {
        load_plugin_textdomain('pagoseguro', false, WP_PLUGIN_URL.'/'.str_replace(basename(__FILE__), '', plugin_basename(__FILE__)).'languages/');
        load_plugin_textdomain('pagoseguro', false, dirname(plugin_basename(__FILE__)).'/languages/');
        // load_textdomain('pagoseguro', WP_PLUGIN_DIR.'/'.dirname(plugin_basename(__FILE__)).'/languages/pagoseguro-'.get_locale().'.mo');
    }
}

function pagoseguro_init_gateway_class()
{
    /**
     * PagoSeguro Class.
     */
    class PagoSeguro_Gateway extends WC_Payment_Gateway
    {
        /**
         * Whether or not logging is enabled
         *
         * @var bool
         */
        public static $log_enabled = false;

        /**
         * Logger instance
         *
         * @var WC_Logger
         */
        public static $log = false;

        /**
         * Class constructor
         */
        public function __construct()
        {
            $this->id                 = 'pagoseguro'; // payment gateway plugin ID
            $this->icon               = ''; // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields         = true; // in case you need a custom credit card form
            $this->method_title       = __('PagoSeguro', 'pagoseguro');
            $this->method_description = __('PagoSeguroDescription', 'pagoseguro'); // will be displayed on the options page

            // gateways can support subscriptions, refunds, saved payment methods,
            // but in this tutorial we begin with simple payments
            $this->supports = [
                    'products',
                ];

            // Method with all the options fields
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();

            $this->title           = $this->get_option('title');
            $this->description     = $this->get_option('description');
            $this->enabled         = $this->get_option('enabled');

            $this->pagoseguroTestMode        = 'yes' === $this->get_option('pagoseguroTestMode');
            $this->pagoseguroAccountId       = $this->get_option('pagoseguroAccountId');
            $this->pagoseguroApiKey          = $this->get_option('pagoseguroApiKey');
            $this->pagoseguroAccountIdTest   = $this->get_option('pagoseguroAccountIdTest');
            $this->pagoseguroApiKeyTest      = $this->get_option('pagoseguroApiKeyTest');
            $this->pagoseguroUrlTest         = $this->get_option('pagoseguroUrlTest');
            $this->pagoseguroUrlPayment      = $this->get_option('pagoseguroUrlPayment');

            self::$log_enabled    = $this->pagoseguroTestMode;

            // Copy translation file by current locale
            copy($this->getLanguageMoFile(), WP_LANG_DIR.'/plugins/pagoseguro-'.get_locale().'.mo');

            // This action hook saves the settings
            add_action('woocommerce_update_options_payment_gateways_'.$this->id, [$this, 'process_admin_options']);

            // We need custom JavaScript to obtain a token
            add_action('wp_enqueue_scripts', [$this, 'payment_scripts']);

            // Redirect form Pago Seguro
            add_action('woocommerce_receipt_'.$this->id, [$this, 'payPagoSeguro']);
        }

        /**
         * Get Admin Panel Options.
         */
        public function admin_options()
        {
            if ($this->isValidForUse()) {
                $icon = $this->getUrlAssets().'/img/logo.png';
                include 'views/admin/settings-configure.php';
            } else {
                include 'views/gateway-disabled.php';
            }
        }

        /**
         * View Configure Admin
         */
        public function init_form_fields()
        {
            $this->form_fields = include 'views/admin/settings-configure-form.php';
        }

        /**
         * Processes and saves options.
         * If there is an error thrown, will continue to save and validate fields, but will leave the erroring field out.
         *
         * @return bool was anything saved?
         */
        public function process_admin_options()
        {
            $saved = parent::process_admin_options();

            $this->log('Admin Options Updated');

            return $saved;
        }

        /**
         * You will need it if you want your custom credit card form,  is about it
         */
        public function payment_fields()
        {
            $amount = get_woocommerce_currency_symbol().' '.floatval(preg_replace('#[^\d.]#', '', WC()->cart->total));
            include 'views/front/payment_detail.php';
        }

        /*
         * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
         */
        public function payment_scripts()
        {
        }

        /*
          * Fields validation, more in
         */
        public function validate_fields()
        {
        }

        /*
        * We're processing the payments here, everything about it is in
        */
        public function process_payment($order_id)
        {
            $order = new WC_Order($order_id);

            return [
                'result'   => 'success',
                'redirect' => $order->get_checkout_payment_url(true),
            ];
        }

        /*
        * In case you need a webhook, like PayPal IPN etc
        */
        public function webhook()
        {
        }

        /**
         * Get gateway icon.
         */
        public function get_icon()
        {
            $icon      = $this->getUrlAssets().'/img/payment_logo.png';
            $icon_html = '<img src="'.esc_attr($icon).'" alt="'.esc_attr__('PagoSeguroDescription').'" style="float: none;" />';

            return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
        }

        /*
        * Validate order and Redirect Pago Seguro
        */
        public function payPagoSeguro($order_id)
        {
            // Cart
            $total       = (float) WC()->cart->total;
            $currency    = get_woocommerce_currency();
            $orderStatus = 'wc-processing';

            // Customer
            $customerFullName = WC()->customer->get_first_name().' '.WC()->customer->get_last_name();
            $customerEmail    = WC()->customer->get_email();

            // Url Checkout
            $url = 'yes' === $this->get_option('pagoseguroTestMode') ? $this->get_option('pagoseguroUrlTest') : $this->get_option('pagoseguroUrlPayment');
            if (false == $url) {
                wc_add_notice(__('moduleWithoutConfig', 'pagoseguro'), 'error');

                return wc_get_checkout_url();
            }

            // Parameters Pago Seguro
            $accountId = 'yes' === $this->get_option('pagoseguroTestMode') ? $this->get_option('pagoseguroAccountIdTest') : $this->get_option('pagoseguroAccountId');
            $apiKey    = 'yes' === $this->get_option('pagoseguroTestMode') ? $this->get_option('pagoseguroApiKeyTest') : $this->get_option('pagoseguroApiKey');

            // Order
            $order          = new WC_Order($order_id);
            $orderReference = ($order->get_id() ? $order->get_id() : 0);
            foreach ($order->get_items() as $item) {
                $product = wc_get_product($item['product_id'])->get_title();
                break;
            }
            // Signature
            $stringSignature = $accountId.'|'.$orderReference.'|'.$total.'|'.$product.'|'.$customerFullName.'|'.$customerEmail.'|'.'/payment/process||||||||||'.$apiKey;
            $signature       = hash('sha512', $stringSignature);

            // Events Order
            echo '<p>'.__('RedirectingPayment.', 'pagoseguro').'</p>';
            $order->add_order_note(__('OrderNote', 'pagoseguro'));
            $order->update_status($orderStatus, __('AwaitingPayment.', 'pagoseguro'));
            WC()->cart->empty_cart();
            $urlResponse = $order->get_checkout_order_received_url();

            return include 'views/front/payment.php';
        }

        /**
         * Get Support Currency Enabled
         */
        public function isValidForUse()
        {
            return in_array(
                get_woocommerce_currency(),
                apply_filters(
                    'woocommerce_paypal_supported_currencies',
                    ['COP', 'VEF', 'PEN', 'ARS', 'BRL', 'CLP', 'MXN', 'PAB', 'USD']
                ),
                true
            );
        }

        /**
         * Get url assets.
         */
        public function getUrlAssets()
        {
            return WP_PLUGIN_URL.'/'.str_replace(basename(__FILE__), '', plugin_basename(__FILE__)).'/assets';
        }

        /**
         * Get language .mo file.
         */
        public function getLanguageMoFile()
        {
            return WP_PLUGIN_URL.'/'.str_replace(basename(__FILE__), '', plugin_basename(__FILE__)).'languages/pagoseguro-'.get_locale().'.mo';
        }

        /**
         * Logging method.
         *
         * @param string $message log message
         * @param string $level Optional. Default 'info'. Possible values:
         * emergency|alert|critical|error|warning|notice|info|debug.
         */
        public function log($message, $level = 'info')
        {
            // Clear Log in Production
            if ('yes' === !$this->get_option('pagoseguroTestMode')) {
                if (empty(self::$log)) {
                    self::$log = wc_get_logger();
                }
                self::$log->clear('pagoseguro');
            }

            if (self::$log_enabled) {
                if (empty(self::$log)) {
                    self::$log = wc_get_logger();
                }
                self::$log->log($level, $message, ['source' => 'pagoseguro']);
            }
        }
    }
}