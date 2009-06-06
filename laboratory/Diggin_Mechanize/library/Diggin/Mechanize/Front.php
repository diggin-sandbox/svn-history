<?php

class Diggin_Mechanize_Front
{
    private function __construct(){}
    
    public static function getInstance()
    {
        return new self();
    }
}
