<?php
/**
 * Diggin - Library Of PHP
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * 
 * @category   Diggin
 * @package    Diggin_Http
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
     * @param string
     * @return string
     */
    public static function getAbsoluteUrl($url, $base_url)
    {
        $parse = parse_url($url);
        if (isset($parse["host"])) {
            $build = $url;
        } else {
            $uridir = pathinfo(parse_url($base_url, PHP_URL_PATH), PATHINFO_DIRNAME);
            $slash = strpos($uridir, '/');
            if ($slash === false) {
                $build = http_build_url($base_url, array("path" => $url,),
                HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT);
            } else {            
                $build = http_build_url($base_url, array("path" => $url,), 
                HTTP_URL_JOIN_PATH | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT);
            }
        }
        
        return $build;
    }
}