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
 * @package    Diggin_Service
 * @subpackage Wedata
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @see Zend_Service_Abstract
 */
require_once 'Zend/Service/Abstract.php';

/**
 * Diggin_Service_Wedata
 */
class Diggin_Service_Wedata extends Zend_Service_Abstract
{
    const API_URL = 'http://wedata.net';
    
    //parameter keys
    const KEY_APIKEY = 'api_key';
    const KEY_PAGE = 'page';
    
    // path to acces database
    const PATH_GET_DATABASES = '/databases.json';
    const PATH_GET_DATABASE  = '/databases/%s.json';
    const PATH_CREATE_DATABASE = '/databases';
    const PATH_UPDATE_DATABASE = '/databases/%s';
    const PATH_DELETE_DATABASE = '/databases/%s';
    
    // path to acces item
    const PATH_GET_ITEMS = '/databases/%s/items.json';//dbname
    const PATH_GET_ITEM  = '/items/%s.json'; //item id
    const PATH_CREATE_ITEM = '/databases/%s/items'; //dbname
    const PATH_UPDATE_ITEM = '/items/%s'; //item id
    const PATH_DELETE_ITEM = '/items/%s'; //item id

    /**
     * Zend_Http_Client Object
     *
     * @var Zend_Http_Client
     */
    protected static $_client;

    /**
     * Request parameters
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Decode Type to handle Wedata's response
     *
     * @var int|null
     */
    protected $_decodetype = null;

    /**
     * Constructs a new Wedata Web Service Client
     *
     * @param array $params parameter acording Wedata
     * @param boolean | string @see Zend_Json
     * @return null
     */
    public function __construct(array $params = null, $decodetype = null)
    {
        $this->_params = $params;
        $this->_decodetype = $decodetype;
    }
    
    /**
     * Decode Json Value
     *
     * @param string $value json
     * @return mixed decoded json's value
     */
    protected function _decode($value)
    {
        if ($this->_decodetype === false) {
            //nothig to do
        } else {
            require_once 'Zend/Json.php';
            if ($this->_decodetype === null || $this->_decodetype === Zend_Json::TYPE_ARRAY) {
                $value = Zend_Json::decode($value, Zend_Json::TYPE_ARRAY);
            } else {
                $value = Zend_Json::decode($value, $this->_decodetype);
            }    
        }
        
        return $value;
    }
    
    /**
     * Retrieve current object's parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Retrieve param by key
     *
     * @param string $key
     * @return mixed|null Null when not found
     */
    public function getParam($key)
    {
        if (array_key_exists($key, $this->_params)){
            return $this->_params[$key];
        }

        return null;
    }
    
    /**
     * setting parameter
     * 
     * @param array $params
     */
    public function setParams(array $params)
    {
        foreach ($params as $key => $value){
            $this->_params[strtolower($key)] = $value;
        }
    }
    
    /**
     * adding parameter
     * 
     * @param string
     * @param string
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
    }
    
    /**
     * adding parameter
     * 
     * @param string $key
     * @param string $value
     */
    public function setParamDatabase($key, $value)
    {
        $this->_params['database'][$key] = $value;
    }
        
    public function setDatabaseName($databaseName)
    {
        $this->_params['database']['name'] = $databaseName;
    }

    public function getDatabaseName()
    {
        if (isset($this->_params['datanbase']['name'])) {
            return $this->_params['database']['name'];
        }

        require_once 'Diggin/Service/Exception.php';
        throw new Diggin_Service_Exception('database name is not set');
    }

    
    /**
     * Handles all requests to a web service
     * 
     * @param string path
     * @param string Prease,using Zend_Http_Client's const
     * @return mixed
     */
    protected function makeRequest($path, $method, array $params = null)
    {
        self::$_client = self::getHttpClient();
        
        require_once 'Zend/Uri/Http.php';
        $uri = Zend_Uri_Http::factory(self::API_URL);
        $uri->setPath($path);

        if (!is_null($params)) {            
            if ($method == Zend_Http_Client::GET) {
                self::$_client->setParameterGet($params);
            } elseif ($method == Zend_Http_Client::POST) {
                self::$_client->setParameterPost($params);
            } else {
                $uri->setQuery($params);
            }
        }

        self::$_client->setUri($uri->getUri());
        
        $response = self::$_client->request($method);
        
        if (!$response->isSuccessful()) {
             /**
              * @see Diggin_Service_Exception
              */
             require_once 'Diggin/Service/Exception.php';
             throw new Diggin_Service_Exception("Http client reported an error: '{$response->getMessage()}'");
        }
        
        //returning response switching by Reqest Method
        if ($method == Zend_Http_Client::GET) {
            return $response->getBody();
        } else {
            $status = $response->getStatus();
            $headers = $response->getHeaders();
            return array($status, $headers);
        }
    }
    
    public function getDatabases(array $params = null)
    {
        $params = (isset($params)) ? $params : $this->getParams();

        $responseBody = $this->makeRequest(self::PATH_GET_DATABASES, Zend_Http_Client::GET, $params);
        
        return $this->_decode($responseBody);
    }

