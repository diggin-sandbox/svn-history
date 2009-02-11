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

/**
 * @see Diggin_Scraper_Strategy_Abstract
 */
require_once 'Diggin/Scraper/Strategy/Abstract.php';
/**
 * @see Zend_Dom_Query_Css2Xpath
 */
require_once 'Zend/Dom/Query/Css2Xpath.php';

/**
 * @see Diggin_Uri_Http
 */
require_once 'Diggin/Uri/Http.php';

/**
 * @see Diggin_Scraper_Helper_HeadBase
 */
require_once 'Diggin/Scraper/FindHelper/HeadBaseHref.php';

class Diggin_Scraper_Strategy_Flexible extends Diggin_Scraper_Strategy_Abstract 
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
            if (self::$_adapterconfig) self::$_adapter->setConfig(self::$_adapterconfig);
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
     * scraping with Xpath
     * 
     * @param Zend_Http_Response $respose
     * @param string $process
     * @return array $results
     */
    public function scrape($response, $process)
    {
        $simplexml = $this->getAdapter()->readData($response);
        
        return self::extract($simplexml, $process);
    }
    
    /**
     * 
     *
     * @param SimpleXMLElement $values
     * @param Diggin_Scraper_Process $process
     * @return array
     * @throws Diggin_Scraper_Strategy_Exception
     */
    public function extract($values, $process)
    {
        $results = (array) $values->xpath(self::_xpathOrCss2Xpath($process->getExpression()));

        
        if (count($results) === 0 or ($results[0] === false)) {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            
            //$process->getExpression() = self::_xpathOrCss2Xpath($process->getExpression());
            throw new Diggin_Scraper_Strategy_Exception("Couldn't find By Xpath, Process :".$process->getExpression());
        }

        return $results;
    }

    protected static function _xpathOrCss2Xpath($exp){
        if (preg_match('!^(?:/|id\()!', $exp)) {
            return '.'.$exp;
        } else {
            if ($exp === '.') {
                return $exp;
            } else if (ctype_alnum($exp)) {
                return ".//$exp";
            } else if (0 === strncasecmp('./', $exp, 2)) {
                return $exp;
            } else {
                return '.'.preg_replace('#//+#', '//', str_replace(chr(32), '', Zend_Dom_Query_Css2Xpath::transform($exp)));
            }
        }
    }

    /**
     * Getting Value
     * 'RAW'----
     *   just as SimpleXMlElement
     * 'TEXT' ----  
     *  step1: SimpleXMlElement->asXML
     *  step2: convert special html entitiy
     *  Htmlscaraping is "Replace every '&' with '&amp;'"
     *  @see Diggin_Scraper_Adapter_Htmlscraping
     *  @see http://www.rcdtokyo.com/etc/htmlscraping/#NOTE_ENTITY
     *  step3: strip_tags
     *  step4: triming (without space)
     *   chr(9)  Tab
     *   chr(10) Line Feed (LF) 
     *   chr(13) Carriage Return(CR)
     *   
     *  @see http://en.wikipedia.org/wiki/ASCII
     * 
     * @param array
     * @param Diggin_Scraper_Process
     * @return mixed
     * @throws Diggin_Scraper_Strategy_Exception
     */
    public function getValue($values, $process)
    {
        //type
        if (strtoupper(($process->getType())) === 'RAW') {
            $strings = $values;
        } elseif (strtoupper(($process->getType())) === 'ASXML') {
            $strings = array();
            foreach ($values as $value) {
                array_push($strings, $value->asXML());
            }
        } elseif (strtoupper(($process->getType())) === 'TEXT') {
            $strings = array();
            foreach ($values as $value) {
                $value = strip_tags(
                        htmlspecialchars_decode($value->asXML(),
                        ENT_NOQUOTES));
                $value = str_replace(array(chr(9), chr(10), chr(13)),
                                     '', $value);
                array_push($strings, $value);
            }
        } elseif (strtoupper(($process->getType())) === 'DECODE' or 
                  strtoupper(($process->getType())) === 'DISP') {
            $strings = array();
            foreach ($values as $value) {
                $value = strip_tags(
                        htmlspecialchars_decode($value->asXML(),
                        ENT_NOQUOTES));
                $value = html_entity_decode(strip_tags($value), ENT_NOQUOTES, 'UTF-8');
                $value = str_replace(array(chr(9), chr(10), chr(13)),
                                     '', $value);
                array_push($strings, $value);
            }
        } elseif (strtoupper(($process->getType())) === 'PLAIN') {
            $strings = array();
            foreach ($values as $value) {
                $value = htmlspecialchars_decode($value->asXML(),
                            ENT_NOQUOTES);
                $value = str_replace(array(chr(10), chr(13)),
                                     '', $value);
                array_push($strings, $value);
            }
        } elseif (strtoupper(($process->getType())) === 'HTML') {
            $strings = array();
            foreach ($values as $value) {
                $value = strip_tags(
                        htmlspecialchars_decode($value->asXML(),
                        ENT_NOQUOTES));
                $value = str_replace(array(chr(10), chr(13)),
                                     '', $value);
                $value = preg_replace(array('#^<.*?>#', '#s*</\w+>\n*$#'), '', $value);
                array_push($strings, $value);
            }
        } elseif ((strpos($process->getType(), '@') === 0) and 
                  ($process->getType() == '@href' OR $process->getType() == '@src')) {
            $strings = array();
            
            $headBase = new Diggin_Scraper_FindHelper_HeadBaseHref();
            $base = $headBase->headBaseHref(current($values));
            if ($base === false) {
                $base = self::$_adapterconfig['url'];
            }
            foreach ($values as $k => $value) {
                $attribute = $value[substr($process->getType(), 1)];
                if ($attribute === null) continue;
                $strings[$k] = Diggin_Uri_Http::getAbsoluteUrl((string)$attribute, $base);
            }
        } elseif (strpos($process->getType(), '@') === 0) {
            $strings = array();
            foreach ($values as $k => $value) {
                $attribute = $value[substr($process->getType(), 1)];
                if ($attribute === null) continue;
                $strings[$k] = (string)$attribute;
            }
        } else {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            throw new Diggin_Scraper_Strategy_Exception("Unknown value type :".$process->getType());
        }
        
        return $strings;
    }
}
