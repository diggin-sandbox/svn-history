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
        return $this->readData($this->getBody());
    }
    
    public function getBody()
    {
        return $this->_body;
    }
    
    protected abstract function readData($body);
    
}