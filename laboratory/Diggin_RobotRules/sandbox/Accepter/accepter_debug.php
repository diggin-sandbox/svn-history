<?php
set_include_path(dirname(__FILE__) . '/../../library' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);

$accepter = new Diggin_RobotRules_Accepter($userAgent);
$accepter->addRule();
$accepter->addRule();
$accepter->addRule();