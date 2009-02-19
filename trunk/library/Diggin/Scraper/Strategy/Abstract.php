<?php
/**
 * Diggin - Simplicity PHP Library
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

abstract class Diggin_Scraper_Strategy_Abstract
{
    /**
     * response
     *
     * @var Zend_Http_Response
     */
    private static $_response;
    
    /**
     * Response Adapter
     *
     * @var Diggin_Scraper_Adapter_Interface
     */
    protected $_adapter;
    
    protected abstract function setAdapter(Diggin_Scraper_Adapter_Interface $adapter);
    
    protected abstract function getAdapter();
    
    /**
     * construct
     * 
     * @param Zend_Http_Response
     * @param Diggin_Scraper_Adapter_Interface
     */
    public function __construct($response, $adapter = null)
    {
        self::$_response = $response;
        if (is_null($adapter)) {
            $this->_adapter = $this->getAdapter();
        } else {
            $this->_adapter = $adapter;
        }
    }
    
    public function readResource()
    {
        return $this->getAdapter()->readData($this->getResponse());
    }

   
    public function getResponse()
    {
        return self::$_response;
    }

    protected abstract function getValue($values, $process);
    
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
                //@todo debug::dump('ERROR') vs E_USER_NOTICE vs $_error[]
                return false;
            }
        }
 
       if ($process->getType() instanceof Diggin_Scraper_Process_Aggregate) {
            $returns = false;
            foreach ($values as $count => $val) {
 
                //if ($process->getType()->getStrategyName()) {
                    //var_dump(class_implements($process->getType()->getStrategyName()));
                    //echo $process->getType()->;
                //}
 
                foreach ($process->getType() as $proc) {
                    //@todo 値がとれなかったとき、格納しないかnullかどうかはconfigでやるべきかな
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
 
        $values = $this->getValue($values, $process);
 
        if ($values === array()) return false;
 
        if ($process->getFilters()) {
            require_once 'Diggin/Scraper/Filter.php';
            $values = Diggin_Scraper_Filter::run($values, $process->getFilters());
        }
 
 
        if ($process->getArrayFlag() === false) {
            $values = current($values);
        }
 
        return $values;
    }
}
