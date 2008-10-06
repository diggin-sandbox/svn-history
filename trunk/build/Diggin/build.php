<?php
//snippet
//sudo pear uninstall __uri/Diggin
//sudo pear uninstall __uri/Diggin_Scraper_Adapter_Htmlscraping

$releaseVersion = '0.6.0';
$releasepath = dirname(__FILE__).DIRECTORY_SEPARATOR.$releaseVersion;

$librarypath = realpath(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'library');
$demospath = realpath(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'demos');
$testspath = realpath(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'tests');
if ($librarypath === false or 
    $demospath === false or 
    $testspath === false) {
    die('path is not valid');
}

//copy file release directory
dircopy($librarypath, $releasepath);
dircopy($demospath, $releasepath.DIRECTORY_SEPARATOR.'Diggin/'.'demos');
dircopy($testspath, $releasepath.DIRECTORY_SEPARATOR.'Diggin/'.'tests');
copy('makepackage_Diggin.php', $releasepath.DIRECTORY_SEPARATOR.'Diggin/'.'makepackage_Diggin.php');
// make package2.xml
include $releasepath.DIRECTORY_SEPARATOR.'Diggin/'.'makepackage_Diggin.php';

//echo ("sudo pear package ");
//exec("sudo pear package $releasepath.DIRECTORY_SEPARATOR.'Diggin/'.package2.xml");

/**
 * NOTES:
 * 
 * function, dircopy & get_dir_tree is copy from manual
 * http://jp2.php.net/manual/ja/function.pathinfo.php
 */
//paste here!