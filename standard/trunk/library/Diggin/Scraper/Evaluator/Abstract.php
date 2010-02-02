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
 * @package    Diggin_Scraper
 * @subpackage Evaluator
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

abstract class Diggin_Scraper_Evaluator_Abstract
{
    private $_config = array();

    public function setConfig($config = array())
    {
        foreach ($config as $k => $v) {
            $this->_config[$k] = $v;
        }
    }

    public function getConfig($key)
    {
        return $this->_config[$key];
    }
}
