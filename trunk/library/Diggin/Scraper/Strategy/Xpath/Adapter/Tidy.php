<?php
require_once 'Diggin/Scraper/Strategy/Xpath/Adapter/Interface.php';

class Diggin_Scraper_Strategy_Xpath_Adapter_Tidy implements Diggin_Scraper_Strategy_Xpath_Adapter_Interface
{
    protected $config = array(
        'indent'         => false,
        'add-xml-decl'   => true,
        'output-xml'     => true,
    	'numeric-entities' => true,
    );

    /**
     * Readdata as SimpleXml
     * 
     * @param string $responseBody
     * @retrun Object SimpleXMLElement
     */
    public function readData($responseBody)
    {
        $simplexml = new SimpleXMLElement($this->_getTidyValue($responseBody));
        
        return $simplexml;
    }
    
    private function _getTidyValue($responseBody)
    {
        $tidy = new tidy;
        $tidy->parseString($responseBody, $this->config, 'utf8');
        $tidy->cleanRepair();
        
        return $tidy->value;
    }
    
    public function setConfig($config = array())
    {
        if (! is_array($config))
            throw new Diggin_Scraper_Strategy_Exception('Expected array parameter, given ' . gettype($config));

        foreach ($config as $k => $v)
            $this->config[strtolower($k)] = $v;

        return $this;
    }
}