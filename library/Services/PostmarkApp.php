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
     * @var Zend_Http_Client $_client
     */
    private $_client;


    /**
     * __construct
     *
     * @param mixed $options
     * @return void
     */
    public function __construct($apiKey = null)
    {
        if (! $apiKey) {
            throw new Exception('no api key given');
        }
        $this->_apiKey = $apiKey;
        $this->_client = $this->getClient();
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
     * send
     *
     * @param array $postData
     * @return void
     */
    public function send(array $postData)
    {
        try {
            $request = $this->makeRequest(
                $this->_uri, Zend_Http_Client::POST, $postData
            );

            $response = $this->parseResponse($request);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
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
     * Returns JSON from the response, and tries to determine if there's an error
     * and supply meaningful exceptions in return.
     *
     * If all goes well, we return the body (stdClass).
     *
     * @param Zend_Controller_Http_Response $response
     *
     * @return stdClass
     *
     * @throws RuntimeException         If the mail was not sent
     * @throws UnexpectedValueException If Postmark couldn't handle the payload.
     */
    protected function parseResponse($response)
    {
        $status = $response->getStatus();
        $body   = json_decode($response->getBody());

        if ($body === false) {
            throw new RuntimeException("Failed to parse response from Postmark.");
        }

        if ($status != 200) {

            switch ($status) {
            case 401:
                throw new ErrorException("Not authorized. (see Postmark credentials)", $status);
            case 422:
                switch ($body->ErrorCode) {
                case 0:
                    throw new LogicException("Missing or invalid API-Token.", $status);
                case 300:
                    throw new LogicException("Validation failed for the email request JSON data that you provided.", $status);
                case 400:
                    throw new LogicException("Sender signature not found.", $status);
                case 401:
                    throw new LogicException("Sender signature not confirmed.", $status);
                case 402:
                    throw new LogicException("Invalid JSON.", $status);
                case 403:
                    throw new LogicException("Incompatible JSON.", $status);
                case 405:
                    throw new LogicException("No credits.", $status);
                case 406:
                    throw new LogicException("Inactive tenant.", $status);
                case 407:
                    throw new RuntimeException("Bounce not found.", $status);
                case 408:
                    throw new LogicException("You provided bad arguments as a bounces filter.", $status);
                case 409:
                    throw new LogicException("JSON required (Content-Type/Accept header)", $status);
                case 410:
                    throw new RuntimeException("Too many batch messages.", $status);
                default:
                    throw new UnexpectedValueException("Incorrect fields, or json.", $status);
                }
                break;
            case 500:
                throw new RuntimeException("Postmark error", $status);
            default:
                throw new RuntimeException('Mail not sent: ' . $body->Message, $status);
            }
        }

        return $json;
    }

}
