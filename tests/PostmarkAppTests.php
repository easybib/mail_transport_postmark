<?php

require_once 'Postmark/Mail/Transport/Postmark.php';
require_once 'Services/PostmarkApp.php';
require_once 'Zend/Http/Client/Adapter/Test.php';


class Services_PostmarkApp_TestCase extends PHPUnit_Framework_TestCase
{

    private $_apiKey = 'c56ceeff-0661-4cd9-a85d-a0e739c1f71f';
    private $_from   = 'test postmark <easybib_postmark@mailinator.com';

    private function _getMakeRequestMock()
    {
        return $this->getMock(
            'Services_PostmarkApp',
            array('makeRequest',),
            array($this->_apiKey)
        );

    }

    public function testWrongSignatureThrowsException()
    {
        $this->setExpectedException('Exception');

        $options = array(
            'From'     => 'yvan volochine <contact@anymail.com>',
            'To'       => 'egr <egr@anymail.com>',
            'Subject'  => 'hola huevon !!!',
            'TextBody' => 'salut bonjour buenos dias'
        );

        $postmark = new Services_PostmarkApp('a-Wrong-Key');
        $postmark->send($options);
    }


    public function testMissingOptionsThrowsException()
    {
        $this->setExpectedException('LogicException');

        $postmark = new Services_PostmarkApp($this->_apiKey);
        $postmark->send(array());
    }


    public function testMissingFromThrowsException()
    {
        $this->setExpectedException('LogicException');

        $options = array(
            'To'       => 'egr <any@mail.com>',
            'Subject'  => 'hola huevon !!!',
            'TextBody' => 'salut bonjour buenos dias'
        );

        $postmark = new Services_PostmarkApp($this->_apiKey);
        $postmark->send($options);
    }


    public function testMissingToThrowsException()
    {
        $this->setExpectedException('LogicException');

        $options = array(
            'From'     => $this->_from,
            'Subject'  => 'hola huevon !!!',
            'TExtBody' => 'salut bonjour buenos dias'
        );

        $postmark = new Services_PostmarkApp($this->_apiKey);
        $postmark->send($options);
    }


    public function testSetGetClient()
    {
        $pm = new Services_PostmarkApp($this->_apiKey);
        $pm->setClient(new Zend_Http_Client);
        $client = $pm->getClient();
        $this->assertInstanceOf('Zend_Http_Client', $client);
    }

    /**
     * Test various exceptions from the PostmarkApp service.
     */
    public function testAnExceptionFromPostmark()
    {
        $exception = 'RuntimeException';

        $this->setExpectedException($exception);

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "blabla"}';
        $code = 500;

        $client = $this->getHttpClient();

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($client);

        $pm->send(array());
    }

    /**
     * @return Zend_Http_Client
     */
    protected function getHttpClient()
    {
        $client = new Zend_Http_Client;
        $client->setAdapter(new Zend_Http_Client_Adapter_Test);
        return $client;
    }


    /**
     * Test parseResponse exceptions.
     */
    public function testNonAuthorizedExceptionFromParseResponse()
    {
        $this->setExpectedException('ErrorException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": ""}';
        $code = 401;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testInvalidApiExceptionFromParseResponse()
    {
        $this->setExpectedException('LogicException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": "0"}';
        $code = 422;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testValidationFailedExceptionFromParseResponse()
    {
        $this->setExpectedException('LogicException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": "300"}';
        $code = 422;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testSignatureNotFoundExceptionFromParseResponse()
    {
        $this->setExpectedException('LogicException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": "400"}';
        $code = 422;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testSignatureNotConfirmedExceptionFromParseResponse()
    {
        $this->setExpectedException('LogicException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": "401"}';
        $code = 422;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testInvalidJsonExceptionFromParseResponse()
    {
        $this->setExpectedException('LogicException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": "402"}';
        $code = 422;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testIncompatibleJsonExceptionFromParseResponse()
    {
        $this->setExpectedException('LogicException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": "403"}';
        $code = 422;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testNoCreditsExceptionFromParseResponse()
    {
        $this->setExpectedException('LogicException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": "405"}';
        $code = 422;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testInactiveTenantExceptionFromParseResponse()
    {
        $this->setExpectedException('LogicException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": "406"}';
        $code = 422;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testBounceNotFoundExceptionFromParseResponse()
    {
        $this->setExpectedException('RuntimeException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": "407"}';
        $code = 422;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testBadArgumentsBounceExceptionFromParseResponse()
    {
        $this->setExpectedException('LogicException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": "408"}';
        $code = 422;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testJsonRequiredExceptionFromParseResponse()
    {
        $this->setExpectedException('LogicException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": "409"}';
        $code = 422;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testTooManyBatchExceptionFromParseResponse()
    {
        $this->setExpectedException('RuntimeException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": "410"}';
        $code = 422;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testIncorrectFieldExceptionFromParseResponse()
    {
        $this->setExpectedException('UnexpectedValueException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": "666"}';
        $code = 422;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }

    public function testMailNotSentExceptionFromParseResponse()
    {
        $this->setExpectedException('RuntimeException');

        $pm = $this->_getMakeRequestMock();

        $body = '{"Message": "", "ErrorCode": ""}';
        $code = 666;

        $pm->expects($this->once())
            ->method('makeRequest')
            ->will($this->returnValue(new Zend_Http_Response($code, array(), $body)));

        $pm->setClient($this->getHttpClient());
        $pm->send(array());
    }


}
