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
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Diggin_Scraper_Context
{
    private $_strategy;
    
    /**
     * construct
     * 
     * @param Diggin_Scraper_Strategy_Abstract $strategy
     */
    public function __construct(Diggin_Scraper_Strategy_Abstract $strategy)
    {
        $this->_strategy = $strategy;
    }

    public function read()
    {
        return $this->_strategy->readResource();
    }
}
