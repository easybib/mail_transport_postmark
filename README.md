Postmarkapp is a "email delivery in the cloud" service for sending out transactional emails in your applications. By using this service you don't need to worry about the setup and maintenance of a mailserver and as Postmarkapp is built to scale it can easily deal with large bursts of messages. Whilst an easy-to-use API is available this Zend_Mail transport allows you to easily switch out and use Postmark for mail delivery.

Usage
-----
    $options = array(
        'From'     => 'Alistair Phillips <your-email-address>',
        'To'       => 'Alistair Phillips <some-other-address>',
        'Subject'  => 'Test',
        'TextBody' => 'hello there',
        'HtmlBody' => '<b>hello</b>'
    );

    $postmark = new Postmark_Services_PostmarkApp('Your API Key');

    if ($postmark->send($options)) {
        $message = 'your email was sent successfully';
    };

Batching messages
-----------------
    $another =  array(
        'From'     => 'Alistair Phillips <your-email-address>',
        'To'       => 'Any Body <some-other-address>',
        'Subject'  => 'Test',
        'TextBody' => 'hello there',
        'HtmlBody' => '<b>hello</b>'
    );

    $postmark = new Postmark_Services_PostmarkApp('Your API Key');

    $recipients = array($options, $another);

    if ($postmark->sendBatch($recipients)) {
        $message = 'batching emails was successful';
    };

Using Zend_Mail
---------------
    $postmark  = new Services_PostmarkApp('Your API Key');
    $transport = new Postmark_Mail_Transport_Postmark($postmark)

    Zend_Mail::setDefaultTransport($transport);

    $mail = new Zend_Mail();
    $mail->setFrom('your-email-address', 'Alistair Phillips');
    $mail->setReplyTo('some-other-address', 'Alistair Phillips');
    $mail->addTo('an-email-address', 'Joe Smith' );
    $mail->setSubject( 'Welcome to...' );
    $mail->setBodyText( 'This is an example of a text body' );
    $mail->setBodyHtml( 'This is an example of an HTML body with <b>bold</b>' );

    $mail->send();

One call to ```setDefaultTransport()``` and you're now using the cloud for delivery. Added support for attachments to the class.

Alternatively, you can use our application resource.
In your application/config/application.ini, add these lines:

    ; This makes sure that your Zend Framework application can find the correct
    ; resource file. Make sure, the Postmark folder is within your library folder.
    pluginPaths.Postmark_Application_Resource = "Postmark/Application/Resource"

    ; This initializes the Postmark drop-in replacement.
    resources.postmark.apikey = "your_api_key"

Now you're good to go and don't have to manually do anything else.
