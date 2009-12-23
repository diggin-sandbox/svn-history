<?php
$path = explode(PATH_SEPARATOR, get_include_path());
array_push($path, '/home/kazusuke/dev/workspace/Diggin/standard/incubator/library/');
set_include_path(implode(PATH_SEPARATOR, $path));



$url = (isset($argv[1]) ? $argv[1]: 'http://d.hatena.ne.jp/sasezaki');

require_once 'Diggin/Scraper.php';
require_once 'Diggin/Scraper/Helper/Simplexml/Pagerize.php';

//1 Zend_Cache_Coreをセットします
Diggin_Scraper_Helper_Simplexml_Pagerize::setCache(getCache());
//2 キーを指定してsiteinfo配列をセットします
Diggin_Scraper_Helper_Simplexml_Pagerize::appendSiteInfo('mysiteinfodom', 
                                array(
                                     array('url' => 'http://d.hatena.ne.jp/sasezaki', 
                                           'nextLink' => '//a[@class="prev" and last()]'),
                                     array('url' => 'http://framework.zend.com/code/changelog/Standard_Library/',
                                           'nextLink' => '//div[@class="changesetList"][last()]/a'),
                                     array('url' => 'http://musicrider.com/',
                                           'nextLink' => '//a'),
                                     array('url' => 'http://kanzaki.com/',
                                           'nextLink' => '//a')
                                          )
                                );

require_once 'Zend/Http/Client.php';
$client = new Zend_Http_Client($url);
$response = $client->request();


$domDocument = new DOMDocument();
$domDocument->preserveWhiteSpace = true; //according Spizer_Document_Xml
@$domDocument->loadHtml(getBody($response));

$pagerize = new Diggin_Scraper_Helper_Simplexml_Pagerize(simplexml_import_dom($domDocument),
                            array('baseUrl' => $url,
                                'preAmpFilter' => true)
                          );
var_dump($pagerize->getNextLink());

                                
                                
function getCache($cache_dir = '.', $frontendOptions= null)
{
    //cache
    if($frontendOptions === null) {
        $frontendOptions = array(
            'lifetime' => 86400,
        'automatic_serialization' => true
        );
    }

    $backendOptions = array(
        'cache_dir' => $cache_dir
    );
    
    require_once 'Zend/Cache.php';
    return Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
}

function getBody($response)
{
    require_once 'Diggin/Scraper/Adapter/Htmlscraping.php';
    $responseAdapter = new Diggin_Scraper_Adapter_Htmlscraping();
        // get foramted XHTML,
        // and encoding to UTF-8 by header's ctype, meta-charset, responseBody
        $xhtml = $responseAdapter->getXhtml($response);

        //remove namepsace
        $xhtml = preg_replace(array('/\sxmlns:?[A-Za-z]*="[^"]+"/', "/\sxmlns:?[A-Za-z]*='[^']+'/"), '', $xhtml);

        return $xhtml;
}