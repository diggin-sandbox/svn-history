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
 * @package    Diggin_Scraper
 * @subpackage FindHelper
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Helper for Search Head-Base Tag
 *
 * @package    Diggin_Scraper
 * @subpackage FindHelper
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Diggin_Scraper_FindHelper_HeadBase
{
    /**
     * Search Base Href
     * 
     * firefoxではbaseタグが複数記述されていた場合は、最後のものを考慮する。
     * スキーマがよろしくない場合は、その前のものを考慮
     * httpスキーマではない場合は無視される。
     *
     * @param SimpleXMLElement $simplexml
     * @return mixed
     */
    public function headBase(SimpleXMLElement $simplexml)
    {
        if ($bases = $simplexml->xpath('//base[@href]')) {
            krsort($bases);
            require_once 'Zend/Uri/Http.php';
            foreach ($bases as $base) {
                try {
                    $uri = Zend_Uri_Http::fromString((string) $base[@href]);
                    
                    return $uri;
                } catch (Zend_Uri_Exception $e) {
                    continue;
                }
            }
        }
        
        return false;
    }
}
