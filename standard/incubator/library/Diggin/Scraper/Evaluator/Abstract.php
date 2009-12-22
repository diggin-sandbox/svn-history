<?php

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
