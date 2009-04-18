<?php
// for ZF-6040
require_once 'Zend/Http/Response.php';
class Diggin_Http_Response extends Zend_Http_Response
{
    //5.2以前では遅延評価がないため単純にオーバーライド
    public function getBody()
    {
        $body = '';

        // Decode the body if it was transfer-encoded
        switch ($this->getHeader('transfer-encoding')) {

            // Handle chunked body
            case 'chunked':
                $body = self::decodeChunkedBody($this->body);
                break;

            // No transfer encoding, or unknown encoding extension:
            // return body as is
            default:
                $body = $this->body;
                break;
        }

        // Decode any content-encoding (gzip or deflate) if needed
        switch (strtolower($this->getHeader('content-encoding'))) {

            // Handle gzip encoding
            case 'gzip':
                $body = self::decodeGzip($body);
                break;

            // Handle deflate encoding
            case 'deflate':
                $body = self::decodeDeflate($body);
                break;

            default:
                break;
        }

        return $body;
    }

    //override
    public static function decodeDeflate($data)
    {
        if (!function_exists('gzuncompress')) {
            throw new Diggin_Http_Response_Exception('Unable to decode body: gzip extension not available');
        }

        // copy HTTP_Request2_Response

        // RFC 2616 defines 'deflate' encoding as zlib format from RFC 1950,
        // while many applications send raw deflate stream from RFC 1951.
        // We should check for presence of zlib header and use gzuncompress() or
        // gzinflate() as needed. See bug #15305
        $header = unpack('n', substr($data, 0, 2));
        return (0 == $header[1] % 31)? gzuncompress($data): gzinflate($data);
    }
    
    //
    public static function fromString($response_str)
    {
        $code    = self::extractCode($response_str);
        $headers = self::extractHeaders($response_str);
        $body    = self::extractBody($response_str);
        $version = self::extractVersion($response_str);
        $message = self::extractMessage($response_str);

        return new self($code, $headers, $body, $version, $message);
    }
}
