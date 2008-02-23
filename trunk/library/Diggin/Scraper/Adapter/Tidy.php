<?php
require_once 'Diggin/Scraper/Adapter/Interface.php';

class Diggin_Scraper_Adapter_Tidy implements Diggin_Scraper_Adapter_Interface
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
     * @param Zend_Http_Response $response
     * @retrun Object SimpleXMLElement
     */
    public function readData($response)
    {
        $simplexml = new SimpleXMLElement($this->_getTidyValue($response->getBody()));
        
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
            throw new Diggin_Scraper_Adapter_Exception('Expected array parameter, given ' . gettype($config));

        foreach ($config as $k => $v)
            $this->config[strtolower($k)] = $v;

        return $this;
    }
}