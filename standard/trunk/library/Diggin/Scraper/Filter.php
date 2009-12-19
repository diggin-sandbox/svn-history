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
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */
class Diggin_Scraper_Filter
{  
    /**
     * Run filter
     *
     * @param array $values
     * @param array |  $filters
     * @param array $filterParams
     * @return array
     * @throws Diggin_Scraper_Filter_Exception
     */
    public static function run($values, $filters, $filterParams = null)
    {
        foreach ($filters as $filter) {
            
            $return = array();


            if ($filter instanceof Zend_Filter_Interface) {
                foreach ($values as $k => $value) {
                    $return[$k] = $filter->filter($value);
                }
            } else if (preg_match('/^[0-9a-zA-Z\0]/', $filter)) {
                if (function_exists($filter)) {
                    foreach ($values as $k => $value) {
                        $return[$k] = call_user_func($filter, $value);
                    }
                } elseif (!strstr($filter, '_')) {
                    require_once 'Zend/Filter.php';
                    require_once 'Zend/Version.php';
                    try {
                        foreach ($values as $k => $value) {
                            if (Zend_Version::compareVersion('1.9.0') >= 0) {
                                $return[$k] = Zend_Filter::get($value, $filter);
                            } else {
                                $return[$k] = Zend_Filter::filterStatic($value, $filter);
                            }
                        }
                    } catch (Zend_Exception $e) {
                        require_once 'Diggin/Scraper/Filter/Exception.php';
                        throw new Diggin_Scraper_Filter_Exception("Unable to load filter '$filter': {$e->getMessage()}");
                    }
                } else {
                    require_once 'Zend/Loader.php';
                    try {
                        Zend_Loader::loadClass($filter);
                    } catch (Zend_Exception $e) {
                        require_once 'Diggin/Scraper/Filter/Exception.php';
                        throw new Diggin_Scraper_Filter_Exception("Unable to load filter '$filter': {$e->getMessage()}");
                    }
                    $filter = new $filter();
                    foreach ($values as $k => $value) {
                        $return[$k] = $filter->filter($value);
                    }
                }
            } else {
                $prefix = substr($filter, 0, 1);
                
                //have
                if ($prefix === '*') {
                    require_once 'Diggin/Scraper/Filter/Iterator.php';
                    $filter = substr($filter, 1);
                    $filterds = new Diggin_Scraper_Filter_Iterator(new ArrayIterator($values), $filter, true);
                //not have
                } elseif ($prefix === '!') {
                    $filter = substr($filter, 1);
                    $filterds = new Diggin_Scraper_Filter_Iterator(new ArrayIterator($values), $filter, false);
                } elseif ($prefix === '/' or $prefix === '#') {
                    $filterds = new RegexIterator(new ArrayIterator($values), $filter);
                } elseif ($prefix === '$') {
                    $filterds = new RegexIterator(new ArrayIterator($values), $filter);
                    $filterds->setMode(RegexIterator::GET_MATCH);
                } else {
                    require_once 'Diggin/Scraper/Filter/Exception.php';
                    throw new Diggin_Scraper_Filter_Exception("Unkown prefix '$prefix'");
                }
                
                foreach($filterds as $k => $filterd) $return[$k] = $filterd;
            }
            
            $values = $return;
        }

        return $return;
    }
}
