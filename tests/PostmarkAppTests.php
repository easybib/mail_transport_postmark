<?php

require_once 'Postmark/Mail/Transport/Postmark.php';
require_once 'Services/PostmarkApp.php';
require_once 'Zend/Http/Client/Adapter/Test.php';


class Services_PostmarkApp_TestCase extends PHPUnit_Framework_TestCase
{

    private $_apiKey = 'c56ceeff-0661-4cd9-a85d-a0e739c1f71f';
    private $_from   = 'test postmark <easybib_postmark@mailinator.com';


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

        $pm = $this->getMock(
            'Services_PostmarkApp',
            array('makeRequest',),
            array($this->_apiKey)
        );

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
}
