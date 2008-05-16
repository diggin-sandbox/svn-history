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
 * @package    Diggin_Scraper
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Diggin_Scraper_Autofilter extends FilterIterator
{
    /**
     * @param  Iterator $iterator
     */
    public function __construct(Iterator $iterator)
    {
        parent::__construct($iterator);
    }
 
    /**
     * accept 
     * 
     * @return boolean
     */
    public function accept()
    {
        $value = $this->current();
        if (preg_match('/2007/', $value)) {
            return true; 
        } else {
            return false;
        }
    }
}
