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
class Diggin_Scraper_Filter
{  
    /**
     * run filter
     *
     * @param array $values
     * @param array |  $filters
     * @param unknown_type $filterParams
     * @return unknown
     */
    public static function run($values, $filters, $filterParams = null)
    {        
        foreach ($filters as $filter) {
            
            $return = array();

            if (preg_match('/^[0-9a-zA-Z]/', $filter)) {
                if (function_exists($filter)) {
                    foreach ($values as $value) {
                        $return[] = call_user_func($filter, $value);
                    }
                } elseif (!strstr($filter, '_')) {
                    require_once 'Zend/Filter.php';
                    foreach ($values as $value) {
                        $return[] = Zend_Filter::get($value, $filter);
                    }
                } else {
                    require_once 'Zend/Loader.php';
                    try {
                        Zend_Loader::loadClass($filter);
                    } catch (Zend_Exception $e) {
                        require_once 'Diggin/Scraper/Exception.php';
                        throw new Diggin_Scraper_Exception("Unable to load filter '$filter': {$e->getMessage()}");
                    }
                    $filter = new $filter();
                    foreach ($values as $value) {
                        $return[] = $filter->filter($value);
                    }
                }
            } else {
                $prefix = substr($filter, 0, 1);
                
                //have
                if ($prefix === '*') {
                    require_once 'Diggin/Scraper/Autofilter.php';
                    $filter = substr($filter, 1);
                    $filterds = new Diggin_Scraper_Autofilter(new ArrayIterator($values), $filter, true);
                //not have
                } elseif ($prefix === '!') {
                	$filter = substr($filter, 1);
                    $filterds = new Diggin_Scraper_Autofilter(new ArrayIterator($values), $filter, false);
                } elseif ($prefix === '/' or $prefix === '#') {
                	$filterds = new RegexIterator(new ArrayIterator($values), $filter);
                } else {
                	require_once 'Diggin/Scraper/Exception.php';
                	throw new Diggin_Scraper_Exception("couldn't auto-filtering: prefix with $prefix : {$e->getMessage()}");
                }
                
                foreach($filterds as $filterd) $return[] = $filterd;
            }
            
            $values = $return;
        }

        return $return;
    }
}
