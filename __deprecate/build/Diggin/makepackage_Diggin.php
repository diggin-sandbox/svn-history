<?php
error_reporting(E_ALL);
require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);
//$apiVersion = $releaseVersion = '0.4.0';
$apiVersion = $releaseVersion = substr(dirname(dirname(__FILE__)), strlen(dirname(dirname(dirname(__FILE__))))+1);
//var_dump($apiVersion);exit;
$changelog = '
  test pack
  ';
$notes = 'This is alpha release.';
$packagexml = new PEAR_PackageFileManager2();
$packagexml->setOptions(
array('filelistgenerator' => 'file',
      'packagefile' => 'package2.xml',
      'packagedirectory' => dirname(realpath(__FILE__)),
//      'outputdirectory' => dirname(realpath(__FILE__)),
      'baseinstalldir' => 'Diggin',
      'ignore' => array('makepackage_Diggin.php',
                        'makepackage_Diggin_Scraper_Adapter_Htmlscraping.php', 
                        'Htmlscraping.php',
                        'Encoding.php',
                        '.svn'),
      'simpleoutput' => true,
      'changelogoldtonew' => true,
      'changelognotes' => $changelog,
      'exceptions' => array('ChangeLog' => 'doc'),
      'dir_roles' => array('demos' => 'doc', 
                           'docs' => 'doc',
                           'tests' => 'test'
                            )));
$packagexml->setPackageType('php');
$packagexml->addRelease();
$packagexml->setChannel('__uri');
$packagexml->setPackage('Diggin');
$packagexml->setReleaseVersion($releaseVersion);
$packagexml->setAPIVersion($apiVersion);
$packagexml->setReleaseStability('alpha');
$packagexml->setAPIStability('alpha');
$packagexml->setSummary('Simplicity PHP Library');
$packagexml->setDescription('Simplicity PHP Library');
$packagexml->setNotes($notes);
$packagexml->setPhpDep('5.2.0');
$packagexml->setPearinstallerDep('1.4.3');
$packagexml->addExtensionDep('required', 'simplexml');
$packagexml->addExtensionDep('required', 'pcre');
$packagexml->addExtensionDep('required', 'mbstring');
$packagexml->addExtensionDep('required', 'ctype');
$packagexml->addPackageDepWithChannel('required', 'Diggin_Scraper_Adapter_Htmlscraping', 'openpear.org');
//$packagexml->addPackageDepWithUri('required',
//                                  'Diggin_Scraper_Adapter_Htmlscraping',
//                                  'http://diggin.musicrider.com/Diggin_Scraper_Adapter_Htmlscraping');
$packagexml->addPackageDepWithChannel('optional', 'Net_CDDB', 'pear.php.net');
$packagexml->addPackageDepWithChannel('optional', 'Net_URL2','pear.php.net', '0.2.0');
$packagexml->addMaintainer('lead', 'sasezaki', 'Mika Sasezaki', 'sasezaki at gmail.com');
$packagexml->setLicense('New BSD license', 'http://framework.zend.com/license/new-bsd');
$packagexml->addGlobalReplacement('package-info', '@PEAR-VER@', 'version');
$packagexml->generateContents();
$pkg = &$packagexml->exportCompatiblePackageFile1();

$pkg->writePackageFile();
$packagexml->writePackageFile();