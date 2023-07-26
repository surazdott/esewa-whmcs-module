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
 * @return redirect
 */ 
function redirect($path)
{
    header('location: '.$path);
    exit();
}

/**
 * Encode invoice to pass unique Invoice number
 * 
 * @param int length
 * @return string
 */
function encodeInvoice($invoiceId)
{
    $encode = randomString(7).'-'.$invoiceId;

    return $encode;
}

/**
 * Decode invoice number
 * 
 * @param int length
 * @return string
 */
function decodeInvoice($string)
{
    return substr($string, 8);
}

/**
 * Create random string
 * 
 * @param int leng
 * @return string
 */
function randomString($leng=100) {
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str_len = strlen($chars);
    $random = '';

    for($i=0; $i<$leng; $i++) {
        $random .= $chars[rand(0, $str_len-1)];
    }

    return $random;
}
