<?php
namespace Diggin\Loader;

require_once 'Zend/Loader/PluginLoader.php';

class PluginLoader extends \Zend_Loader_PluginLoader
{
    private static $_namespaceSeparator = '_';

    private static $_namespaceSet = false;

    /**
     * override Zend_Loader_PluginLoader's method
     *
     * @param $prefix string
     */
    protected function _formatPrefix($prefix)
    {
        if($prefix == "") {
            return $prefix;
        }
        return rtrim($prefix, static::$_namespaceSeparator) . static::$_namespaceSeparator;
    }

    /**
     * set Namespace-Seaprator
     *
     * @param $separator string
     */
    public static function setNamespaceSeparator($separator)
    {
        if (self::$_namespaceSet) {
            require_once 'Diggin/Loader/Exception.php';
            throw new \Diggin_Loader_Exception('Namespace-Separator is already set!');
        } else {
            self::$_namespaceSet = true;
        }

        static::$_namespaceSeparator = $separator;
    }
}

