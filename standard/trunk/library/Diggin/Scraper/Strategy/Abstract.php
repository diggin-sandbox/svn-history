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
/** Diggin_Scraper_Callback_Evaluator */
require_once 'Diggin/Scraper/Callback/Evaluator.php';
/** Diggin_Scraper_Callback_Filter */
require_once 'Diggin/Scraper/Callback/Filter.php';

abstract class Diggin_Scraper_Strategy_Abstract
{
    /**
     * response
     *
     * @var Zend_Http_Response
     */
    private $_response;
    
    /**
     * Response Adapter
     *
     * @var Diggin_Scraper_Adapter_Interface
     */
    protected $_adapter;

    /**
     * Adapted Resouce
     *
     * @var Diggin_Scraper_Adapter_Interface
     */
    private $_adaptedResource;
    
    protected abstract function setAdapter(Diggin_Scraper_Adapter_Interface $adapter);
    
    protected abstract function getAdapter();
    
    /**
     * construct
     * 
     * @param Zend_Http_Response
     */
    public function __construct($response)
    {
        $this->_response = $response;
    }
    
    /**
     * Read resource from adapter'read
     * 
     * @return mixed
     */
    public function readResource()
    {
        if (!$this->_adaptedResource) {
            $this->_adaptedResource = $this->getAdapter()->readData($this->getResponse());
        }
        return $this->_adaptedResource;
    }

    
    public function getResponse()
    {
        return $this->_response;
    }

    protected abstract function getEvaluator();
    
    protected abstract function extract($values, $process);

    /**
     * get values (Recursive)
     *
     * @param mixed $context 
     *          [first:Diggin_Scraper_Context
     *           second:array]
     * @param Diggin_Scraper_Process $process
     * @return mixed $values
     * @throws Diggin_Scraper_Strategy_Exception
     * @throws Diggin_Scraper_Filter_Exception
     */
    public function getValues($context, $process)
    {
 
        if ($context instanceof Diggin_Scraper_Context) {
            $values = $this->extract($context->read(), $process);
        } else {
            try {
                $values = $this->extract($context, $process);
            } catch (Diggin_Scraper_Strategy_Exception $e) {
                return false;
            }
        }

       if ($process->getType() instanceof Diggin_Scraper_Process_Aggregate) {
            $returns = false;
            foreach ($values as $count => $val) {
                foreach ($process->getType() as $proc) {
                    if (false !== $getval = $this->getValues($val, $proc)) {
                        $returns[$count][trim($proc->getName())] = $getval;
                    }
                }

                if (($process->getArrayFlag() === false) && $count === 0) {
                    if(is_array($returns)) {
                        $returns = current($returns); break;
                    }
                }
            }

            return $returns;
        }

        $callback = new Diggin_Scraper_Callback_Evaluator($values, $process, $this->getEvaluator());
        $values = new Diggin_Scraper_Callback_Filter($callback);

        if ($process->getArrayFlag() === false) {
            $values = new LimitIterator($values, 0, 1);
        }

        $iterator_to_array = true;
        //$iterator_to_array = false;

        //$values = iterator_to_array($iterator); 
        //if ($values === array()) return false;
 
        if ($iterator_to_array === true) {
            $values = iterator_to_array($values); 
            if ($process->getArrayFlag() == false) {
                return current($values);
            }
        }
        
 
        return $values;
    }
}
