<?php
require_once 'rhaco/Rhaco.php';
Rhaco::import("tag.model.SimpleTag");
require_once 'Diggin/Scraper/Strategy/Abstract.php';

class Diggin_Scraper_Strategy_SimpleTag extends Diggin_Scraper_Strategy_Abstract
{
    protected function readData($resposeBody)
    {
        return $resposeBody;
    }
    
    public function scrape($resposeBody, $process)
    {
        SimpleTag::setof($xml, $resposeBody);
        $result = $xml->getIn($process);
        
        return $result;
    }
}
