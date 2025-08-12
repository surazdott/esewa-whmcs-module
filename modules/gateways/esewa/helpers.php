<?php

/**
 * WHMCS eSewa Payment Gateway Helper Functions
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://github.com/surazdott/esewa-whmcs-module
 *
 * @copyright Copyright (c) Suraj Datheputhe
 * @author : @Suraj Datheputhe
*/

/**
 * Redirect page
 *
 * @param string url
 * @return void
*/
function redirect($path)
{
    header('location: '.$path);
    exit();
}

/**
 * Create random string.
 *
 * @param int length
 * @return string
*/
function randomString($length = 100) {
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str_len = strlen($chars);
    $random = '';

    for($i=0; $i<$length; $i++) {
        $random .= $chars[rand(0, $str_len-1)];
    }

    return $random;
}

/**
 * Encode invoice to pass unique Invoice number.
 *
 * @param string invoiceId
 * @return string
*/
function encodeInvoice(string $invoiceId)
{
    return randomString(7).'-'.$invoiceId;
}

/**
 * Decode invoice number.
 *
 * @param string string
 * @return string
*/
function decodeInvoice(string $string)
{
    return substr($string, 8);
}

/**
 * Generate signature for the payment.
 *
 * @param array $data
 * @return string
*/
function generateSignature(string $secretKey, array $data): string
{
    $signedFields = "total_amount={$data['amount']}," .
        "transaction_uuid={$data['transaction_uuid']}," .
        "product_code={$data['product_code']}";

    return base64_encode(hash_hmac('sha256', $signedFields, $secretKey ?? '', true));
}

/**
 * Decrypt signature for the payment.
 *
 * @param string $data
 * @return array
*/
function decodeSignature(string $data): array
{
    return json_decode(base64_decode($data), true);
}
