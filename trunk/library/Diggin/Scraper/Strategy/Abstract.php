<?php

abstract class Diggin_Scraper_Strategy_Abstract {
    private $_body;
    
    /**
     * construct
     * 
     * @return 
     */
    public function __construct($body)
    {
        $this->_body = $body;
    }
    
    /*
     * 
     */
    public function getData()
    {
        //if !is_readble($this->getBody)...
        
        return $this->readData($this->getBody());
    }
    
    public function scrapedData($process)
    {   
        return $this->scrape($this->getBody(), $process);
    }
    
    
    public function getBody()
    {
        return $this->_body;
    }
    
    protected abstract function readData($body);
    
    protected abstract function scrape($body, $process);
    
}