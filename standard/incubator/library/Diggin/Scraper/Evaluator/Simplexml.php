<?php

/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://diggin.musicrider.com/LICENSE
 * 
 * @category   Diggin
 * @package    Diggin_Scraper
 * @subpackage Evaluator
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/** Diggin_Scraper_Evaluator_Abstract */
require_once 'Diggin/Scraper/Evaluator/Abstract.php';

/**
 * @see Diggin_Uri_Http
 */
require_once 'Diggin/Uri/Http.php';

class Diggin_Scraper_Evaluator_Simplexml extends Diggin_Scraper_Evaluator_Abstract
{
    private $_baseUri;

    public function raw($simplexml)
    {
        return $simplexml;
    }

    public function asxml($simplexml)
    {
        return $simplexml->asXML();
    }

    public function text($simplexml)
    {
        $value = strip_tags($simplexml->asXML());
        $value = str_replace(array(chr(9), chr(10), chr(13)), '', $value);
        return $value;
    }
    
    public function decode($simplexml)
    {
        $value = strip_tags($value->asXML());
        $value = html_entity_decode(strip_tags($value), ENT_NOQUOTES, 'UTF-8');
        $value = str_replace(array(chr(9), chr(10), chr(13)), '', $value);

        return $value;
    }
    
    /**
     * decode alias
     *
     * @return string
     */ 
    public function disp($simplexml)
    {
        return $this->decode($simplexml);
    }

    public function __call($method, $args)
    {
        if (preg_match('/^at_/', $method)) {
            $value = $args[0];            
            
            if ($method== 'at_href' OR $method == 'at_src') {
                if (!$this->_baseUri) {
                    require_once 'Diggin/Scraper/Helper/Simplexml/HeadBaseHref.php';
                    $headBase = new Diggin_Scraper_Helper_Simplexml_HeadBaseHref($value);
                    $headBase->setOption(array('baseUrl' => $this->getConfig('baseUrl')));
                    require_once 'Diggin/Uri/Http.php';
                    $this->_baseUri = new Diggin_Uri_Http;
                    $this->_baseUri->setBaseUri($headBase->getBaseUrl());
                }
                $attribute = $value[substr($method, 3)];
                if ($attribute === null) {
                    $value = false;
                } else {
                    $value = $this->_baseUri->getAbsoluteUrl((string)$attribute);
                }
            } else {
                $attribute = $value[substr($method, 3)];
                if ($attribute === null) {
                    $value = false;
                } else {
                    $value = (string)$attribute;
                }
            }
        } else {
            require_once 'Diggin/Scraper/Evaluator/Exception.php';
            throw new Diggin_Scraper_Evaluator_Exception("Unknown evaluate method  :".$method);
        }

        return $value;
    }
}
