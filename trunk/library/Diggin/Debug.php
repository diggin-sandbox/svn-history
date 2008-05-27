<?php
//for win32 cmd.exe

class Diggin_Debug
{

    /**
     * @var string
     */
    protected static $_sapi = null;

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

    public static function dump($var, $configs = array())
    {
        $config = array(
                    'label'        => null,
                    'echo'         => TRUE,
                    'toEncoding'   => 'sjis',
                    'fromEncoding' => 'utf-8',
                    'start'        => 0,
                    'length'       => 80000,
        );

        foreach ($configs as $conf => $setting) {
            $config[strtolower($conf)] = $setting;
        }

        // format the label
        $label = ($config['label']===null) ? '' : rtrim($config['label']) . ' ';

        // var_dump the variable into a buffer and keep the output
        ob_start();        
        var_dump($var);
        $output = ob_get_clean();
        $output = mb_convert_encoding($output, $config['toEncoding'], $config['fromEncoding']);
        $output= substr($output, $config['start'], $config['length']);

        // neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        if (self::getSapi() == 'cli') {
            $output = PHP_EOL . $label
                    . PHP_EOL . $output
                    . PHP_EOL;
        } else {
            $output = '<pre>'
                    . $label
                    . htmlspecialchars($output, ENT_QUOTES)
                    . '</pre>';
        }

        if ($config['echo'] === TRUE) {
            echo($output);
        }
        return $output;
    }

}

