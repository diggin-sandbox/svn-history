<?php
$adapterpath = dirname(dirname(dirname(realpath(__FILE__)))).'/library/Diggin/Scraper/Adapter/';
// copy file this directory
copy($adapterpath.'Htmlscraping.php', 'Htmlscraping.php');
// make package2.xml
include 'makepackage_Diggin_Scraper_Adapter_Htmlscraping.php';
exec('sudo pear package package2.xml');
// delete file
unlink('Htmlscraping.php');