<?php

interface Diggin_Scraper_Adapter_Interface
{
    
    /**
     * Set the configuration array for the adapter
     *
     * @param array $config
     */
    public function setConfig($config = array());

    public function readData($response);
}