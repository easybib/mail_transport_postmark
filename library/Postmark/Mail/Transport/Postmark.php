<?php
/**
 * Postmark Mail Transport Class for Zend_Mail
 *
 * Copyright 2010, Alistair Phillips, http://the.0gravity.co.uk/universe/php/zend/mail_transport_postmark/
 *
 * @author Alistair Phillips (alistair@0gravity.co.uk)
 * @copyright Copyright 2010, Alistair Phillips
 * @version 0.3
 *
 */

require_once 'Zend/Mime/Decode.php';

class Postmark_Mail_Transport_Postmark extends Zend_Mail_Transport_Abstract
{
    /**
     * @var Services_PostmarkApp $_postmark
     */
    private $_postmark;

    /**
     * __construct
     *
     * @param Services_PostmarkApp $postmark
     * @return void
     */
    public function __construct(Services_PostmarkApp $postmark)
    {
        $this->_postmark = $postmark;
    }

    /**
     * _sendMail
     *
     * @return void
     * @uses Services_PostmarkApp::prepareClient()
     * @todo Write a test to cover special characters in subject (to confirm imap_utf8() is not necessary)
     */
    public function _sendMail()
    {
        // Retrieve the headers and appropriate keys we need to construct our mail
        $headers = $this->_mail->getHeaders();

        $to = array();
        if (array_key_exists('To', $headers)) {
            foreach($headers['To'] as $key => $val) {
                if(empty($key) || $key != 'append') {
                    $to[] = $val;
                }
            }
        }

        $cc = array();
        if (array_key_exists('Cc', $headers)) {
            foreach($headers['Cc'] as $key => $val) {
                if(empty($key) || $key != 'append') {
                    $cc[] = $val;
                }
            }
        }

        $bcc = array();
        if (array_key_exists('Bcc', $headers)) {
            foreach($headers['Bcc'] as $key => $val ) {
                if(empty($key) || $key != 'append') {
                    $bcc[] = $val;
                }
            }
        }

        $from = array();
        if (array_key_exists('From', $headers)) {
            foreach($headers['From'] as $key => $val) {
                if(empty($key) || $key != 'append') {
                    $from[] = $val;
                }
            }
        }

        $replyto = array();
        if (array_key_exists('Reply-To', $headers)) {
            foreach($headers['Reply-To'] as $key => $val) {
                if(empty($key) || $key != 'append') {
                    $replyto[] = $val;
                }
            }
        }

        $tags = array();
        if (array_key_exists('postmark-tag', $headers)) {
            foreach ($headers['postmark-tag'] as $key => $val) {
                if (empty($key) || $key != 'append') {
                    $tags[] = $val;
                }
            }
        }

        $postData = array(
            'From'     => implode( ',', $from ),
            'To'       => implode( ',', $to ),
            'Cc'       => implode( ',', $cc ),
            'Bcc'      => implode( ',', $bcc),
            'Subject'  => $this->_mail->getSubject(),
            'ReplyTo'  => implode( ',', $replyto ),
            'tag'      => implode(',', $tags)
        );

        // We first check if the relevant content exists (returned as a Zend_Mime_Part)
        if ($this->_mail->getBodyText()) {
            $part = $this->_mail->getBodyText();
            $part->encoding = false;
            $postData['TextBody'] = $part->getContent();
        }

        if ($this->_mail->getBodyHtml()) {
            $part = $this->_mail->getBodyHtml();
            $part->encoding = false;
            $postData['HtmlBody'] = $part->getContent();
        }

        if($this->_mail->hasAttachments){
            $attachments = array();
            $parts = $this->_mail->getParts();
            if(is_array($parts)){
                $i = 0;
                foreach($parts as $part){
                    $attachments[$i]['ContentType'] = $part->type;
                    $attachments[$i]['Name']        = $part->filename;
                    $attachments[$i]['Content']     = $part->getContent();
                    $i++;
                }
            }
            $postData['Attachments'] = $attachments;
        }

        $this->_postmark->setupClient($postData);
    }

}
