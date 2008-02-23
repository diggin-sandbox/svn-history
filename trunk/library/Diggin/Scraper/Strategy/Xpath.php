<?php
require_once 'Diggin/Scraper/Strategy/Abstract.php';

class Diggin_Scraper_Strategy_Xpath extends Diggin_Scraper_Strategy_Abstract 
{
    protected static $_adapter = null;

    public function __destruct() {
       self::$_adapter = null;
       parent::$_adapter = null;
   }
    
    public function setAdapter(Diggin_Scraper_Adapter_Interface $adapter)
    {
        self::$_adapter = $adapter;
    }

    public function getAdapter()
    {
        if(isset(self::$_adapter)){
            return self::$_adapter;
        }
        
        //コンストラクタで設定されてた時用
        if (parent::$_adapter instanceof Diggin_Scraper_Adapter_Interface) {
            return parent::$_adapter;
        } else { 
            /**
             * @see Diggin_Scraper_Adapter
             */
            require_once 'Diggin/Scraper/Adapter/Htmlscraping.php';
            self::$_adapter = new Diggin_Scraper_Adapter_Htmlscraping();
        }

        return self::$_adapter;
    }
    
    /**
     * 
     * @param Zend_Http_Response $respose
     * @return Object SimpleXMLElement
     */
    protected function readData($respose)
    {
        //@todo if return !simplexml throw
        return $this->getAdapter()->readData($respose);
    }
    
    /**
     * scraping with Xpath
     * 
     * @param Zend_Http_Response $respose
     * @param string $process
     * @return void
     */
    public function scrape($respose, $process)
    {
        $simplexml = $this->getAdapter()->readData($respose);

        $results = array();
        foreach ($simplexml->xpath($process) as $count => $result) {
            $results[] = $result; 
        }
        
        return $results;
    }
}
