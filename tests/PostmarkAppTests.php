<?php

require_once 'Postmark/Mail/Transport/Postmark.php';
require_once 'Services/PostmarkApp.php';


class Services_PostmarkApp_TestCase extends PHPUnit_Framework_TestCase
{

    private $_apiKey = 'c56ceeff-0661-4cd9-a85d-a0e739c1f71f';
    private $_from   = 'test postmark <easybib_postmark@mailinator.com';


    public function testWrongSignatureThrowsException()
    {
        $this->setExpectedException('Exception');

        $options = new stdClass();
        $options->apiKey   = 'a-Wrong-Key';
        $options->from     = 'yvan volochine <contact@anymail.com>';
        $options->to       = 'egr <egr@anymail.com>';
        $options->subject  = 'hola huevon !!!';
        $options->bodyText = 'salut bonjour buenos dias';

        $postmark = new Services_PostmarkApp();
        $postmark->setOptions($options);
        $postmark->send();
    }


    public function testMissingOptionsThrowsException()
    {
        $this->setExpectedException('Zend_Mail_Transport_Exception');

        $postmark = new Services_PostmarkApp();
        $postmark->send();
    }


    public function testMissingFromThrowsException()
    {
        $this->setExpectedException('RuntimeException');

        $options = new stdClass();
        $options->apiKey   = $this->_apiKey;
        $options->to       = 'egr <any@mail.com>';
        $options->subject  = 'hola huevon !!!';
        $options->bodyText = 'salut bonjour buenos dias';

        $postmark = new Services_PostmarkApp();
        $postmark->setOptions($options);
        $postmark->send();
    }


    public function testMissingToThrowsException()
    {
        $this->setExpectedException('RuntimeException');

        $options = new stdClass();
        $options->apiKey   = $this->_apiKey;
        $options->from     = $this->_from;
        $options->subject  = 'hola huevon !!!';
        $options->bodyText = 'salut bonjour buenos dias';

        $postmark = new Services_PostmarkApp();
        $postmark->setOptions($options);
        $postmark->send();
    }


    public function testSetGetClient()
    {
        $pm = new Services_PostmarkApp();
        $pm->setClient(new Zend_Http_Client);
        $client = $pm->getClient();
        $this->assertInstanceOf('Zend_Http_Client', $client);
    }
}
