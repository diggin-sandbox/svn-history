<?php
/**
 * @see Diggin_Scraper_Adapter_Interface
 */
require_once 'Diggin/Scraper/Adapter/Interface.php';

abstract class Diggin_Scraper_Adapter_SimplexmlAbstract implements Diggin_Scraper_Adapter_Interface
{

    protected abstract function getSimplexml($response);

    /**
     * Reading Response as SimpleXmlElement
     * 
     * @return SimplXmlElement
     */
    public function readData($response)
    {
        
        try {
            $simplexml = $this->getSimplexml($response);
        } catch (Exception $e){
            require_once 'Diggin/Scraper/Adapter/Exception.php';
            throw new Diggin_Scraper_Adapter_Exception($e);
        }
        
        if (!$simplexml instanceof SimpleXMLElement) {
            require_once 'Diggin/Scraper/Adapter/Exception.php';
            throw new Diggin_Scraper_Adapter_Exception('adapter getSimplexml not return SimpleXMLElement');
        }
        
        return $simplexml;
    }
}