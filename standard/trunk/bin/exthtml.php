<?php
require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace('Diggin_');

$console = new Zend_Console_Getopt(
    array(
     'xpath|x=s' => 'expression xpath or css selector',
      'type|v=s' => 'val type',
   'referer|e=s' => 'referer',
 'cookieJar|c=s' => 'cookie',
     'agent|a=s' => 'useragent',
  'nextlink|n=s' => 'nextlink',
     'depth|d=i' => 'depth "if not set nextlink, using wedata"',
     'basic|b=s' => 'basic auth "user/pass"',
     'cache|h=s' => 'cache with Zend_Cache',
   'noCache|r'   => 'no-cache-force',
      'wait|w=s' => 'sleep() :default 1',
    'filter|f=s' => 'filter for Diggin_Scraper',
       'out|o=i' => 'timeout',
    'helper|l=s' => 'helper'
      )
);

    if(!$console->xpath) {
        throw new InvalidArgumentException('"now" must xpath: -x //html/body'.$console->getUsageMessage());
    }
    if(count($console->getRemainingArgs()) === 0) {
        throw new InvalidArgumentException('URL is not set'.$console->getUsageMessage());
    }

    //now suport one url
    $remaings = $console->getRemainingArgs();
    $url = $remaings[0];

    $client = new Zend_Http_Client();

    if ($console->agent) {
        $client->setConfig(array('useragent'=> $console->agent));
    }

    if ($console->basic) {
        list($basicusername, $basicpassword) = explode('/', $console->basic);
        if(!$basicpassword) throw new InvalidArgumentException('argument is not user/pass');
        $client->setAuth($basicusername, $basicpassword, Zend_Http_Client::AUTH_BASIC);
    }

    if ($console->referer) {
        $referer = (string)$console->referer;
        $client->setHeaders("Referer: $referer");
    }

    if ($console->cookieJar) {
        require_once 'Diggin/Http/CookieJar/Loader/Firefox3.php';
        if ($cookieJar = Diggin_Http_CookieJar_Loader_Firefox3::load($console->cookieJar, $url)) {
            $client->setCookieJar($cookieJar);
        }
    }

    if ($console->out) $client->setConfig(array('timeout' => $console->out));

    if ($console->wait) {
        $stoptime = $console->wait;
    } else {
        $stoptime = 1/10;
    }

    $depth = (isset($console->depth))? $console->depth : 1;

if ($console->filter){
    if ((substr($console->filter, 0, 2) === 's/') or 
        (substr($console->filter, 0, 2) === 's#')) {
        $quote = substr($console->filter,1,1);
        list($regex, $after) = explode($quote, substr($console->filter, 2));
        $filter = create_function('$var', <<<FUNC
return preg_replace('/'.preg_quote("$regex", '/').'/', "$after", \$var);
FUNC
);

} else {
        $filter = $console->filter;
    }
}

for ($i = 1; $i <= $depth; $i++) {

    $client->setUri($url);
    
    if ($console->cache && !isset($console->noCache)) {
        $cache = getCacheCore($console->cache);
        $response = requestWithCache($client, $cache, $url);
    } else {
        $response = $client->request();
    }

    if ($console->depth && !isset($console->nextlink)){
        //searching wedata
       $nextLink = getNextLinkFromWedata($url, $console->cache) ;
    } else if ($console->depth && isset($console->nextlink)) {
       $nextLink = $console->nextlink;
    }
    
    $scraper = new Diggin_Scraper();
    $scraper->setUrl($url);
    
    $type = (isset($console->type)) ? $console->type : 'TEXT';
    $helper = (isset($console->helper)) ? $console->helper : null;

    if (isset($filter)) {
        $scraper->process($console->xpath, "xpath[] => $type, ".$filter.']');
    } else {
        $scraper->process("$console->xpath", "xpath[] => $type");
    }

    if (isset($nextLink) && !($i == $depth)) {
        $scraper->process($nextLink, 'nextLink => "@href"');
    }

    $scraper->scrape($response);
  
    if ($helper) {
        try {
            $helperValue = $scraper->$helper();
            echo (is_array($helperValue)) ? $helperValue[0]: $helperValue;
        } catch (Exception $e){
            die($e);
        }
    } else {
        echo implode("\n", $scraper->xpath);
    }

    if (!isset($console->depth) or ($i == $depth)) exit;
    
    if ($scraper->nextLink === false) {
        Diggin_Debug::dump('next page not found');
        exit;
    } else {
        $url = $scraper->nextLink;
        echo PHP_EOL;
        sleep($stoptime);
    }
}




function getNextLinkFromWedata($url, $cache_dir = null)
{
    require_once 'Diggin/Service/Wedata.php';
        
    
    if ($cache_dir) {
        $frontendOptions = array(
            'lifetime' => 864000,
            'automatic_serialization' => true
        );
        
        $cache = getCacheCore($cache_dir, $frontendOptions);
        if(!$items = $cache->load('wedata_items')) {
            $items = Diggin_Service_Wedata::getItems('AutoPagerize');
            $cache->save($items, 'wedata_items');
        }
    } else {
        //@todo e_notice
        $items = Diggin_Service_Wedata::getItems('AutoPagerize');
    }
    
    $nextLink = getNextlink($items, $url);
    if($nextLink === false) {
        Diggin_Debug::dump('not found from wedata with url:'.$url);
        exit;
    }
    
    return $nextLink;
}
/**
 * Get next url from wedata('AutoPagerize')
 *
 * @param array $items
 * @param string $url base url
 * @return mixed
 */
function getNextlink($items, $url) {

    foreach ($items as $item) {
    
        //hAtom 対策
        if ('^https?://.' != $item['data']['url'] && (preg_match('>'.$item['data']['url'].'>', $url) == 1)) {
            $nextLink = $item['data']['nextLink'];
            return $nextLink;
        }
    }
    
    return false;
}

//
function getCacheCore($cache_dir, $frontendOptions= null)
{
    //cache
    if($frontendOptions === null) {
        $frontendOptions = array(
            'lifetime' => 86400,
        );
    }

    $backendOptions = array(
        'cache_dir' => $cache_dir
    );
     
    $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    
    return $cache;
}

function requestWithCache($client, $cache, $url)
{
    $key = str_replace(array('%', '.', '-'), array('__PER__', '__DOT__', '__HYP__'), rawurlencode($url));

    if (!$httpResponseString = $cache->load($key)) {
 
        try {
            $httpResponse = $client->request();
            
        } catch (Exception $e) {
            throw new $e;
        }
 
        $cache->save($httpResponseString = $httpResponse->asString(), $key);
    
    }
    
    $res = Zend_Http_Response::fromstring($httpResponseString);

    return $res;
}
