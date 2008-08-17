<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * http://framework.zend.com/license/new-bsd
 * 
 * @category   Diggin
 * @package    Diggin_Service
 * @subpackage Eventcast
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Service_Abstract
 */
require_once 'Zend/Service/Abstract.php';

/**
 * @see Zend_Uri_Http
 */
require_once 'Zend/Uri/Http.php';

class Diggin_Service_Eventcast extends Zend_Service_Abstract
{
    const API_URL = 'http://clip.eventcast.jp/api/v1/Search?';
    
    /**
     * Zend_Http_Client Object
     *
     * @var Zend_Http_Client
     */
    protected static $_client;

    /**
     * default parameter
     *
     * @var array
     */
    protected static $_parameter = array('sort' => 'date',
                                         'order' => 'asc',
                                         'start' => 1,
                                         'results' => 100,
                                         'trim' => 0,
                                         'Format' => 'php');
    
    /**
     * set Parameter
     *
     * @param array $parameter
     */
    public static function setParameter(array $parameter)
    {
        self::$_parameter = array_merge(self::$_parameter, $parameter);
    }
    
    /**
     * getting parameter for eventcast
     * if start or end date not set 
     * 
     * @return array self::$_parameter
     */
    public static function getParameter()
    {
        if (!array_key_exists(strtolower('startdate'), self::$_parameter)) {
            self::$_parameter['startdate'] = date('Y/m/d', time()+86400*0);
        }
        
        if (!array_key_exists(strtolower('enddate'), self::$_parameter)) {
            self::$_parameter['enddate'] = date('Y/m/d', time()+86400*30);
        }
        
        return self::$_parameter;
    }
    
    public static function makeRequest($parameter)
    {
        self::setParameter($parameter);
        
        self::$_client = self::getHttpClient();
        
        $uri = Zend_Uri_Http::factory(self::API_URL);
        $uri->setQuery(self::getParameter());

        self::$_client->setUri($uri->getUri());
        
        $response = self::$_client->request(Zend_Http_Client::GET);
        
        if (!$response->isSuccessful()) {
             /**
              * @see Diggin_Service_Exception
              */
             require_once 'Diggin/Service/Exception.php';
             throw new Diggin_Service_Exception("Http client reported an error: '{$response->getMessage()}'");
        }
        
        return unserialize($response->getBody());
    }
    
}