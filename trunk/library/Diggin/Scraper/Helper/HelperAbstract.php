<?php

abstract class Diggin_Scraper_Helper_HelperAbstract
{
    public $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    abstract public function direct();

    public function __invoke()
    {
        return $this->direct();
    }
}
