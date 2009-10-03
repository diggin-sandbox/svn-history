<?php
require_once 'Zend/Http/Response.php';
require_once 'Diggin/Http/Response/CharactorEncoding/Wrapper/WrapperInterface.php';

class Diggin_Http_Response_CharactorEncoding_Wrapper_Zf 
    extends Zend_Http_Response implements Diggin_Http_Response_CharactorEncoding_Wrapper_WrapperInterface
{
    private $_encodingFrom;
    private $_encodingTo;

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

    public function getBody()
    {
        require_once 'Diggin/Http/Response/CharactorEncoding.php';
        $body = Diggin_Http_Response_CharactorEncoding::mbconvert(parent::getBody(), 
                                                       $this->getEncodingFrom(), 
                                                       $this->getEncodingTo());
        return $body;
    }

    final public function setEncodingFrom($encoding_from)
    {
        $this->_encodingFrom = $encoding_from;
    }

    final public function getEncodingFrom()
    {
        return $this->_encodingFrom;
    }

    final public function setEncodingTo($encoding_to)
    {
        $this->_encodingTo = $encoding_to;
    }

    final public function getEncodingTo()
    {
        return $this->_encodingTo;
    }
}
