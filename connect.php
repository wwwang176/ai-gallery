<?php

date_default_timezone_set('Asia/Taipei');

include __DIR__.'/config.php';

//DB
define('DB_NAME', $DB_NAME);
define('DB_USER', $DB_USER);
define('DB_PASSWD', $DB_PASSWD);
define('DB_HOST', $DB_HOST);
define('GEMINI_APIKEY', $GEMINI_APIKEY);

$DBC = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWD);
$DBC->exec("set names utf8");


/**
 * 判斷是否為Json格式
 *
 * @param string $string
 * @return boolean
 */
if(!function_exists('isJson'))
{
    function isJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}
