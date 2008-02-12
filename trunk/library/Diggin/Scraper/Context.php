<?php
/**
 * 
 */
class Diggin_Scraper_Context {
    private $_strategy;
    
    /**
     * construct
     * 
     * @param Diggin_Scraper_Strategy_SimpleXmlStrategy $strategy
     */
    public function __construct(Diggin_Scraper_Strategy_Abstract $strategy)
    {
        $this->_strategy = $strategy;
    }
    
    /**
     * getting Simple Xml
     * 
     * @return SimpleXml 
     */
    public function getSimpleXml()
    {
        return $this->_strategy->getData();
    }
}
