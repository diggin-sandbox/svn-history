<?php

require_once 'ZFDebug/Controller/Plugin/Debug/Plugin/Interface.php';
require_once 'Diggin/Version.php';

class Diggin_Controller_Plugin_ZFDebugbar_Plugin_DigginVersion
        implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{
    protected $_identifier = 'diggin_version';
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    public function getTab()
    {
        return Diggin_Version::VERSION;
    }

    public function getPanel()
    {
        //return '';  // 空でもOK
        //
        $msg = "Diggin - Simplicity PHP library";
        $msg .= "<br />since 2006";
        $msg .= "<br /> checks extensions...<br />";
        $msg .= extension_loaded('tidy') ? 'tidy is available' : 'tidy is not loaded!';

        return $msg;
    }
}
