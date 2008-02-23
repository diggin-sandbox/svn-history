<?php
require_once 'Diggin/Scraper/Adapter/Interface.php';

class Diggin_Scraper_Adapter_Raw implements Diggin_Scraper_Adapter_Interface
{
    protected $config = array();
    
    /**
     * Readdata as Raw
     * 
     * @param string $response
     * @retrun Object Raw
     */
    public function readData($response)
    {   
        return $response->getBody();
    }

    public function setConfig($config = array())
    {
        if (! is_array($config))
            throw new Diggin_Scraper_Adapter_Exception('Expected array parameter, given ' . gettype($config));

        foreach ($config as $k => $v)
            $this->config[strtolower($k)] = $v;

        return $this;
    }
}