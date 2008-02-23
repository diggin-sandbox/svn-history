<?php
abstract class Diggin_Scraper_Strategy_Abstract {
    private static $_response;
    //private static $_adapter;
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
    
    /**
     * 
     */
    public function getData()
    {
        //if !is_readble($this->getBody)...
        
        return $this->readData($this->getResponse());
    }
    
    public function scrapedData($process)
    {   
        return $this->scrape($this->getResponse(), $process);
    }
   
    public function getResponse()
    {
        return self::$_response;
    }
    
    /**
     * 
     */
    protected abstract function readData($response);
    
    protected abstract function scrape($response, $process);
    
}