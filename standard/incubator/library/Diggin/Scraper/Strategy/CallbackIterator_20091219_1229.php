<?php

class Fil extends FilterIterator
{
    private $ite;
    public function __construct($ite)
    {
        $this->ite = $ite;
    }

    public function accept()
    {
        if ($this->current() == 2) {
            return false;
        }
        return true;
    }
}

class Diggin_Scraper_Strategy_CallbackIterator implements Iterator
{
    private $_iterator;

    public function __construct(array $a)
    {
        $this->_iterator = new ArrayIterator($a);
    }

    public function getInnerIterator()
    {
        $this->_iterator;
    }

    public function current()
    {
        return call_user_func(array($this, 'testCall'), $this->_iterator->current());
    }

    public function next()
    {
        return $this->_iterator->next();
    }

    public function valid()
    {
        return $this->_iterator->valid();
    }

    public function rewind()
    {
        return $this->_iterator->rewind();
    }

    public function key()
    {
        return $this->_iterator->key();
    }

    public function testCall($v)
    {
        return $v;
    }

    public function unshiftIterator($iteratorName)
    {
        $this->_iterator = new $iteratorName($this->_iterator);
    }
}

$c = new Diggin_Scraper_Strategy_CallbackIterator(array(1,2,3));
$c->unshiftIterator('Fil');

var_dump($c);
echo $c->current();
foreach ($c as $v) {
//var_dump($v);
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
