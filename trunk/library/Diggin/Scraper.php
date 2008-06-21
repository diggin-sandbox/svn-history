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
     * @param dsl1, dsl2, dsl3,,,
     * @param 
     * @return Diggin_Scraper Provides a fluent interface
     */
    public function process($expression, $dsl1, $autofilter = null)
    {
        $getargs = func_get_args();

        $lastarg = array_slice($getargs, -1);
    
        $namestypes = array_slice($getargs, 1);
        
        require_once 'Diggin/Scraper/Process.php';
        foreach ($namestypes as $nametype) {
            if(is_string($nametype)) {
                if(strpos($nametype, '=>') !== false) list($name, $types) = explode('=>', $nametype);
                if(!isset($types)) {
                    self::$_processes[] = new Diggin_Scraper_Process($expression, trim($nametype));
                } else {
                    $types = trim($types, " '\"");
                    if(strpos($types, ',') !== false) $types = explode(',', $types);
                    if(count($types) === 1) {
                        self::$_processes[] = 
                        new Diggin_Scraper_Process($expression, trim($name), $types);
                    } else {
                        foreach ($types as $count => $type) {
                            if ($count !== 0) $filter[] = trim($type, " []'\"");
                        }
                        self::$_processes[] = 
                        new Diggin_Scraper_Process($expression, trim($name),
                                                   trim($types[0], " []'\""), $filter);
                    }
                }
            } elseif (is_array($nametype)) {
                self::$_processes[] = new Diggin_Scraper_Process($expression, $nametype[0], $nametype[1], $nametype[2]);
            } elseif (is_object($nametype)) {
                foreach ($nametype->processes as $process) {
                    ///突貫処置
                    if (self::$_strategyName == "Diggin_Scraper_Strategy_Selector") {
                        $separeter = " ";
                    } else {
                        $separeter = "/";
                    }
                    $this->process($expression.$separeter.$process->expression,
                                   array($process->name, $process->type, $process->filter));
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
     * @return array $this->results Scraping data.
     */
    public function scrape($resource = null)
    {        
        if (!$resource instanceof Zend_Http_Response) {
            $resource = $this->makeRequest($resource);
        }
        
        if (!is_null(self::$_strategyName)) {
            $this->callStrategy($resource, self::$_strategyName, self::$_adapter);
        }

        require_once 'Diggin/Scraper/Context.php';
        //@todo getStrategy($resource) ←strategyが設定されてないときにデフォで呼ぶときに
        //resourceを渡すようにしているが......
        $context = new Diggin_Scraper_Context($this->getStrategy($resource));
        foreach (self::$_processes as $process) {
            $values = self::$_strategy->getValue($context, $process);

            if ($process->filter) {
                require_once 'Diggin/Scraper/Filter.php';
                $values = Diggin_Scraper_Filter::run($values, $process->filter);
            }

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
                $nameorandtype = explode('=>', $nametype);
                if((count($nameorandtype)) === 1) {
                    $this->processes[] = new Diggin_Scraper_Process($expression, trim($nameorandtype[0]));
                } else {
                    $type = trim($nameorandtype[1], " '\"");
                    $typef = explode(',', $type);
                    if((count($typef)) === 1) {
                        $this->processes[] = 
                        new Diggin_Scraper_Process($expression, trim($nameorandtype[0]), $type);
                    } else {
                        $this->processes[] = 
                        new Diggin_Scraper_Process($expression, 
                                                   trim($nameorandtype[0]),
                                                   trim($typef[0], " []'\""),
                                                   trim($typef[1], " []'\""));
                    }
                }
            } elseif (is_array($nametype)) {
                $this->processes[] = new Diggin_Scraper_Process($expression, $nametype[0], $nametype[1], $nametype[2]);
            } elseif (is_object($nametype)) {
                foreach ($nametype->processes as $process) {
                    $this->process($expression.$process->expression,
                                   array($process->name, $process->type, $process->filter));
                }
            }
        }
        
        return $this;
    }
}
