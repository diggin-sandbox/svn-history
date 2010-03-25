<?php
/**
 * This class is remodeling of Zend_Debug
 * 
 * Zend Framework : 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

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
 * @package    Diggin_Debug
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

class Diggin_Debug
{
    /**
     * @var string
     */
    protected static $_sapi = null;
    
    /**
     * @var string
     */
    protected static $_config = array();

    /**
     * Get the current value of the debug output environment.
     * This defaults to the value of PHP_SAPI.
     *
     * @return string;
     */
    public static function getSapi()
    {
        if (self::$_sapi === null) {
            self::$_sapi = PHP_SAPI;
        }

        return self::$_sapi;
    }

    /**
     * Set the debug ouput environment.
     * Setting a value of null causes Zend_Debug to use PHP_SAPI.
     *
     * @param string $sapi
     * @return null;
     */
    public static function setSapi($sapi)
    {
        self::$_sapi = $sapi;
    }

    public static function setConfig($config)
    {
        //
        self::getConfig();

        foreach ($config as $conf => $setting) {
            self::$_config[strtolower($conf)] = $setting;
        }

    }

    protected static function getConfig()
    {

        if (!self::$_config) {
            if (PHP_OS === 'WINNT') {
            $config = array(
                        'label'        => null,
                        'echo'         => TRUE,
                        'to_encoding'   => 'sjis',
                        'from_encoding' => 'utf-8',
                        'start'        => 0,
                        'length'       => 80000,
                );
            } else {
            $config = array(
                        'label'        => null,
                        'echo'         => TRUE,
                        'start'        => 0,
                        'length'       => 80000,
                );
            }

            self::$_config = $config;
        }

        return self::$_config;
    }
    
    /**
     * dump args
     *
     * @param mixed $var 
     */
    public static function dump($var)
    {

        $config = self::getConfig();

        // format the label
        $label = ($config['label'] === null) ? '' : rtrim($config['label']) . ' ';
        
        $vars = func_get_args();

        array_walk($vars, function ($var, $config) use (&$output) {
            // var_dump the variable into a buffer and keep the output
            ob_start();
            var_dump($var);
            $out = ob_get_clean();
            if (isset($config['to_encoding']) and isset($config['from_encoding'])) {
                $out = mb_convert_encoding($out, $config['to_encoding'], $config['from_encoding']);
            }

            $output .= $out . PHP_EOL;
        });

        $output= substr($output, $config['start'], $config['length']);

        // neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        if (self::getSapi() == 'cli') {
            $output = $label
                    . PHP_EOL . $output;
        } else {
            $output = '<pre>'
                    . $label
                    . htmlspecialchars($output, ENT_QUOTES)
                    . '</pre>';
        }

        if ($config['echo'] === true) {
            echo($output);
        }

        return $output;
    }
}

