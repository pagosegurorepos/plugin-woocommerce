<?php
/**
 * Settings for Pago Seguro Gateway.
 */
defined('ABSPATH') || exit;

return [
    'description' => [
        'title'       => __('Description', 'pagoseguro'),
        'type'        => 'textarea',
        'default'     => 'Pay with your credit card via our super-cool payment gateway Pago Seguro.',
    ],
    'pagoseguroTestMode' => [
        'title'       => __('PAGOSEGURO_TEST_MODE', 'pagoseguro'),
        'type'        => 'checkbox',
        'label'       => 'Debe estar desactivado si en la configuración de Pago Seguro esta en modo producción',
        'default'     => 'yes',
    ],
    'pagoseguroAccountId' => [
        'title'       => __('PAGOSEGURO_ACCOUNT_ID', 'pagoseguro'),
        'type'        => 'number',
        'required'    => '',
    ],
    'pagoseguroApiKey' => [
        'title'       => __('PAGOSEGURO_API_KEY', 'pagoseguro'),
        'type'        => 'password',
    ],
    'pagoseguroAccountIdTest' => [
        'title'       => __('PAGOSEGURO_ACCOUNT_ID_TEST', 'pagoseguro'),
        'type'        => 'number',
        'required'    => '',
    ],
    'pagoseguroApiKeyTest' => [
        'title'       => __('PAGOSEGURO_API_KEY_TEST', 'pagoseguro'),
        'type'        => 'password',
    ],
    'pagoseguroUrlTest' => [
        'title'       => __('PAGOSEGURO_URL_TEST', 'pagoseguro'),
        'type'        => 'text',
    ],
    'pagoseguroUrlPayment' => [
        'title'       => __('PAGOSEGURO_URL_PAYMENT', 'pagoseguro'),
        'type'        => 'text',
    ],
];