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
    public function __construct(Diggin_Scraper_Strategy_Callback $callback)
    {
        if ($filters = $callback->getProcess()->getFilters()) {
            $callback = $this->getFilters($callback, $filters);
        }

        return parent::__construct($callback);
    }
    
    protected function getFilters($iterator, $filters)
    {
        foreach ($filters as $filter) {
            $iterator = Diggin_Scraper_Filter::factory($iterator, $filter);
        }

        return $iterator;
    }

}
