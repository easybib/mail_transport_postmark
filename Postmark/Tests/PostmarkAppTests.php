<?php

class Services_PostmarkApp_TestCase extends PHPUnit_Framework_TestCase
{

    public function testWrongSignatureThrowsException()
    {
        $this->setExpectedException('Exception');

        $options = new stdClass();
        $options->apiKey   = 'a-Wrong-Key';
        $options->from     = 'yvan volochine <contact@anymail.com>';
        $options->to       = 'egr <egr@anymail.com>';
        $options->subject  = 'hola huevon !!!';
        $options->bodyText = 'salut bonjour buenos dias';

        $postmark = new Postmark_Services_PostmarkApp();
        $postmark->setOptions($options);
        $postmark->send();
    }

}
