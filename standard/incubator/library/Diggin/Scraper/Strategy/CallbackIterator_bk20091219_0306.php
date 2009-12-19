<?php

class Diggin_Scraper_Strategy_CallbackIterator implements IteratorAggregate
    //extends ArrayIterator
{
    // values
    private $_iterator;

    private $_strategy;
    private $_type;

    public function __construct(array $values)
    {
        $this->_iterator = new ArrayIterator($values);
    }

    // implement IteratorAggregate
    public function getIterator()
    {
        return $this->_iterator;
    }

    public function unshiftIterator(Iterator $iterator)
    {

    }

    public function setCallback(Diggin_Scraper_Process $process, 
                                Diggin_Scraper_Strategy_Abstract $strategy)
    {
        $type = $process->getType();
        $type = preg_replace('/^@/', 'at_', $type);

        $this->_type = $type;
        $this->_strategy = $strategy;
    }

    public function current()
    {
        //return call_user_func(array($this->_strategy, $this->_type), parent::current());
        return call_user_func(array($this->_strategy, $this->_type), $this->getIterator()->current());
    }
}
