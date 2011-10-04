<?php
/**
 * EasyBib Copyright 2008-2011
 * Modifying, copying, of code contained herein that is not specifically
 * authorized by Imagine Easy Solutions LLC ("Company") is strictly prohibited.
 * Violators will be prosecuted.
 *
 * This restriction applies to proprietary code developed by EasyBib. Code from
 * third-parties or open source projects may be subject to other licensing
 * restrictions by their respective owners.
 *
 * Additional terms can be found at http://www.easybib.com/company/terms
 *
 * PHP Version 5
 *
 * @category
 * @package
 * @author   Yvan Volochine <yvan.volochine@gmail.com>
 * @license  http://www.easybib.com/company/terms Terms of Service
 * @version  GIT: $Id$
 * @link     http://www.easybib.com
 */

require_once 'Zend/Http/Client.php';

class Services_PostmarkApp
{
    /**
     * @var string $_apiKey
     */
    private $_apiKey;

    /**
     * @var string $_uri
     */
    private $_uri = 'http://api.postmarkapp.com/email';

    /**
     * @var Zend_Mail $_mail
     */
    private $_mail;

    /**
     * @var Zend_Http_Client $_client
     */
    private $_client;


    /**
     * __construct
     *
     * @param mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        $this->_prepareAdapter();
        $this->_mail   = new Zend_Mail();
        $this->_client = $this->getClient();

        if ($options) {
            $this->setOptions($options);
        }
    }

    /**
     * setOptions
     *
     * @param mixed $options
     * @return void
     */
    public function setOptions($options)
    {
        if (! is_object($options) && ! is_array($options)) {
            throw new Exception('Bad options format, should be object or array');
        }

        foreach ($options as $key => $value) {
            switch ($key) {
            case 'apiKey':
                $this->_apiKey = $value;
                break;
            case 'uri':
                $this->_uri = $value;
                break;
            case 'from':
                $this->_mail->setFrom($value);
                break;
            case 'replyTo':
                $this->_mail->setReplyTo($value);
                break;
            case 'to':
                $this->_mail->addTo($value);
                break;
            case 'subject':
                $this->_mail->setSubject($value);
                break;
            case 'bodyText':
                $this->_mail->setBodyText($value);
                break;
            case 'bodyHtml':
                $this->_mail->setBodyHtml($value);
                break;
            }
        }
    }

    /**
     * send
     *
     * @return void
     * @throws Exception if email could not be sent
     */
    public function send()
    {
        try {
            $this->_mail->send();
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * setClient
     *
     * @param Zend_Http_Client $client ''
     * @return void
     * @todo be able to use something else than Zend_Http_Client ??
     */
    public function setClient(Zend_Http_Client $client)
    {
        $this->_client = $client;
    }

    /**
     * getClient
     *
     * @return Zend_Http_Client
     */
    public function getClient()
    {
        if ($this->_client === null) {
            $this->_client = new Zend_Http_Client();
        }
        return $this->_client;
    }

    /**
     * setupClient
     *
     * @param array $postData
     * @return void
     */
    public function setupClient(array $postData = null)
    {
        if (! $postData) {
            throw new Exception('Post data is missing for Http Client.');
        }

        $request = $this->makeRequest(
            $this->_uri, Zend_Http_Client::POST, $postData
        );

        $this->parseResponse($request);
    }

    /**
     * makeRequest
     *
     * @param string $uri ''
     * @param mixed  $method GET or POST
     * @param mixed  $data ''
     *
     * @return void
     */
    protected function makeRequest($uri, $method, $data)
    {
        $this->_client->setUri($uri);
        $this->_client->setMethod($method);
        $this->_client->setHeaders(
            array(
                'Accept' => 'application/json',
                'X-Postmark-Server-Token' => $this->_apiKey
            )
        );
        $this->_client->setRawData(json_encode($data), 'application/json');

        return $this->_client->request();
    }


    /**
     * parseResponse
     *
     * @param Zend_Controller_Http_Response $response
     * @return Zend_Controller_Http_Response
     * @throws RuntimeException if the mail was not sent
     */
    protected function parseResponse($response)
    {
        if ($response->getStatus() != 200) {
            $body = json_decode($response->getBody());
            throw new RuntimeException('Mail not sent: ' . $body->Message);
        }
        return $response;
    }

    /**
     * _prepareAdapter
     *
     * @return void
     */
    private function _prepareAdapter()
    {
        Zend_Mail::setDefaultTransport(
            new Postmark_Mail_Transport_Postmark($this)
        );
    }

}
