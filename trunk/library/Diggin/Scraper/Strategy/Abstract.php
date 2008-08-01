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

abstract class Diggin_Scraper_Strategy_Abstract {
    private static $_response;
    protected static $_adapter;
    
    protected abstract function setAdapter(Diggin_Scraper_Adapter_Interface $adapter);
    
    protected abstract function getAdapter();
    
    /**
     * construct
     * 
     * @param Zend_Http_Response
     * @param  
     */
    public function __construct($response, $adapter = null)
    {
        self::$_response = $response;
        if(is_null($adapter)) {
            self::$_adapter = $this->getAdapter();
        } else {
            self::$_adapter = $adapter;
        }
    }
    
    public function scrapedData($process)
    {   
        return $this->scrape($this->getResponse(), $process);
    }
   
    public function getResponse()
    {
        return self::$_response;
    }

    protected abstract function scrape($response, $process);
    
    protected abstract function getValue($values, $process);
    
    protected abstract function extract($values, $process);

    public function getValues($context, $process)
    {
        if (!isset($process->type)) {
            return $context->scrape($process);
        }
        
        if ($context instanceof Diggin_Scraper_Context) {
            $values = $context->scrape($process);
        } else {
            try {
                $values = $this->extract($context, $process);
            } catch (Diggin_Scraper_Strategy_Exception $e) {
                echo "error";echo PHP_EOL; return false;
            }
        }
        
        if ($process->type instanceof scraper) {
            foreach ($values as $count => $val) {
                foreach ($process->type->processes as $proc) {
                    //@todo 値がとれなかったとき、格納しないか空かどうかはconfigでやるべきかな
                    if (false !== $getval = $this->getValues($val, $proc)) {
                        $returns[$count][$proc->name] = $getval;
                    }
                }
                
                if (($process->arrayflag === false) && $count === 0) {
                    $returns = current($returns); break;
                }
            }
            
           return $returns;
        }
        
        $values = $this->getValue($values, $process);
        
        if ($process->arrayflag === false && strtoupper($process->type) === 'RAW') {
            $values = current($values);
        } elseif ($process->arrayflag === false) {
            $values = (string) current($values);
        }
        
        if ($process->filters) {
            require_once 'Diggin/Scraper/Filter.php';
            $values = Diggin_Scraper_Filter::run($values, $process->filters);
        }
        
        return $values;
    }
}