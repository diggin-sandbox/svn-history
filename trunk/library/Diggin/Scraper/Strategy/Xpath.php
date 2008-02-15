<?php
require_once 'Diggin/Scraper/Strategy/Abstract.php';

class Diggin_Scraper_Strategy_Xpath extends Diggin_Scraper_Strategy_Abstract 
{
    protected static $_adapter = null;

    public function setAdapter(Diggin_Scraper_Strategy_Xpath_Adapter_Interface $adapter)
    {
        self::$_adapter = $adapter;
    }

    public function getAdapter()
    {
        if (!self::$_adapter instanceof Diggin_Scraper_Strategy_Xpath_Adapter_Interface) {
            /**
             * @see Diggin_Scraper_Strategy_Xpath_Adapter
             */
            require_once 'Diggin/Scraper/Strategy/Xpath/Adapter/Tidy.php';
            self::$_adapter = new Diggin_Scraper_Strategy_Xpath_Adapter_Tidy();
        }

        return self::$_adapter;
    }
    
    /**
     * 
     * @param string $resposeBody
     * @return Object SimpleXMLElement
     */
    protected function readData($resposeBody)
    {
        return $this->getAdapter()->readData($resposeBody);
    }
    
    /**
     * 
     * @param string $resposeBody
     * @param string $process
     * @return void
     */
    public function scrape($resposeBody, $process)
    {
        $simplexml = $this->getAdapter()->readData($resposeBody);
 
        $results = array();        
        foreach ($simplexml->xpath($process) as $count => $result) {
            $results[] = $result; 
        }
        
        return $results;
    }
}
