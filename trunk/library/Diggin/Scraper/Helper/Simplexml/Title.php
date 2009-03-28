<?php

require_once 'Diggin/Scraper/Helper/Abstract.php';
class Diggin_Scraper_Helper_Simplexml_Title extends Diggin_Scraper_Helper_Abstract
{
    public function direct()
    {
        if ($titles = $this->resource->xpath('//title')) {
                $value = htmlspecialchars_decode(current($titles)->asXML(),
                        ENT_NOQUOTES);
                $value = html_entity_decode($value, ENT_NOQUOTES, 'UTF-8');
                $value = str_replace(array(chr(9), chr(10), chr(13)),'', $value);
            return trim(preg_replace(array('#^<.*?>#', '#s*</\w+>\n*$#'), '', $value));
        }

        return false;
    }
}
