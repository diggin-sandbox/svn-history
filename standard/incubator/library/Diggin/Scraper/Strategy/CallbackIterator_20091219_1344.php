<?php

class Fil extends FilterIterator
{
    public function accept()
    {
        if ($this->current() == 2) {
            return false;
        }
        return true;
    }
}

class Diggin_Scraper_Strategy_Callback extends IteratorIterator
{
    public function __construct(array $values)
    {
        return parent::__construct(new ArrayIterator($values));
    }

    public function current()
    {
        //return call_user_func(array($this->_strategy, $this->_type), parent::current());
        return call_user_func(array($this, 'testCall'), parent::current());
    }

    public function setCallback(Diggin_Scraper_Process $process, 
                                Diggin_Scraper_Strategy_Abstract $strategy)
    {
        $type = $process->getType();
        //$type = preg_replace('/^@/', 'at_', $type);

        //$this->_type = $type;
        $this->_process = $process;
        $this->_strategy = $strategy;
    }

    public function testCall($v)
    {
        return $v.'test';
    }
}

class Diggin_Scraper_Strategy_CallbackIterator implements IteratorAggregate
{
    private $_iterator;

    public function __construct(Diggin_Scraper_Strategy_Callback $callback)
    {
        $this->_iterator = $callback;

        //init filters
        //if ($filters = $callback->getProcess()->getFilters()) {
        //  $this->unshiftIterator($filters);
        //}
    }

    public function getIterator()
    {
        return $this->_iterator;
    }

    public function unshiftIterator($iteratorName)
    {
        $this->_iterator = new $iteratorName($this->_iterator);
    }
}

$c = new Diggin_Scraper_Strategy_Callback(array(1,2,3));
//$c->setCallback();
$i = new Diggin_Scraper_Strategy_CallbackIterator($c);
$i->unshiftIterator('Fil');
var_dump($i);

foreach ($i as $v) {
    var_dump($v);
}

exit;

class Diggin_Scraper_Strategy_Callbacks extends IteratorIterator
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


        $this->_type = $type;
        $this->_strategy = $strategy;
    }

    public function current()
    {
        $string = call_user_func(array($this->_strategy, $this->_type), parent::current());
        if ($filters = $process->getFilters()) {

        }
    }
}

$c = new Diggin_Scraper_Strategy_CallbackIterator(new ArrayIterator(array(1,2, 3)));
var_dump($c);
