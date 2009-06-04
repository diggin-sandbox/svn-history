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

/** Diggin_Scraper_Helper_Simplexml_SimplexmlAbstract **/
require_once 'Diggin/Scraper/Helper/Simplexml/HeadBaseHref.php';

/**
 * Helper for pagerize info
 *
 * @package    Diggin_Scraper
 * @subpackage Helper
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */
class Diggin_Scraper_Helper_Simplexml_Pagerize
        extends Diggin_Scraper_Helper_Simplexml_HeadBaseHref
{
    
    const HATOM_PAGEELEMENT = '//*[contains(concat(" ", @class, " "), " hentry ")]';
    const HATOM_NEXTLINK = '//link[contains(concat(" ", translate(normalize-space(@rel),"NEXT","next"), " "), " next ")] | //a[contains(concat(" ", translate(normalize-space(@rel),"NEXT","next"), " "), " next ")]';
    /**
     * @var Zend_Cache_Core
     */
    private static $_cacheCore;
    
    public function getNextLink($preferhAtom = true)
    {
    
    }
    
    //checks, 
    public function hAtomNextLink()
    {
        $this->getResource()->xpath(self::HATOM_NEXTLINK);
    }
}
