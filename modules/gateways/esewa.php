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
        'APIVersion' => '1.1',
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
        'MerchantCode' => array(
            'FriendlyName' => 'Merchant Code',
            'Type' => 'password',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your Merchant Code here provided by eSewa',
        ),
        'testMode' => array(
            'FriendlyName' => 'Test Mode',
            'Type' => 'yesno',
            'Description' => 'Tick to enable test mode',
        ),
    );
}

/**
 * eSewa Payment Gateway link.
 *
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
    $testMode = $params['testMode'];
    $merchantCode = $params['MerchantCode'];

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    $url = $params['testMode'] == true ? 'https://uat.esewa.com.np/epay/main' : 'https://esewa.com.np/epay/main';

    $postfields = array();
    $postfields['pid'] = encodeInvoice($invoiceId);
    $postfields['tAmt'] = $amount;
    $postfields['amt'] = $amount;
    $postfields['txAmt'] = '0';
    $postfields['psc'] = '0';
    $postfields['pdc'] = '0';
    $postfields['scd'] = $merchantCode;
    $postfields['su'] = $systemUrl . '/modules/gateways/callback/' . $moduleName . '.php';
    $postfields['fu'] = $returnUrl;

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
