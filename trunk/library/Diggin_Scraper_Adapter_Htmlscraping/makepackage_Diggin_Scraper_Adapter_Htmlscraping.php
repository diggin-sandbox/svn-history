<?php
error_reporting(E_ALL);
$adapterpath = dirname(dirname(dirname(realpath(__FILE__)))).'/library/Diggin/Scraper/Adapter/';

$ignore = array('makepackage_Diggin.php', 
                'makepackage_Diggin_Scraper_Adapter_Htmlscraping.php',
                        '.svn');
foreach(new DirectoryIterator($adapterpath) as $file) {
	if($file->getFileName() !== 'Htmlscraping.php') { 
         $ignore[]= $file->getFileName();
	}
}
//print_r($ignore);exit;
require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);
$apiVersion = $releaseVersion = '0.2.0';
//$apiVersion = '0.2.0';
$changelog = '
  test pack
  ';
$notes = 'This is alpha release.';
$packagexml = new PEAR_PackageFileManager2();
$packagexml->setOptions(
array('filelistgenerator' => 'file',
      'packagefile' => 'package2.xml',
      'packagedirectory' => $adapterpath,
      'baseinstalldir' => 'Diggin/Scraper/Adapter/',
      'outputdirectory' => dirname(realpath(__FILE__)),
      'ignore' => $ignore,
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
//$packagexml->setChannel('diggin.musicrider.com');
$packagexml->setChannel('__uri');
$packagexml->setPackage('Diggin_Scraper_Adapter_Htmlscraping');
$packagexml->setReleaseVersion($releaseVersion);
$packagexml->setAPIVersion($apiVersion);
$packagexml->setReleaseStability('alpha');
$packagexml->setAPIStability('alpha');
$packagexml->setSummary('remodeling of HTMLScraping');
$packagexml->setDescription(<<<EOF
remodeling of HTMLScraping 
http://www.rcdtokyo.com/etc/htmlscraping/
EOF
);
$packagexml->setNotes($notes);
$packagexml->setPhpDep('5.2.0');
$packagexml->setPearinstallerDep('1.4.0a12');
$packagexml->addExtensionDep('required', 'simplexml');
$packagexml->addExtensionDep('required', 'tidy');
$packagexml->addMaintainer('lead', '(ucb)', 'Mika Sasezaki', 'sasezaki at gmail.com');
$packagexml->setLicense('GNU Lesser General Public', 'http://www.gnu.org/licenses/lgpl.html');
$packagexml->addGlobalReplacement('package-info', '@PEAR-VER@', 'version');
$packagexml->generateContents();
$pkg = &$packagexml->exportCompatiblePackageFile1();

$pkg->writePackageFile();
$packagexml->writePackageFile();