<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * 
 * @category   Diggin
 * @package    Diggin_Scraper
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
require_once 'Diggin/Scraper/Adapter/Interface.php';
require_once 'Diggin/Scraper/Adapter/StringInterface.php';

class Diggin_Scraper_Adapter_Normal
    implements Diggin_Scraper_Adapter_Interface, Diggin_Scraper_Adapter_StringInterface
{
    protected $_config = array();
    
    /**
     * Readdata as just getBody() 
     * (not rawBody and not html converting)
     * 
     * @param string $response
     * @retrun Object Raw
     */
    public function readData($response)
    {   
        return $response->getBody();
    }

    public function setConfig($config = array())
    {
        if (! is_array($config))
            throw new Diggin_Scraper_Adapter_Exception('Expected array parameter, given ' . gettype($config));

        foreach ($config as $k => $v)
            $this->_config[strtolower($k)] = $v;

        return $this;
    }
}
