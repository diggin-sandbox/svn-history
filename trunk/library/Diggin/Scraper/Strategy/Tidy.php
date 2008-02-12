<?php
require_once 'Diggin/Scraper/Strategy/Abstract.php';

class Diggin_Scraper_Strategy_Tidy extends Diggin_Scraper_Strategy_Abstract
{
    protected $config = array(
        'indent'         => false,
        'add-xml-decl'   => true,
        'output-xml'     => true,
    	'numeric-entities' => true,
    );
    
    protected function readData($body)
    {
        $simplexml = new SimpleXMLElement($this->getTidyValue($body));
        
        return $simplexml;
    }
    
    private function getTidyValue($body, $config = null)
    {
        $tidy = new tidy;
        if ($config !== null) $this->setConfig($config);

        $tidy->parseString($body, $this->config, 'utf8');
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