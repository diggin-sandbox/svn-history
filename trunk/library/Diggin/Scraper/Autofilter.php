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
    public static $filter;
    public static $prefixFlag;

    /**
     * @param Iterator $iterator
     * @param String $filter
     * @param boolean $prefixFlag
     */
    public function __construct(Iterator $iterator, $filter, $prefixFlag)
    {
        parent::__construct($iterator);
        self::$filter = $filter;
        self::$prefixFlag = $prefixFlag;
    }

    /**
     * accept 
     * 
     * @return boolean
     */
    public function accept()
    {
        $value = $this->current();
        
        if (function_exists(self::$filter)) {
            $filterValue = call_user_func(self::$filter, $value);
        } else if (!strstr(self::$filter, '_')) {
            require_once 'Zend/Filter.php';
            $filterValue = Zend_Filter::get($value, self::$filter);
        } else {
            require_once 'Zend/Loader.php';
            $filter = self::$filter;
            try {
                Zend_Loader::loadClass($filter);
            } catch (Zend_Exception $e) {
                require_once 'Diggin/Scraper/Exception.php';
                throw new Diggin_Scraper_Exception("Unable to load filter '$filter': {$e->getMessage()}");
            }
            $filter = new $filter();
            $filterValue = $filter->filter($value);
        }
        
        if (self::$prefixFlag === true) {
            if ($filterValue != $value) {
                return false;
            } else {
                return true;
            }
        } else {
             if ($filterValue != $value) {
                return true;
            } else {
                return false;
            }
        }
    }
}
