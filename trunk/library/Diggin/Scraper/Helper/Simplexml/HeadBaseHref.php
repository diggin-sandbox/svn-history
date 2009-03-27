<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://diggin.musicrider.com/LICENSE
 * 
 * @category   Diggin
 * @package    Diggin_Scraper
 * @subpackage Helper
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * Helper for Search Head-Base Tag
 *
 * @package    Diggin_Scraper
 * @subpackage Helper
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

require_once 'Diggin/Scraper/Helper/Abstract.php';

class Diggin_Scraper_Helper_Simplexml_HeadBaseHref  extends Diggin_Scraper_Helper_Abstract
{
    /**
     * Search Base Href
     * 
     * firefoxではbaseタグが複数記述されていた場合は、最後のものを考慮する。
     * スキーマがよろしくない場合は、その前のものを考慮
     * httpスキーマではない場合は無視される。
     *
     * @return mixed
     */
    public function direct()
    {
        if ($bases = $this->resource->xpath('//base[@href]')) {
            rsort($bases);
            require_once 'Zend/Uri.php';
            foreach ($bases as $base) {
                try {
                    $uri = Zend_Uri::factory((string) $base[@href]);
                    
                    return $uri;
                } catch (Zend_Uri_Exception $e) {
                    continue;
                }
            }
        }
        
        return false;
    }
}
