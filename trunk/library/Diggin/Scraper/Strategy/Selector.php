<?php
/**
 * Diggin - Library Of PHP
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

require_once 'Diggin/Scraper/Strategy/Abstract.php';

class Diggin_Scraper_Strategy_Selector extends Diggin_Scraper_Strategy_Abstract 
{    
    protected static $_adapter = null;
    protected static $_adapterconfig = null;

    public function __destruct() 
    {
       self::$_adapter = null;
       self::$_adapterconfig = null;
       parent::$_adapter = null;
    }
    
    public function setAdapter(Diggin_Scraper_Adapter_Interface $adapter)
    {
        self::$_adapter = $adapter;
    }
    
    public function setAdapterConfig($config)
    {
        self::$_adapterconfig = $config;
    }
    
    public function getAdapter()
    {
        if (isset(self::$_adapter)) {
            if(self::$_adapterconfig) self::$_adapter->setConfig(self::$_adapterconfig);
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
            if(self::$_adapterconfig) self::$_adapter->setConfig(self::$_adapterconfig);
        }

        return self::$_adapter;
    }

    /**
     * scraping with CssSelector
     * 
     * @param Zend_Http_Response $respose
     * @param string $process
     * @return array $results
     */
    public function scrape($respose, $process)
    {
        $simplexml = $this->getAdapter()->readData($respose);

        $dom = dom_import_simplexml($simplexml);

        require_once dirname(__FILE__).'/Selector/sfDomCssSelector.class.php';
        $selector = new sfDomCssSelector($dom);
        
        $results = array();       
        foreach ($selector->getElements($process->expression) as $result) {
            $results[] = simplexml_import_dom($result); 
        }
        
        return $results;
    }

    /**
     * get value with DSL
     * 
     * @param Diggin_Scraper_Context
     * @param Diggin_Scraper_Process
     * @return mixed
     */
    public function getValue($context, $process)
    {
        if (!isset($process->type)) {
            return $context->scrape($process);
        }
        
        $values = $context->scrape($process);

        //type
        if (strtoupper(($process->type)) === 'TEXT') {
            
            $strings = array();
            foreach ($values as $value) {
                //@todo strict 
                $value = strip_tags((string) str_replace('&amp;', '&', $value->asXML()));
                $value = str_replace(array(chr(10), chr(13)), '', $value);
                array_push($strings, $value);
            }
        } elseif (strtoupper(($process->type)) === 'PLAIN') {
            $strings = array();
            foreach ($values as $value) {
                array_push($strings, (string) str_replace('&amp;', '&', $value->asXML()));
            }
                
        } elseif (strpos($process->type, '@') === 0) {
            $strings = array();
            foreach ($values as $value) {
                //@todo
                if (($process->type == '@href' OR $process->type == '@src')
                    && method_exists($this->getAdapter(), 'getAbsoluteUrl')) {
                    array_push($strings, $this->getAdapter()->getAbsoluteUrl((string)$value[substr($process->type, 1)], self::$_adapterconfig['url']));
                    //require_once 'Diggin/Uri/Http.php';
                    //array_push($strings, Diggin_Uri_Http::getAbsoluteUrl((string)$value[substr($process->type, 1)], self::$_adapterconfig['url']));
                } else {
                    array_push($strings, (string) $value[substr($process->type, 1)]);
                }
            }
            
        } else {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            throw new Diggin_Scraper_Strategy_Exception("can not understand type :".$process->type);
        }
        
        //スクレイプ該当が1件だったときに、
        if (count($strings) === 1) {
            $strings = (string) array_shift($strings);
        }

        return $strings;
    }
}