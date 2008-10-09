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
require_once 'Zend/Dom/Query/Css2Xpath.php';

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
    public function scrape($respose, $process)
    {
        $simplexml = $this->getAdapter()->readData($respose);
        
        return self::extract($simplexml, $process);
    }
    
    public function extract($values, $process)
    {
        //↓このハンドリングはxpathの記述自体が間違ってたとき（いらないかな？）
        set_error_handler(
            create_function('$errno, $errstr',
            'if($errno) require_once "Diggin/Scraper/Strategy/Exception.php"; 
               throw new Diggin_Scraper_Strategy_Exception($errstr, $errno);'
            )
        );

        $results = (array) $values->xpath(self::_xpathOrCss2Xpath($process->expression));
        restore_error_handler();

        if (count($results) === 0 or ($results[0] === false)) {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            
            $process->expression = self::_xpathOrCss2Xpath($process->expression);
            throw new Diggin_Scraper_Strategy_Exception("Couldn't find By Xpath, Process : $process");
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
     *  step2: replace escaped entity 
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
     * NOTES: 2008/10/09
     * $xml = '<tag>text1>text2</tag>';
     * $s = new SimpleXMLElement($xml);
	 * var_dump($s->asXML()); 
	 * // '<tag>text1&gt;text2</tag>'
     * 
     * @param Diggin_Scraper_Context
     * @param Diggin_Scraper_Process
     * @return mixed
     */
    public function getValue($values, $process)
    {
        //type
        if (strtoupper(($process->type)) === 'RAW') {
            $strings = $values;
        } elseif (strtoupper(($process->type)) === 'TEXT') {
            $strings = array();
            foreach ($values as $value) {
                $value = strip_tags(str_replace(array('&gt;', '&amp;'),
                                                array('>', '&'), 
                                     $value->asXML()));
                $value = str_replace(array(chr(9), chr(10), chr(13)),
                                     '', $value);
                array_push($strings, $value);
            }
        } elseif (strtoupper(($process->type)) === 'DECODE' or 
                  strtoupper(($process->type)) === 'DISP') {
            $strings = array();
            foreach ($values as $value) {
            	$value = str_replace(array('&gt;', '&amp;'),
                                     array('>', '&'), 
                                     $value->asXML());
                $value = html_entity_decode(strip_tags($value), ENT_NOQUOTES, 'UTF-8');
                $value = str_replace(array(chr(9), chr(10), chr(13)),
                                     '', $value);
                array_push($strings, $value);
            }        	
        } elseif (strtoupper(($process->type)) === 'PLAIN' or
                  strtoupper(($process->type)) === 'HTML') {
            $strings = array();
            foreach ($values as $value) {
            	$value = str_replace(array('&gt;', '&amp;'),
                                     array('>', '&'),
                                     $value->asXML());
                $value = str_replace(array(chr(10), chr(13)),
                                     '', $value);
                array_push($strings, $value);
            }
        } elseif (strpos($process->type, '@') === 0) {
            $strings = array();
            require_once 'Diggin/Uri/Http.php';
            foreach ($values as $value) {
            	$attribute = (string) $value[substr($process->type, 1)];
                if (($process->type == '@href' OR $process->type == '@src')) {
                    array_push($strings, Diggin_Uri_Http::getAbsoluteUrl($attribute, self::$_adapterconfig['url']));
                } else {
                    array_push($strings, $attribute);
                }
            }            
        } else {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            throw new Diggin_Scraper_Strategy_Exception("Unknown value type :".$process->type);
        }
        
        return $strings;
    }
}
