<?php

/**
 * Diggin - Simplicity PHP Library
 * 
 * @category   Diggin
 * @package    Diggin_Http
 * @subpackage Response_CharactorEncoding
 */

/** Zend_Http_Response */
require_once 'Zend/Http/Response.php';
/** Diggin_Http_Response_CharactorEncoding_Wrapper_WrapperInterface */
require_once 'Diggin/Http/Response/CharactorEncoding/Wrapper/WrapperInterface.php';

class Diggin_Http_Response_CharactorEncoding_Wrapper_Zf 
    extends Zend_Http_Response implements Diggin_Http_Response_CharactorEncoding_Wrapper_WrapperInterface
{
    /**
     * @var string character code names before conversion
     */
    private $_encodingFrom;

    /**
     * @var The type of encoding
     */
    private $_encodingTo;

    /**
     * Create wrapper instance
     *
     * @param Zend_Http_Response $response
     * @param string $encoding_from
     * @param string $encoding_to
     * @return Diggin_Http_Response_CharactorEncoding_Wrapper_Zf
     */
    public static function createWrapper($response, $encoding_from, $encoding_to = 'UTF-8')
    {
        $httpResponse = new self($response->getStatus(), 
                                 $response->getHeaders(),
                                 $response->getRawBody(),
                                 $response->getVersion(),
                                 $response->getMessage());

        $httpResponse->setEncodingFrom($encoding_from);
        $httpResponse->setEncodingTo($encoding_to);

        return $httpResponse;
    }

    /**
     * Get converted response's body
     *
     * @return string
     */
    public function getBody()
    {
        require_once 'Diggin/Http/Response/CharactorEncoding.php';
        $body = Diggin_Http_Response_CharactorEncoding::mbconvert(parent::getBody(), 
                                                       $this->getEncodingFrom(), 
                                                       $this->getEncodingTo());
        return $body;
    }


    /**
     * Set character code name before conversion
     *
     * @param string $encoding_from
     */
    final public function setEncodingFrom($encoding_from)
    {
        $this->_encodingFrom = $encoding_from;
    }

    /**
     * Get character code name before conversion
     *
     * @return string
     */
    final public function getEncodingFrom()
    {
        return $this->_encodingFrom;
    }

    /**
     * Set charactor code name that response's body is being converted to
     *
     * @param string $encoding_to
     */
    final public function setEncodingTo($encoding_to)
    {
        $this->_encodingTo = $encoding_to;
    }

    /**
     * Get charactor code name that response's body is being converted to
     *
     * @return string
     */
    final public function getEncodingTo()
    {
        return $this->_encodingTo;
    }
}
