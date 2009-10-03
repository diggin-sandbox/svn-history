<?php

interface Diggin_Http_Response_CharactorEncoding_Wrapper_WrapperInterface
{
    public static function createWrapper($response, $encoding_from, $encoding_to = 'UTF-8');
}
