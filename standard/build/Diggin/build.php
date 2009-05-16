<?php
error_reporting(E_ALL);
//snippet
//ls /usr/share/php/Diggin 
//sudo pear uninstall __uri/Diggin
//sudo pear uninstall openpear/Diggin_Scraper_Adapter_Htmlscraping

//pear remote-info openpear/Diggin_Scraper_Adapter_Htmlscraping 
//sudo pear install --alldeps --force http://diggin.musicrider.com/archive/Diggin-0.5.3.tgz

/**
 * NOTES:
 * 
 * function, dircopy & get_dir_tree is copy from manual
 * http://jp2.php.net/manual/ja/function.copy.php
 */
require_once ('dircopy.php');


$releaseVersion = '0.5.3';
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
include $releasepath.DIRECTORY_SEPARATOR.'Diggin'.DIRECTORY_SEPARATOR.'makepackage_Diggin.php';

//echo ("sudo pear package ");
var_dump("pear package $releasepath/Diggin/package2.xml");
exec("pear package $releasepath/Diggin/package2.xml");

