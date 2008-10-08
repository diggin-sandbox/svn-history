<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * 
 * @category   Diggin
 * @package    Diggin_Scraper
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
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
     * @return SimpleXMLElement
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