<?php

/**
 * WHMCS eSewa Payment Gateway Module
 *
 * This script handles the callback response from the eSewa payment gateway.
 * It updates the payment status in WHMCS based on the callback response.
 * 
 * For more information, please refer to the online documentation.
 *
 * @see https://github.com/surazdott/esewa-whmcs-module
 *
 * @copyright Copyright (c) Suraj Datheputhe
 * @author : @Suraj Datheputhe
 */

# Include the WHMCS initialization file
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

# Require libraries
require_once __DIR__ . '/../esewa/init.php';

# Get module name
$gatewayModuleName = basename(__FILE__, '.php');

# Get the gateway module parameters
$gatewayParams = getGatewayVariables($gatewayModuleName);

# Check if the gateway is activated
if (!$gatewayParams['type']) {
    die("Module Not Activated");
}

# Variable per payment gateway
$invoiceId = decodeInvoice($_GET['oid']);
$transactionId = $_GET['refId'];
$paymentAmount = $_GET['amt'];
$transactionStatus = $transactionId != null ? 'Success' : 'Failure';

/**
 * Validate Callback Invoice ID.
 *
 * Checks invoice ID is a valid invoice number. Note it will count an
 * invoice in any status as valid.
 *
 * Performs a die upon encountering an invalid Invoice ID.
 *
 * Returns a normalised invoice ID.
 *
 * @param int $invoiceId Invoice ID
 * @param string $gatewayName Gateway Name
 */

$invoiceId = checkCbInvoiceID($invoiceId, $gatewayParams['gatewayParams']);

/**
 * Get invoice number
 * 
 * @param int invoiceId
 * @return data
 */
$invoice = WHMCS\Billing\Invoice::find($invoiceId);


/**
 * Validate invoice amount
 * 
 * @return boolean 
 */
if ($invoice->total != $paymentAmount) {
    $failedUrl = $gatewayParams['systemurl'].'/viewinvoice.php?id='.$invoiceId.'&paymentfailed=true';
    redirect($failedUrl);
} else {

    /**
     * Log Transaction.
     *
     * Add an entry to the Gateway Log for debugging purposes.
     *
     * The debug data can be a string or an array. In the case of an
     * array it will be
     *
     * @param string $gatewayName        Display label
     * @param string|array $debugData    Data to log
     * @param string $transactionStatus  Status
     */

    logTransaction($gatewayModuleName, $_GET, $transactionStatus);

    /**
     * Payment Verification Process and Update Invoice Paid
     * 
     * @param int invoiceId
     * @param string transactionId
     */

    $url = $gatewayParams['testMode'] == true ? 
        'https://uat.esewa.com.np/epay/transrec' : 
        'https://esewa.com.np/epay/transrec';

    $paymentData = [
        'amt'=> $_GET['amt'],
        'rid'=> $_GET['refId'],
        'pid'=> $_GET['oid'],
        'scd'=> $gatewayParams['MerchantCode']
    ];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $paymentData);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($curl);
    curl_close($curl);

    $response = (string) simplexml_load_string($result)->response_code;

    if (strpos($response, 'Success') == true) {
        $paymentFee = '0.0';

        /**
         * Add Invoice Payment.
         *
         * Applies a payment transaction entry to the given invoice ID.
         *
         * @param int $invoiceId         Invoice ID
         * @param string $transactionId  Transaction ID
         * @param float $paymentAmount   Amount paid (defaults to full balance)
         * @param float $paymentFee      Payment fee (optional)
         * @param string $gatewayModule  Gateway module name
         */
        addInvoicePayment(
            $invoiceId,
            $transactionId,
            $paymentAmount,
            $paymentFee,
            $gatewayModuleName
        );

        $successUrl = $gatewayParams['systemurl'].'/viewinvoice.php?id='.$invoiceId.'&paymentsuccess=true';
        redirect($successUrl);
    } else {
        $failedUrl = $gatewayParams['systemurl'].'/viewinvoice.php?id='.$invoiceId.'&paymentfailed=true';
        redirect($failedUrl);
    }
}
