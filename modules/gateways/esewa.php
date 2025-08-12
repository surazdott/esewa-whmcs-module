<?php

/**
 * WHMCS eSewa Payment Gateway Module
 *
 * eSewa Payment Gateway modules WHMCS platform.
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://github.com/surazdott/esewa-whmcs-module
 *
 * @copyright Copyright (c) Suraj Datheputhe
 * @author : @Suraj Datheputhe
*/

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

# Require libraries
require_once __DIR__ . '/esewa/init.php';

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related abilities and
 * settings.
 *
 * @see https://developers.whmcs.com/provisioning-modules/meta-data-params/
 *
 * @return array
*/
function esewa_MetaData()
{
    return array(
        'DisplayName' => 'eSewa Payment Gateway',
        'APIVersion' => '2.0',
        'DisableLocalCreditCardInput' => true,
        'TokenisedStorage' => false,
    );
}

/**
 * Define eSewa configuration options.
 *
 * @see https://developers.whmcs.com/provisioning-modules/config-options/
 *
 * @return array
*/
function esewa_config()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'eSewa Payment Gateway',
        ),
        'merchant_code' => array(
            'FriendlyName' => 'Merchant Code',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your merchant code provided by Esewa',
        ),
        'secret_key' => array(
            'FriendlyName' => 'Secret Key',
            'Type' => 'password',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your secret code provided by Esewa',
        ),
        'test_mode' => array(
            'FriendlyName' => 'Test Mode',
            'Type' => 'yesno',
            'Description' => 'Tick to enable test mode',
        ),
    );
}

/**
 * eSewa Payment Gateway link.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/third-party-gateway/
 *
 * @return string
*/
function esewa_link($params)
{
    // Gateway Configuration Parameters
    $merchantCode = $params['merchant_code'];
    $secretKey = $params['secret_key'];

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $amount = $params['amount'];

    // System Parameters
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleName = $params['paymentmethod'];

    $url = $params['test_mode'] == true ? 'https://rc-epay.esewa.com.np/api/epay/main/v2/form' : 'https://esewa.com.np/epay/main';

    $postfields = [];
    $postfields['amount'] = $amount;
    $postfields['tax_amount'] = '0';
    $postfields['total_amount'] = $amount;
    $postfields['transaction_uuid'] = encodeInvoice($invoiceId);
    $postfields['product_code'] = $merchantCode;
    $postfields['product_service_charge'] = 0;
    $postfields['product_delivery_charge'] = 0;
    $postfields['success_url'] = $systemUrl . '/modules/gateways/callback/' . $moduleName . '.php';
    $postfields['failure_url'] = $returnUrl;
    $postfields['signed_field_names'] = 'total_amount,transaction_uuid,product_code';
    $postfields['signature'] = generateSignature($secretKey, $postfields);
    
    $htmlOutput = '<form method="post" action="' . $url . '">';

    foreach ($postfields as $k => $v) {
        $htmlOutput .= '<input type="hidden" name="' . $k . '" value="' . $v . '" />';
    }

    $logo = $systemUrl . '/modules/gateways/esewa/logo.png';

    $htmlOutput .= '<img src="'.$logo.'" width="130"><br>';

    $htmlOutput .= '<input class="btn btn-success" type="submit" value="' . $langPayNow . '" />';
    $htmlOutput .= '</form>';

    return $htmlOutput;
}

/**
 * eSewa Payment Gateway refund transaction.
 *
 * Called when a refund is requested for a previously successful transaction.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/refunds/
 *
 * @return array Transaction response status
*/
function esewa_refund($params)
{
    return false;
}
