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

require_once 'Diggin/Scraper/Strategy/Abstract.php';
if(!class_exists('sfDomCssSelector')) require_once 'symfony/util/sfDomCssSelector.class.php';
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

        return self::extract($simplexml, $process);
    }
    
    public function extract($simplexml, $process)
    {
        $dom = dom_import_simplexml($simplexml);
        
        $selector = new sfDomCssSelector($dom);
        
        $results = array();       
        foreach ($selector->getElements($process->expression) as $result) {
            $results[] = simplexml_import_dom($result); 
        }
        
        if (count($results) === 0) {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            throw new Diggin_Scraper_Strategy_Exception("couldn't find By Selector, Process : $process");            
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
    public function getValue($values, $process)
    {        
        //type
        if (strtoupper(($process->type)) === 'RAW'){
            $strings = $values;
        } elseif (strtoupper(($process->type)) === 'TEXT') {
            
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
            require_once 'Diggin/Uri/Http.php';
            foreach ($values as $value) {
                if (($process->type == '@href' OR $process->type == '@src')) {
                    array_push($strings, Diggin_Uri_Http::getAbsoluteUrl((string)$value[substr($process->type, 1)], self::$_adapterconfig['url']));
                } else {
                    array_push($strings, (string) $value[substr($process->type, 1)]);
                }
            }
            
        } else {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            throw new Diggin_Scraper_Strategy_Exception("Unknown value type :".$process->type);
        }
        
        return $strings;
    }

}
