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

/**
 * @see Diggin_Scraper_Process
 */       
require_once 'Diggin/Scraper/Process.php';
/**
 * @see Diggin_Scraper_Context
 */
require_once 'Diggin/Scraper/Context.php';

class Diggin_Scraper
{
    /**
     * scraping results
     * 
     * @var array 
     */
    public $results;
    
    /**
     * target url of scraping
     * 
     * @var string 
     */
    protected $_url;

    /**
     * Stores the prosess
     */
    private static $_processes;
    
    /**
     * strategy name to use for chaning strategy
     */
    private static $_strategyName;
    
    /**
     * adapter for response
     */
    private static $_adapter;

    /**
     * startegy for scraping
     */
    protected static $_strategy = null;
    
    /**
     * Getting the URL for scraping
     * 
     * @return string $this->_url
     */
    private function getUrl()
    {
        return $this->_url;
    }

    /**
     * Set the Url for scraping
     * 
     * @param string $url
     */
    public function setUrl ($url) 
    {
        $this->_url = $url;
    }

    /**
     * HTTP client object to use for retrieving
     *
     * @var Zend_Http_Client
     */
    protected static $_httpClient = null;

    /**
     * Read only properties accessor
     *
     * @param  string $var property to read
     * @return mixed
     */
    public function __get($var)
    {
        return $this->results[$var];
    }

    /**
     * Set the HTTP client instance
     *
     * Sets the HTTP client object to use for retrieving the feeds.
     *
     * @param  Zend_Http_Client $httpClient
     * @return null
     */
    public static function setHttpClient(Zend_Http_Client $httpClient)
    {
        self::$_httpClient = $httpClient;
    }

    /**
     * Gets the HTTP client object. If none is set, a new Zend_Http_Client will be used.
     *
     * @return Zend_Http_Client_Abstract
     */
    public static function getHttpClient()
    {
        if (!self::$_httpClient instanceof Zend_Http_Client) {
            /**
             * @see Zend_Http_Client
             */
            require_once 'Zend/Http/Client.php';
            self::$_httpClient = new Zend_Http_Client();
        }

        return self::$_httpClient;
    }

    /**
     * changing Startegy
     * 
     * @param string $strategyName
     * @param Diggin_Scraper_Adapter_Interface $adapter
     * @return Diggin_Scraper Provides a fluent interface
     */
    public function changeStrategy($strategyName, $adapter = null)
    {
        self::$_strategyName = $strategyName;
        self::$_adapter = $adapter;

        return $this;
    }

    /**
     * calling this scraper's strategy
     * 
     * @param Zend_Http_Response $response
     * @param string $strategyName
     * @param Object Diggin_Scraper_Adapter_Interface (optional)
     */
    private function callStrategy($response, $strategyName, $adapter = null)
    {
        require_once 'Zend/Loader.php';

        try {
            Zend_Loader::loadClass($strategyName);
        } catch (Zend_Exception $e) {
            require_once 'Diggin/Scraper/Exception.php';
            throw new Diggin_Scraper_Exception("Unable to load strategy '$strategyName': {$e->getMessage()}");
        }

        $strategy = new $strategyName($response);
        if($adapter) $strategy->setAdapter($adapter);
        if(method_exists($strategy, 'setAdapterConfig')) $strategy->setAdapterConfig(array('url' => $this->_url));

        self::$_strategy = $strategy;
    }

    /**
     * Returning this scraper's strategy
     * 
     * @param Zend_Http_Response $response
     * @return Diggin_Scraper_Strategy
     */
    public function getStrategy($response)
    {
        if (!self::$_strategy instanceof Diggin_Scraper_Strategy_Abstract) {
            /**
             * @see Diggin_Scraper_Strategy_Abstract
             */
            require_once 'Diggin/Scraper/Strategy/Xpath.php';
            $strategy = new Diggin_Scraper_Strategy_Xpath($response);
            $strategy->setAdapterConfig(array('url' => $this->_url));
            
            self::$_strategy = $strategy;
        }
        
        return self::$_strategy;
    }

    /**
     * construct
     *
     * @param string $url
     */
    public function __construct($url = null)
    {
        $this->_url = $url;
    }

    /**
     * making request
     * 
     * @param string $url
     * @return Zend_Http_Response $response
     */
    private function makeRequest($url = null)
    {
        $client = self::getHttpClient();
        
        if ($url) {
            $this->setUrl($url);
        } 
        if ($this->_url) {
            $client->setUri($this->getUrl());
        }
        
        $response = $client->request('GET');

        if (!$response->isSuccessful()) {
             /**
              * @see Diggin_Scraper_Exception
              */
             require_once 'Diggin/Scraper/Exception.php';
             throw new Diggin_Scraper_Exception("Http client reported an error: '{$response->getMessage()}'");
        }
        
        return $response;
    }

