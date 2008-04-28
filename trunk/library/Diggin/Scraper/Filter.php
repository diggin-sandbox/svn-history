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
    public static function run($values, $filter, $filterParams = null)
    {
        $argValues = $values;
        
        if (!is_array($values)) {
            $values = array($values);
        }
        
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
        
        if (!is_array($argValues)) {
            $return = (string) array_shift($return);
        }
        
        return $return;
    }
}
