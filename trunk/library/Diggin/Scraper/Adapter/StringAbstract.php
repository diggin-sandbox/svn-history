<?php
/**
 * @see Diggin_Scraper_Adapter_Interface
 */
require_once 'Diggin/Scraper/Adapter/Interface.php';

abstract class Diggin_Scraper_Adapter_StringAbstract implements Diggin_Scraper_Adapter_Interface
{
    
    protected abstract function getString($response);

    /**
     * Reading Response as SimpleXmlElement
     * 
     * @return SimplXmlElement
     */
    public function readData($response)
    {
        $string = $this->getString($response);
        if (!is_string($string)) {
            require_once 'Diggin/Scraper/Adapter/Exception.php';
            throw new Diggin_Scraper_Adapter_Exception('adapter getString not return String');
        }
        
        return $string;
    }
}