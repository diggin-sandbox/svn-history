<?php


/**
 * Include PHPUnit dependencies
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

error_reporting( E_ALL | E_STRICT );


//
$digginRoot = dirname(__FILE__)."/../../";
$digginCoreLibrary = realpath("$digginRoot/library/");
//$digginCoreTests = realpath("$digginRoot/tests");
$digginHttpResponseCharset = realpath("$digginRoot/../../../Diggin_Http_Response_Charset/library");
//$digginScraperAdapterHtmlscraping = realpath("$digginRoot/../../../Diggin_Scraper_Adapter_Htmlscraping/trunk/library");
//$path = array($digginCoreLibrary, $digginHttpResponseCharset, $digginScraperAdapterHtmlscraping, get_include_path());
$path = array($digginCoreLibrary, $digginHttpResponseCharset, get_include_path());
set_include_path(implode(PATH_SEPARATOR, $path));


//if (is_readable($digginCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
//    require_once $digginCoreTests . DIRECTORY_SEPARATOR . 'Diggin' .DIRECTORY_SEPARATOR.'TestConfiguration.php';
//} else {
//    require_once $digginCoreTests . DIRECTORY_SEPARATOR . 'Diggin' . DIRECTORY_SEPARATOR .'TestConfiguration.php.dist';
//}

unset($digginRoot, $digginCoreLibrary, $digginCoreTests, $path);


