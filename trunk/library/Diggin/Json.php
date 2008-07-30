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
 * @package    Diggin_Json
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

class Diggin_Json 
{
    const TYPE_ARRAY = 1;
    const TYPE_SCRAPEROBJECT = 2;
    const TYPE_WEBSCRAPERIDE = 3;

    /**
     * decode 
     *
     * @param string $encodedValue
     * @param int $objectDecodeType
     * @param int $encodeType
     * @return mixed $decodes
     */
    public static function decode($encodedValue, 
                                  $objectDecodeType = Diggin_Json::TYPE_ARRAY, 
                                  $encodeType = Diggin_Json::TYPE_WEBSCRAPERIDE)
    {        
        $decodes = Zend_Json::decode(self::reEncode($encodedValue, $encodeType));
        if ($objectDecodeType === Diggin_Json::TYPE_SCRAPEROBJECT) {
            foreach ($decodes as $keys => $decode) {
                if(is_array(current($decode))) {
                    $scraper = new scraper();
                    foreach (current($decode) as $key => $val) {
                        foreach ($val as $k => $v) {
                            if ((substr($k, -2) == '[]')) {
                                $k = substr($k, 0, -2);
                                $arrayflag = true;
                            } else {
                                $arrayflag = false;
                            }
                            
                            $scraper->processes[] = new Diggin_Scraper_Process($key, $k, $arrayflag, $v);
                        }
                    }
                    
                    if ((substr(trim(key($decode)), -2) == '[]')) {
                        $name = substr(trim(key($decode)), 0, -2);
                        $arrayflag = true;
                    } else {
                        $name = trim(key($decode));
                        $arrayflag = false;
                    }
                    
                $processes = new Diggin_Scraper_Process($keys, $name, $arrayflag, $scraper);
                }
            }
            
            return $processes;
        }
        
        return $decodes;
    }

    /**
     * reEncode
     *
     * @param string $valueToEncode
     * @param int $encodeType
     * @return string $json
     */
    public static function reEncode($valueToEncode, $encodedType = Diggin_Json::TYPE_WEBSCRAPERIDE)
    {
        $json = str_replace(array(chr(10), chr(13)), '', $valueToEncode);
        $json = str_replace('  ', '', $json);
        $json = str_replace('\'', '"', $json);
        $pattern = array('/{(\w+)/i', '/,(\w+)/i');
        $replacement = array('{"${1}"', ',"${1}"');
        $json = preg_replace($pattern, $replacement, $json);
        $json = str_replace('"[]', '[]"', $json);
        
        return $json;
    }
}
