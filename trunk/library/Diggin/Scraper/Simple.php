<?php
class Diggin_Scraper_Simple
{
    /**
     * Zend_Http_Client Object
     *
     * @var Zend_Http_Client
     */
    protected $_client;

    protected $_url;
    
    //以下の変数に関わる処理は適当なので、後で設計しなおす必要が
    protected $_resetUrlFlg = FALSE;
    protected $_strategyName = "Tidy";
    protected $_strategyConfig;
    
    private function getUrl()
    {
        return $this->_url;
    }

    public function setUrl ($url) 
    {
        $this->_resetUrlFlg = TRUE;
        $this->_url = $url;
    }
    
    /**
     * HTTP client object to use for retrieving
     *
     * @var Zend_Http_Client
     */
    protected static $_httpClient = null;

    /**
     * Scraper Strategy object to use for retrieving
     *
     * @var Diggin
     */
    protected static $_strategy = null;
   
    /**
     * Override HTTP PUT and DELETE request methods?
     *
     * @var boolean
     */
    //protected static $_httpMethodOverride = false;

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

   public function changeStrategy($strategyName)
    {
        $this->_strategyName = $strategyName;
//        $this->_strategyConfig = $config;
    }

    public function getStrategy($body)
    {
    
        if ($this->_strategyName == "Tidy") {
            require_once 'Diggin/Scraper/Strategy/Tidy.php';
            self::$_strategy = new Diggin_Scraper_Strategy_Tidy($body);
        } else if ($this->_strategyName == "Loadhtml"){
            require_once 'Diggin/Scraper/Strategy/Loadhtml.php';
            self::$_strategy = new Diggin_Scraper_Strategy_Loadhtml($body);
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
     */
    public function makeRequest()
    {
        $this->_client = self::getHttpClient();
        
        if ($this->_url) {
            $this->_client->setUri($this->getUrl());
        }
        
        if (isset($parms)){
            $this->_client->setParameterGet($parms);
        }
        
        if ((!$this->_client->getLastResponse()) || ($this->_resetUrlFlg == TRUE)) {
            $response = $this->_client->request('GET');
            
            if (!$response->isSuccessful()) {
                 /**
                  * @see Diggin_Scraper_Exception
                  */
                 require_once 'Diggin/Scraper/Exception.php';
                 throw new Diggin_Scraper_Exception("Http client reported an error: '{$response->getMessage()}'");
            }
        } else {
            $response = $this->_client->getLastResponse();
        }
        
        $responseBody = $response->getBody();

        return $responseBody;
    }
    
    /**
     * 
     */
    private function getSimpleXML($body)
    {
        require_once 'Diggin/Scraper/Context.php';
        
        $strategy = $this->getStrategy($body);        
        $context  = new Diggin_Scraper_Context($strategy);
               
        return $context->getSimpleXml();
    }
    
    /**
     * 
     * 
     * @param string(xpath)
     * @return array
     */
    public function scrape($xpath) 
    {
        $results = array();
        
        $xml = $this->getSimpleXML($this->makeRequest());
        foreach ($xml->xpath($xpath) as $count => $result) {
            $results[] = $result; 
        }
        
        return $results;
    }
    
    /**
     * discovery "Real"URL acording attribute with Xpath
     * 
     * @param string
     * @param string
     * @return array
     */
    public function discovery($xpath = 'head/link[@type="application/rss+xml"]', $attribute = "href") 
    {
        $replaces = array();
        foreach ($this->getAttribute($xpath, $attribute) as $getAttribute) {
            $replaces[] = $this->getRealUrl($getAttribute);
        }
        
        return array_unique($replaces);
    }
    
    
    /**
     * Getting "Real" URL not URI
     * Replace HTML'S relative path
     *  
     * using pecl_http 
     * @see http://pecl4win.php.net/
     * 
     * @param string
     * @return string
     */
    public function getRealUrl($href)
    {
        $parse = parse_url($href);
        if (isset($parse["host"])) {
            $build = $href;
        } else {
            $uri = $this->getHttpClient()->getUri(TRUE);
            $uridir = pathinfo(parse_url($uri, PHP_URL_PATH), PATHINFO_DIRNAME);
            $slash = strpos($uridir, '/');
            if ($slash === false) {
                $build = http_build_url($uri, array("path" => $href,),
                HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT);
            } else {            
                $build = http_build_url($uri, array("path" => $href,), 
                HTTP_URL_JOIN_PATH | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT);
            }
        }
        
        return $build;
    }
    
    /**
     * Get Attribute
     * 
     * @param string
     * @param string
     * @return array
     */
    public function getAttribute($xpath, $attribute)
    {
        $getAttributes = array();
                
        $results = $this->scrape($xpath);
        foreach ($results as $result) {
            array_push($getAttributes, (string) $result[$attribute]);
        }
        
        return  $getAttributes;
    }

    
    /**
     * Getting head->title tag's value
     * 
     * @return string
     */
    public function getTitle ()
    {
        $results = $this->scrape('head/title');
                
        return trim((string) $results[0]); 
    }

    public function getUrls ()
    {
        $results = $this->getAttribute('//a', 'href');
                
        return $results; 
    } 
}
