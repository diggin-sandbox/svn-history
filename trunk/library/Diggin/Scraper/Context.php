<?php
/**
 * 
 */
class Diggin_Scraper_Context {
    private $_strategy;
    
    /**
     * construct
     * 
     * @param Diggin_Scraper_Strategy_Abstract $strategy
     */
    public function __construct(Diggin_Scraper_Strategy_Abstract $strategy)
    {
        $this->_strategy = $strategy;
    }
    
    /**
     * getting getItems
     * 
     * @return void
     */
    public function getItems()
    {
        return $this->_strategy->getData();
    }
    
    public function scrape($process)
    {
        return $this->_strategy->scrapedData($process);
    }
}