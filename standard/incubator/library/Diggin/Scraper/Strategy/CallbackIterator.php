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

require_once 'Diggin/Scraper/Filter.php';

class Diggin_Scraper_Strategy_CallbackIterator extends IteratorIterator
{
    private $_iterator;

    public function __construct(Diggin_Scraper_Strategy_Callback $callback)
    {
        $this->_iterator = $callback;

        if ($filters = $callback->getProcess()->getFilters()) {
            $this->setFilters($filters);
        }

        return parent::__construct($this->_iterator);
    }

    /*
    public function getInnerIterator()
    {
        return $this->_iterator;
    }
    */
    
    public function setFilters($filters)
    {
        foreach ($filters as $filter) {
            $this->_iterator = $this->_getFilter($filter);
        }
    }

    protected function _getFilter($filter)
    {
        if ( ($filter instanceof Zend_Filter_Interface) or 
             (preg_match('/^[0-9a-zA-Z\0]/', $filter)) ) {
            $iterator = new Diggin_Scraper_Filter($this->_iterator);
            $iterator->setFilter($filter);
        } else {
            $prefix = $filter[0];

            if ($prefix === '/' or $prefix === '#') {
                $iterator = new RegexIterator($this->_iterator, $filter);
            } elseif ($prefix === '$') {
                $iterator = new RegexIterator($this->_iterator, $filter);
                $iterator->setMode(RegexIterator::GET_MATCH);
            }
        }

        return $iterator;
    }

}
