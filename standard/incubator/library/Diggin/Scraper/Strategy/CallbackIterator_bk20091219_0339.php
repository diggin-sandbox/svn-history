<?php

class Diggin_Scraper_Strategy_CallbackIterator extends FilterIterator
{

    private $_strategy;
    private $_type;

    public function unshiftIterator(Iterator $iterator)
    {

    }

    public function setCallback(Diggin_Scraper_Process $process, 
                                Diggin_Scraper_Strategy_Abstract $strategy)
    {
        $type = $process->getType();
        $type = preg_replace('/^@/', 'at_', $type);

        //if ($filters = $process->getFilters()){}

        $this->_type = $type;
        $this->_strategy = $strategy;
    }

    public function current()
    {
        return call_user_func(array($this->_strategy, $this->_type), $this->current());
    }
}

$c = new Diggin_Scraper_Strategy_CallbackIterator(array(1,2, 3));