    public function getDatabase($databaseName = null, $page = null)
    {
        $databaseName = (isset($databaseName)) ? $databaseName : $this->getDatabaseName();
        $params = $this->getParams();

        if ($page) {
            $params['page'] = $page;
        } else if (!isset($params['page'])) {
            require_once 'Diggin/Service/Exception.php';
            throw new Diggin_Service_Exception("currently parameter not set 'page'");
        }
        
        $path = sprintf(self::PATH_GET_DATABASE, rawurlencode($databaseName));
        $responseBody = $this->makeRequest($path, Zend_Http_Client::GET, $params);
        
        return $this->_decode($responseBody);
    }

    public function createDatabase(array $params = null)
    {
        $params = (isset($params)) ? $params : $this->getParams();
        
        if (!isset($params['api_key'])){
            require_once 'Diggin/Service/Exception.php';
            throw new Diggin_Service_Exception('API key is not set ');
        } elseif (!isset($params['database']['name'])) {
            require_once 'Diggin/Service/Exception.php';
            throw new Diggin_Service_Exception('Database name is not set ');
        } elseif (!isset($params['database']['required_keys'])) {
            require_once 'Diggin/Service/Exception.php';
            throw new Diggin_Service_Exception('required_keys is not set');
        }
        
        $return = $this->makeRequest($this->PATH_CREATE_DATABASE, Zend_Http_Client::POST, $params);
        
        return $return;
    }
    
    
    public function udpateDatabase($databaseName = null, array $params = null)
    {
        $databaseName = (isset($databaseName)) ? $databaseName : $this->getDatabaseName();
        $params (isset($params)) ? $params : $this->getParams();
        
        if(!isset($params['api_key'])){
            require_once 'Diggin/Service/Exception.php';
            throw new Diggin_Service_Exception('API key is not set ');
        } elseif (!isset($params['database']['required_keys'])) {
            require_once 'Diggin/Service/Exception.php';
            throw new Diggin_Service_Exception('required_keys is not set');
        }

        $path = sprintf(self::PATH_UPDATE_DATABASE, rawurlencode($databaseName));
        $return = $this->makeRequest($path, Zend_Http_Client::PUT, $params);
        
        return $return;
    }
    
    public function deleteDatabase($databaseName = null, $apiKey = null)
    {
        $databaseName = (isset($databaseName)) ? $databaseName : $this->getDatabaseName();
        $params = isset($apiKey) ? array(self::KEY_APIKEY => $apiKey) : $this->getParams();
        
        if (!isset($params[self::KEY_APIKEY])) {
            require_once 'Diggin/Service/Exception.php';
            throw new Diggin_Service_Exception('API key is not set ');
        }
        
        $path = sprintf(self::PATH_DELETE_DATABASE, rawurlencode($databaseName));
        $return = $this->makeRequest($path, Zend_Http_Client::DELETE, $params);
        
        return $return;
    }
    
    //////item methods    
    public function getItems($databaseName = null, $page = null)
    {
        $databaseName = (isset($databaseName)) ? $databaseName : $this->getDatabaseName();

        if (isset($page)) {
            $params = array(self::KEY_PAGE => $paget); 
        } else if (!$this->getParam[self::KEY_PAGE]) {
            $params = array();
        } else {
            $params = array(self::KEY_PAGE => $this->getParam(self::KEY_PAGE));
        }
        
        $path = sprintf(self::PATH_GET_ITEMS, rawurlencode($databaseName));
        $responseBody = $this->makeRequest($path, Zend_Http_Client::GET, $params);
        
        return $this->_decode($responseBody);
    }

    /**
     * Get Item
     * 
     * @param string $itemId
     * @param string $page
     * @return array Decording Result
     */
    public function getItem($itemId, $page = null)
    {
        //@todo if int set as itemid or string searching itemid by name
        //is_integer($item);
        //is_string($item) ;
        
        $page = isset($page) ? $page : $this->getParam(self::KEY_PAGE);
        
        if ($page) {
            $params = array(self::KEY_PAGE => $page);
        } else {
            $params = array();
        }

        $path = sprintf(self::PATH_GET_ITEM, $itemId);
        $responseBody = $this->makeRequest($path, Zend_Http_Client::GET, $params);
        
        return $this->_decode($responseBody);
    }
    
    public function insertItem($databaseName = null, array $params = null)
    {
        $databaseName = (isset($databaseName)) ? $databaseName : $this->getDatabaseName();
        
        $path = sprintf(self::PATH_CREATE_ITEM, rawurlencode($databaseName));
        $return = $this->makeRequest($path, Zend_Http_Client::POST, $params);
        
        return $return;
    }
    
    public function updateItem($itemId, array $params = array())
    {
        if (!isset($params['api_key'])) {
            require_once 'Diggin/Service/Exception.php';
            throw new Diggin_Service_Exception('API key is not set ');
        }
        
        $path = sprintf(self::PATH_UPDATE_ITEM, $itemId);
        $return = $this->makeRequest($path, Zend_Http_Client::PUT, $params);
        
        return $return;
    }
    
    public function deleteItem($itemId, $apiKey = null)
    {
        $apiKey = isset($apiKey) ? $apiKey : $this->getParam(self::KEY_APIKEY);
        
        if ($apikey) {
            $params = array('api_key' => $apiKey);
        } else {
            require_once 'Diggin/Service/Exception.php';
            throw new Diggin_Service_Exception('API key is not set ');
        }
        
        $path = sprintf(self::PATH_DELETE_ITEM, $itemId);
        $return = $this->makeRequest($path, Zend_Http_Client::DELETE, $params);
        
        return $return;
    }
}
