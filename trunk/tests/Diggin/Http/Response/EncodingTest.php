<?php
require_once 'PHPUnit/Framework.php';

require_once 'Diggin/Http/Response/Encoding.php';

require_once 'Zend/Http/Response.php';

/**
 * Test class for Diggin_Http_Response_Encoding.
 * Generated by PHPUnit on 2008-12-11 at 21:42:20.
 */
class Diggin_Http_Response_EncodingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Diggin_Http_Response_Encoding
     * @access protected
     */
    protected $object;

    protected $responseHeaderUTF8;
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new Diggin_Http_Response_Encoding;
        $this->responseHeaderUTF8 =
           "HTTP/1.1 200 OK"        ."\r\n".
           "Date: Sat, 02 Aug 2008 15:17:11 GMT"."\r\n".
           "Server: Apache/2.2.6 (Win32) mod_ssl/2.2.6 OpenSSL/0.9.8e PHP/5.2.5"."\r\n".
           "Last-modified: Sun, 29 Jun 2008 21:20:50 GMT"."\r\n".
           "Accept-ranges: bytes"   . "\r\n" .
           "Content-length: 1000"   . "\r\n" .
           "Connection: close"      . "\r\n" .
           "Content-type: text/html; charset=utf-8;";
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    /**
     * @todo Implement testEncode().
     */
    public function testEncode() {
$header = <<<HEADER
HTTP/1.1 200 OK
Content-type: text/html charset=Shift-JIS;
HEADER;

$sjis1= mb_convert_encoding('あいうえお', 'SJIS', 'UTF-8');
$sjis2= mb_convert_encoding('かきくけこ', 'SJIS', 'UTF-8');

    $resBody = <<<BODY
<html lang="ja">
<head>
<body>
$sjis2
</body>
</html>
BODY;
    $expect = <<<BODY
<html lang="ja">
<head>
<body>
かきくけこ
</body>
</html>
BODY;
    
    $responseString = $header."\r\n\r\n".$resBody;
    $response = Zend_Http_Response::fromString($responseString);
    $ret = 
        Diggin_Http_Response_Encoding::encode($response->getBody(),
                                             $response->getHeader('content-type'),
                                             'UTF-8',
                                             $sjis1);
    $this->assertType('array', $ret);
    
    $this->assertEquals($expect, $ret[0]);
    $this->assertEquals('あいうえお', $ret[1]);
    
    }

    /**
     * @todo Implement testEncodeResponseObject().
     */
    public function testEncodeResponseObject() {
    $header = <<<HEADER
HTTP/1.1 200 OK
Content-type: text/html charset=Shift-JIS;
HEADER;

$sjis= mb_convert_encoding('あいうえお', 'SJIS', 'UTF-8');
    
    $resBody = <<<BODY
<html lang="ja">
<head>
<body>
$sjis
</body>
</html>
BODY;
    
$expect = <<<BODY
<html lang="ja">
<head>
<body>
あいうえお
</body>
</html>
BODY;
    
        $responseString = $header."\r\n\r\n".$resBody;
        
        $response = Zend_Http_Response::fromString($responseString);
        
        $encoded = $this->object->encodeResponseObject($response);
        
        $this->assertEquals($expect, $encoded);
        ////////////////////
        //@todo implement 
//        $response = HTTP_Request2_ResponseTest::
    }

    /**
     * test "detect" part.1
     * 
     */
    public function testDetectOnlyResponseBody() {
        //
        $this->assertEquals('Shift_JIS',
                            $this->object->detect(pack("C2", 0x87, 0x40)));

        //@see http://homepage2.nifty.com/Catra/memo/perl_pack.html
        $this->assertEquals('EUC-JP',
                            $this->object->detect(pack("C4", 164, 164, 164, 164)));
                            
        //this source is encoding with UTF-8.
        //if parameter has non-AlNum, must detect as UTF-8
        $this->assertEquals('UTF-8',
                            $this->object->detect('あ1ab'));
        
        
    }
    
    public function testDetectWithMetaTag() {
        //require_once 'Diggin/Http/';
$body = <<<BODY
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html;CHARSET=Shift_JIS">
    <title>test</title>
    </head>
<body>
</body>
BODY;
        $this->assertEquals('Shift_JIS',
                            $this->object->detect($body));
    }
    
    
    /**
     * test "detect" part.2 
     */
    public function testDetectWithHeadersContentType() {
        //////header("Content-type: text/html; charset=utf-8;");
        $header = "Content-type: text/html; charset=utf-8;";
        
        $bodyUTF8 = <<<BODY
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html;CHARSET=Shift_JIS">
    <title>test</title>
    </head>
<body>
</body>
BODY;
        $bodySJIS = mb_convert_encoding($bodyUTF8, 'SJIS', 'UTF-8');
        
        //browser 
        $this->assertEquals('UTF-8',
                            $this->object->detect($bodySJIS, $header));
    }
}
?>
