<?php
require_once 'Zend/Filter/Interface.php';

class Diggin_Scraper_Process implements Zend_Filter_Interface
{

    public $expression;
    public $name;

    public function __construct($expression, $name)
    {
        $this->expression = $expression;
        $this->name = $name;
    }

    public function filter($value)
    {
        
        return $value;
    }
}