    /**
     * setting process like DSL of Web::Scraper
     * 
     * @param string (xpath etc)
     * @params mixed args1, args2, args3,,,
     * @return Diggin_Scraper Provides a fluent interface
     */
    public function process($expression, $args)
    {
        $namestypes = array_slice(func_get_args(), 1);

        foreach ($namestypes as $nametype) {
            if (is_string($nametype)) {
                if (strpos($nametype, '=>') !== false) list($name, $types) = explode('=>', $nametype);
                if (!isset($types)) $name = $nametype;
                if ((substr(trim($name), -2) == '[]')) {
                    $name = substr(trim($name), 0, -2);
                    $arrayflag = true;
                } else {
                    $arrayflag = false;
                }  
                if (!isset($types)) {
                    self::$_processes[] = new Diggin_Scraper_Process($expression, trim($nametype), $arrayflag);
                } else {
                    $types = trim($types, " '\"");
                    if (strpos($types, ',') !== false) $types = explode(',', $types);
                    
                    if (count($types) === 1) {
                        self::$_processes[] = 
                        new Diggin_Scraper_Process($expression, trim($name), $arrayflag, $types);
                    } else {
                        foreach ($types as $count => $type) {
                            if ($count !== 0) $filters[] = trim($type, " []'\"");
                        }
                        self::$_processes[] = 
                        new Diggin_Scraper_Process($expression, trim($name), $arrayflag,
                                                   trim($types[0], " []'\""), $filters);
                    }
                }
            } elseif (is_array($nametype)) {
                if(!is_numeric(key($nametype))) {
                    if ((substr(key($nametype), -2) == '[]')) {
                        $name = substr(key($nametype), 0, -2);
                        $arrayflag = true;
                    } else {
                        $name = key($nametype);
                        $arrayflag = false;
                    }
                    self::$_processes[] = new Diggin_Scraper_Process($expression, $name, $arrayflag, array_shift($nametype));
                } else {
                    self::$_processes[] = new Diggin_Scraper_Process($expression, $nametype[0], $nametype[1], $nametype[2], $nametype[3]);
                }
            }
        }

        return $this;
    }

    /**
     * scraping
     * 
     * @param (string | Zend_Http_Response) $resource
     * 		setting URL or Zend_Http_Response
     * @param string (if $resource is not URL, please set URL for recognize)
     * @return array $this->results Scraping data.
     */
    public function scrape($resource = null, $targetUrl = null)
    {        
        if (!$resource instanceof Zend_Http_Response) {
            $resource = $this->makeRequest($resource);
        }
        
        if (isset($targetUrl)) {
            $this->setUrl($targetUrl);
        }
        
        if (!is_null(self::$_strategyName)) {
            $this->callStrategy($resource, self::$_strategyName, self::$_adapter);
        }

        $context = new Diggin_Scraper_Context($this->getStrategy($resource));
        foreach (self::$_processes as $process) {
            $values = self::$_strategy->getValues($context, $process);

            $this->results[$process->name] = $values;
        }

        return $this->results;
    }

    /**
     * Class destructor.
     *
     * @return null
     */
    public function __destruct()
    {
        self::$_processes = null;
        self::$_strategy = null;
        self::$_strategyName = null;
        self::$_adapter = null;
    }
}

class scraper
{

    public $processes;

    public function process($expression, $namestypes, $filterIterator = null)
    {
        $getargs = func_get_args();
        $lastarg = array_slice($getargs, -1);
        $namestypes = array_slice($getargs, 1);
        
        require_once 'Diggin/Scraper/Process.php';
        foreach ($namestypes as $nametype) {
            if(is_string($nametype)) {
                //$types = null;
                if (strpos($nametype, '=>') !== false) list($name, $types) = explode('=>', $nametype);
                if (!isset($types)) $name = $nametype;
                if ((substr(trim($name), -2) == '[]')) {
                    $name = substr(trim($name), 0, -2);
                    $arrayflag = true;
                } else {
                    $arrayflag = false;
                }
                if (!isset($types)) {
                    $this->processes[] = new Diggin_Scraper_Process($expression, trim($nametype), $arrayflag);
                } else {
                    $types = trim($types, " '\"");
                    if (strpos($types, ',') !== false) $types = explode(',', $types);
                    if (count($types) === 1) {
                        $this->processes[] = 
                        new Diggin_Scraper_Process($expression, trim($name), $arrayflag, $types);
                    } else {
                        foreach ($types as $count => $type) {
                            if ($count !== 0) $filters[] = trim($type, " []'\"");
                        }
                        $this->processes[] = 
                        new Diggin_Scraper_Process($expression, trim($name), $arrayflag,
                                                   trim($types[0], " []'\""), $filters);
                    }
                }
            } elseif (is_array($nametype)) {
                if(!is_numeric(key($nametype))) {
                    if ((substr(key($nametype), -2) == '[]')) {
                        $name = substr(key($nametype), 0, -2);
                        $arrayflag = true;
                    } else {
                        $name = key($nametype);
                        $arrayflag = false;
                    }                                
                    $this->processes[] = new Diggin_Scraper_Process($expression, $name, $arrayflag, array_shift($nametype));
                } else {
                    $this->processes[] = new Diggin_Scraper_Process($expression, $nametype[0], $nametype[1], $nametype[2], $nametype[3]);
                }
            }
        }
        
        return $this;
    }
}