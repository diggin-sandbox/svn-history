<?php
return;
require_once 'PHPUnit/Framework.php';

require_once 'Diggin/Http/Response/CharactorEncoding.php';

require_once 'Zend/Http/Response.php';

/**
 * Test class for Diggin_Http_Response_CharactorEncoding
 * borrowd Diggin_Http_Response_Encoding
 */
class Diggin_Http_Response_CharactorEncodingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Diggin_Http_Response_CharactorEncoding
     * @access protected
     */
    protected $object;

    protected $responseHeaderUTF8;

    private $detectOrder;
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {

        $this->detectOrder = mb_detect_order();

        $this->object = new Diggin_Http_Response_CharactorEncoding;
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
        mb_detect_order($this->detectOrder);
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

    
    public function testDetect_Restore() {

        $iniDetectOrder = mb_detect_order();

        $testerDetectOrder = mb_detect_order('UTF-8, SJIS');

$body = <<<BODY
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html;CHARSET=Shift_JIS">
        <title>test</title>
            </head>
            <body>
            </body>
BODY;
        $this->object->setDetectOrder('ASCII, SJIS');
        $this->object->detect($body); //run mb_detect
        $this->object->setDetectOrder(Diggin_Http_Response_CharactorEncoding::DETECT_ORDER); //restore object's order

        //restore ok ?
        $this->assertEquals(array('UTF-8', 'SJIS'), mb_detect_order());

        //restore
        mb_detect_order($iniDetectOrder);
    }


    /**
     *
     *
     */
    public function testSetDetectOrder() {

        //
        $this->assertEquals(Diggin_Http_Response_CharactorEncoding::DETECT_ORDER,
                            Diggin_Http_Response_CharactorEncoding::getDetectOrder());
        
        $detectOrder = 'SJIS, UTF-8';
        Diggin_Http_Response_CharactorEncoding::setDetectOrder($detectOrder);
        
        $this->assertEquals($detectOrder,
                            Diggin_Http_Response_CharactorEncoding::getDetectOrder());


        Diggin_Http_Response_CharactorEncoding::setDetectOrder(false);

        $this->assertEquals(Diggin_Http_Response_CharactorEncoding::DETECT_ORDER,
                            Diggin_Http_Response_CharactorEncoding::getDetectOrder());

    }
}
?>
