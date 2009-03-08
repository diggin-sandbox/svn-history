<?php

require_once 'Diggin/Scraper/Helper/Abstract.php';
class Diggin_Scraper_Helper_Simplexml_Title extends Diggin_Scraper_Helper_Abstract
{
    public function direct()
    {
        if ($titles = $this->resource->xpath('//title')) {
            return (string) current($titles);
        }

        return false;
    }
}
