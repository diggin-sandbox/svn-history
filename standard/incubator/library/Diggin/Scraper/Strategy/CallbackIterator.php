<?php
require_once 'Diggin/Scraper/Process.php';

class Diggin_Scraper_Strategy_Callback extends ArrayIterator
{
    private $_process;
    private $_strategy;

    public function __construct(array $values, 
                                Diggin_Scraper_Process $process, 
                                Diggin_Scraper_Strategy_Abstract $strategy = null)
    {
        $this->_process = $process;
        $this->_strategy = $strategy;

        return parent::__construct($values);
    }

    public function current()
    {
        //return call_user_func(array($this->_strategy, $this->_type), parent::current());
        return call_user_func(array($this, 'testCall'), parent::current());
    }

    public function getProcess()
    {
        return $this->_process;
    }

    public function testCall($v)
    {
        return $v.'test';
    }
}

class Diggin_Scraper_Strategy_CallbackIterator extends IteratorIterator
{
    private $_iterator;

    public function __construct(Diggin_Scraper_Strategy_Callback $callback)
    {
        $this->_iterator = $callback;

        return parent::__construct($this->_iterator);
    }

    public function getInnerIterator()
    {
        if ($filters = $callback->getProcess()->getFilters()) {
            $this->setFilters($filters);
        }

        return $this->_iterator;
    }
    
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

            if ($prefix = '/') {
                $iterator = new RegexIterator($this->_iterator, $filter);
            } elseif ($prefix = '$') {
                $iterator = new RegexIterator($this->_iterator, $filter);
                $iterator->setMode(RegexIterator::GET_MATCH);
            }
        }

        return $iterator;
    }

}

class Diggin_Scraper_Filter extends IteratorIterator
{
    private $_filter = array();

    public function setFilter($filter)
    {
        if (preg_match('/^[0-9a-zA-Z\0]/', $filter)) {
            //user-func or lambda
            if (function_exists($filter)) {
                $this->_filter = $filter;
            } else {
                if (!strstr($filter, '_')) {
                    $filter = "Zend_Filter_$filter";
                }
                require_once 'Zend/Loader.php';
                try {
                    Zend_Loader::loadClass($filter);
                } catch (Zend_Exception $e) {
                    require_once 'Diggin/Scraper/Filter/Exception.php';
                    throw new Diggin_Scraper_Filter_Exception("Unable to load filter '$filter': {$e->getMessage()}");
                }
                $filter = new $filter();
                $this->_filter['filter'] = $filter;
            }
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

class Fil extends FilterIterator { public function accept() {return ($this->current() == 2 )?:false;}} 
function fil(){return 1;}
$process = new Diggin_Scraper_Process;
$process->setFilters(array('$(a+).*$'));


$c = new Diggin_Scraper_Strategy_Callback(array('1a','aaa2', '3', 'sss', '1a'), $process);
$i = new Diggin_Scraper_Strategy_CallbackIterator($c);
//$i = new LimitIterator($i, 0, 2);
var_dump($i);

foreach ($i as $v) {
    var_dump($v);
}
