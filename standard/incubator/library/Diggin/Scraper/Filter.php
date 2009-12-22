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

class Diggin_Scraper_Filter extends IteratorIterator
{
    private $_filter = array();

    public function setFilter($filter)
    {
        //user-func or lambda
        if (is_callable($filter)) {
            $this->_filter = $filter;
        } else {
            if (!strstr($filter, '_')) {
                $filter = "Zend_Filter_$filter";
            }

            require_once 'Zend/Loader.php';
            try {
                Zend_Loader::loadClass($filter);
                $filter = new $filter();
            } catch (Zend_Exception $e) {
                require_once 'Diggin/Scraper/Filter/Exception.php';
                throw new Diggin_Scraper_Filter_Exception("Unable to load filter '$filter': {$e->getMessage()}");
            }

            $this->_filter['filter'] = $filter;
        }
    }

    public function current()
    {
        return call_user_func(is_array($this->_filter) ? 
                                array(current($this->_filter), key($this->_filter)) : 
                                $this->_filter, 
                              parent::current());
    }
}
