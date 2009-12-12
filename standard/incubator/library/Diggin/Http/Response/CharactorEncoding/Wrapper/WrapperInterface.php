<?php

/**
 * Diggin - Simplicity PHP Library
 * 
 * @category   Diggin
 * @package    Diggin_Http
 * @subpackage Response_CharactorEncoding
 */
interface Diggin_Http_Response_CharactorEncoding_Wrapper_WrapperInterface
{
    /**
     * Create wrapper instance
     *
     * @param Zend_Http_Response $response
     * @param string $encoding_from
     * @param string $encoding_to
     * @return Diggin_Http_Response_CharactorEncoding_Wrapper_WrapperInterface
     */
    public static function createWrapper($response, $encoding_from, $encoding_to = 'UTF-8');
}
