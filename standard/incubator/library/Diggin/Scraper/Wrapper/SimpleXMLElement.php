<?php

class Diggin_Scraper_Wrapper_SimpleXMLElement extends SimpleXMLElement
{
    public function asXML()
    {
         return htmlspecialchars_decode(parent::asXML(), ENT_NOQUOTES);
    }
}
