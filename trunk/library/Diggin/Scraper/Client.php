<?php
class Diggin_Scraper_Client
{
    protected $_url;
        
    private static $_processes;
    public $scrapes;
    
    protected static $_strategy = null;
    
    private function getUrl()
    {
        return $this->_url;
    }

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
     * 
     * @param string $strategyName
     * @param Object Diggin_Scraper_Adapter_Interface (optional)
     * @return Object 
     */
    public function setStrategy($strategyName, $adapter = null)
    {
        require_once 'Zend/Loader.php';
        
        try {
            Zend_Loader::loadClass($strategyName);
        } catch (Zend_Exception $e) {
            throw new Diggin_Scraper_Client_Exception("Unable to load strategy '$strategyName': {$e->getMessage()}");
        }
        
        $strategy = new $strategyName($this->makeRequest());
        if($adapter) $strategy->setAdapter($adapter);

        self::$_strategy = $strategy;
    }

    /**
     * 
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
     * 
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
     * 
     */
    public function process($filterOrExpression, $name)
    {
        if(is_string($filterOrExpression)){
            require_once 'Diggin/Scraper/Process.php';
            $filterOrExpression = new Diggin_Scraper_Process($filterOrExpression, $name);
        }
        
        self::$_processes[] = $filterOrExpression;
        return $this;
    }
    
    public function scrape($url = null)
    {
        
        $response = $this->makeRequest($url);

        require_once 'Diggin/Scraper/Context.php';
        $context = new Diggin_Scraper_Context($this->getStrategy($response));
        
        foreach (self::$_processes as $process) {
            //@todo gettting TEXT format like using $process->filter['TEXT']
            $this->scrapes[$process->name] = $context->scrape($process->expression);
        }
        
        return $this->scrapes;
    }
}
