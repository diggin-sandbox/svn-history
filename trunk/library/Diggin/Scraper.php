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
     * scraping resuluts
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

    private static $_processes;
    private static $_strategyName;
    private static $_adapter;
    
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
     * Set the HTTP client instance
     *
     * Sets the HTTP client object to use for retrieving the feeds.
     *
     * @param  Zend_Http_Client $httpClient
     * @return void
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
     * @param string $adapter(optional)
     * @retrun Diggin_Scraper Provides a fluent interface
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
     * @param Zend_Http_Response
     * @return Diggin_Scraper_Strategy
     */
    public function getStrategy($response)
    {
        if (!self::$_strategy instanceof Diggin_Scraper_Strategy_Abstract) {
            /**
             * @see Diggin_Scraper_Strategy_Abstract
             */
            require_once 'Diggin/Scraper/Adapter/Htmlscraping.php';
            $scraperAdapter = new Diggin_Scraper_Adapter_Htmlscraping();
            $scraperAdapter->setConfig(array('url' => $this->_url));
            require_once 'Diggin/Scraper/Strategy/Xpath.php';
            self::$_strategy = new Diggin_Scraper_Strategy_Xpath($response, $scraperAdapter);
        }
        
        return self::$_strategy;
    }

    /**
     * construct
     *
     * @param string 
     * @param  array
     */
    public function __construct($url = null, array $parms = array())
    {
        $this->_url = $url;
    }

    /**
     * making request
     * 
     * @param array $parms
     * @return Zend_Http_Response
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
        
        if (isset($parms)){
            $client->setParameterGet($parms);
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
        }

        return $this;
    }

    /**
     * scraping
     * 
     * @param string $url
     * @return array scraping data.
     */
    public function scrape($url = null)
    {
        
        $response = $this->makeRequest($url);
        
        if (!is_null(self::$_strategyName)) {
            $this->callStrategy($response, self::$_strategyName, self::$_adapter);
        }

        require_once 'Diggin/Scraper/Context.php';
        $context = new Diggin_Scraper_Context($this->getStrategy($response));

        foreach (self::$_processes as $process) {
            $values = self::$_strategy->getValue($context, $process);

            if ($process->filter && is_callable($process->filter)) {
                require_once 'Diggin/Scraper/Filter.php';
                $values = Diggin_Scraper_Filter::runFilter($process->filter, $values);
            }
            
            $this->results[$process->name] = $values;
        }
        
        return $this->results;
    }
}