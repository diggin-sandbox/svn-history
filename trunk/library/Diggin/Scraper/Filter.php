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
class Diggin_Scraper_Filter
{
    private static $_values;
    private static $_filter;
    private static $_filterParams;
    
    /**
     * @param  
     */
    public function __construct($values, $filter, $filterParams = null)
    {
        self::$_values = $values;
        self::$_filter = $filter;
        self::$_filterParams = $filterParams;
    }
    
    public static function runFilter()
    {
        $values = call_user_func_array(self::$_filter, self::$_values);
        
        return $values;
    }
}