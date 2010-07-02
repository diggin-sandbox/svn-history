<?php

/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://diggin.musicrider.com/LICENSE
 * 
 * @category   Diggin
 * @package    Diggin_Tool
 * @subpackage Framework
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @category   Diggin
 * @package    Diggin_Tool
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 * 
 * This code was mostly adapted from Zend_Tool_Framework_Client_Console - a part of the Zend Framework
 * Copyright (c) 2005-2010 Zend Technologies USA Inc., licensed under the
 * New BSD License. See http://framework.zend.com for more information.
 */
require_once 'Zend/Tool/Framework/Client/Console.php';

class Diggin_Tool_Framework_Client_Console extends Zend_Tool_Framework_Client_Console
{

    private $_shName;

    public function setShName($shName)
    {
        $this->_shName = $shName;
    }

    public function getShName()
    {
        return $this->_shName;
    }

    /**
     * _preDispatch() - Tasks handed after initialization but before dispatching
     *
     */
    protected function _preDispatch()
    {
        $response = $this->_registry->getResponse();

        $response->addContentDecorator(new Zend_Tool_Framework_Client_Console_ResponseDecorator_AlignCenter());
        $response->addContentDecorator(new Zend_Tool_Framework_Client_Console_ResponseDecorator_Indention());
        $response->addContentDecorator(new Zend_Tool_Framework_Client_Console_ResponseDecorator_Blockize());

        if (function_exists('posix_isatty')) {
            $response->addContentDecorator(new Zend_Tool_Framework_Client_Console_ResponseDecorator_Colorizer());
        }

        $response->addContentDecorator(new Zend_Tool_Framework_Client_Response_ContentDecorator_Separator())
            ->setDefaultDecoratorOptions(array('separator' => true));

        require_once 'Diggin/Tool/Framework/Client/Console/ArgumentParser.php';
        $optParser = new Diggin_Tool_Framework_Client_Console_ArgumentParser();
        $optParser->setShName($this->_shName);
        $optParser->setArguments($_SERVER['argv'])
            ->setRegistry($this->_registry)
            ->parse();

        return;
    }

    /**
     * _postDispatch() - Tasks handled after dispatching
     *
     */
    protected function _postDispatch()
    {
        $request = $this->_registry->getRequest();
        $response = $this->_registry->getResponse();

        if ($response->isException()) {
            require_once 'Diggin/Tool/Framework/Client/Console/HelpSystem.php';
            $helpSystem = new Diggin_Tool_Framework_Client_Console_HelpSystem();
            $helpSystem->setShName($this->_shName);
            $helpSystem->setRegistry($this->_registry)
                ->respondWithErrorMessage($response->getException()->getMessage(), $response->getException())
                ->respondWithSpecialtyAndParamHelp(
                    $request->getProviderName(),
                    $request->getActionName()
                    );
        }

        echo PHP_EOL;
        return;
    }

    public function handleInteractiveInputRequest(Zend_Tool_Framework_Client_Interactive_InputRequest $inputRequest)
    {
        fwrite(STDOUT, $inputRequest->getContent() . PHP_EOL . $this->_shName . '> ');
        $inputContent = fgets(STDIN);
        return rtrim($inputContent); // remove the return from the end of the string
    }

}
