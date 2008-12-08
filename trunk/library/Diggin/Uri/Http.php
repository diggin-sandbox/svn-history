<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * 
 * @category   Diggin
 * @package    Diggin_Uri
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


class Diggin_Uri_Http
{
    /**
     * Getting "Real" URL not URI
     * Replace HTML'S relative path
     *  
     * using pecl_http 
     * 
     * @param string $url
     * @param string $baseUrl 
     *  (Not directoy, this param must set URL work as file(HTMLorXML))
     * @return string
     */
    public static function getAbsoluteUrl($url, $baseUrl)
    {
        //using pecl_http
        if (extension_loaded('http')) {
            static $baseUrl;
            if (array_key_exists('host', parse_url($url))) {
                return $url;
            } else {
                if (strpos(pathinfo(parse_url($baseUrl, PHP_URL_PATH), PATHINFO_DIRNAME), '/') === false) {
                    return http_build_url($baseUrl, array("path" => $url),
                    HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT);
                } else {            
                    return http_build_url($baseUrl, array("path" => $url), 
                    HTTP_URL_JOIN_PATH | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT);
                }
            }
        //Net_URL2 ver 0.2.0
        } else {
            if (!class_exists('Net_URL2')) require_once 'Net/URL2.php';
            static $neturl2;
            $neturl2 = new Net_URL2($baseUrl);
            return $neturl2->resolve(str_replace(chr(32), '%20', $url))->getUrl();
        } 
    }
}

