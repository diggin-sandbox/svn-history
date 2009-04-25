<?php
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

$console = new Zend_Console_Getopt(
    array(
    'xpath|x=s' => 'expression xpath or css selector',
    'type|v=s' => 'val type',
    'referer|e=s' => 'referer',
    'cookieJar|c=s' => 'cookie',
    'agent|a=s' => 'useragent',
    'nextlink|n=s' => 'nextlink',
    'depth|d=i' => 'depth "if not set nextlink, using wedata"',
    //"s|as-source" => '$as_xml',
    'basic|b=s' => 'basic auth "user/pass"',
    'cache|h=s' => 'cache with Zend_Cache',
    'wait|w=s' => 'sleep() :default 1',
    'filter|f=s' => 'filter for Diggin_Scraper',
    'out|o=i' => 'timeout',
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
        $stoptime = 1;
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

for ($i = 0; $i < $depth; $i++) {

    $client->setUri($url);
    
    if ($console->cache) {
        $cache = getCacheCore($console->cache);
        $response = requestWithCache($client, $cache, $url);
    } else {
        $response = $client->request();
    }

    if($console->depth && !isset($console->nextlink)){
        //searching wedata
       $nextLink = getNextLinkFromWedata($url, $console->cache) ;
    } else if ($console->depth && isset($console->nextlink)) {
       $nextLink = $console->nextlink;
    }
    
    $scraper = new Diggin_Scraper();
    $scraper->setUrl($url);
    $type = (isset($console->type))? $console->type : 'TEXT';
    if(isset($filter)) {
        $scraper->process($console->xpath, "xpath[] => $type, ".$filter.']');
    } else $scraper->process("$console->xpath", "xpath[] => $type");
    if (isset($nextLink)) {
        $scraper->process($nextLink, 'nextLink => "@href"');
    }
    $scraper->scrape($response);
  
    echo implode(PHP_EOL, $scraper->xpath);

    if(!isset($console->depth)) exit;
    if($scraper->nextLink === false) {
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
    $pattern = '#'.$item['data']['url'].'#i';
        //hAtom 対策
        if ('^https?://.' != $item['data']['url'] && (preg_match($pattern, $url) == 1)) {
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
 
        //#ZF-6040
        $httpResponse = new Diggin_Http_Response(
                            $httpResponse->getStatus(),
                            $httpResponse->getHeaders(),
                            $httpResponse->getRawBody(),
                            $httpResponse->getVersion(),
                            $httpResponse->getMessage());
     
        $cache->save($httpResponseString = $httpResponse->asString(), $key);
    
    }
    
    $res = Diggin_Http_Response::fromstring($httpResponseString);

    return $res;
}