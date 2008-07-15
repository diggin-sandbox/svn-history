<?php
/**
 * Diggin - Library Of PHP
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
    
    protected abstract static function extract($values, $process);

    public function getValues($context, $process)
    {
        if (!isset($process->type)) {
            return $context->scrape($process);
        }
        
        if ($context instanceof Diggin_Scraper_Context) {
            $values = $context->scrape($process);
        } else {
            $values = $this->extract($context, $process);
        }
                
        if ($process->type instanceof scraper) {
            foreach ($values as $count => $val) {
                foreach ($process->type->processes as $proc) {
                    $returns[$count][$proc->name] = $this->getValues($val, $proc);
                }
                
                if (($process->arrayflag === false) && $count === 0) break;
            }
           return $returns;
        }
        
        $values = $this->getValue($values, $process);
        
        if ($process->arrayflag === false && strtoupper($process->type) === 'RAW') {
            $values = array_shift($values);
        } elseif ($process->arrayflag === false) {
            $values = (string) array_shift($values);
        }
        
        if ($process->filters) {
            require_once 'Diggin/Scraper/Filter.php';
            $values = Diggin_Scraper_Filter::run($values, $process->filters);
        }
        
        return $values;
    }
}