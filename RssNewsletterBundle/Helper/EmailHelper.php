<?php

namespace MauticPlugin\RssNewsletterBundle\Helper;

use Mautic\EmailBundle\Model\EmailModel;
use Mautic\EmailBundle\Entity\Email;

class EmailHelper
{
    private $emailModel;

    public function __construct(EmailModel $emailModel)
    {
        $this->emailModel = $emailModel;
    }

    public function sendCustomEmail($to, $subject, $content)
    {
        // Create a new email entity
        $email = new Email();
        $email->setSubject($subject);
        $email->setCustomHtml($content);

        // Send the email
        $this->emailModel->sendEmail(
            $email,
            [
                'to' => $to,
                'from' => ['email' => 'no-reply@yourdomain.com', 'name' => 'Your Company'], // Customize this
            ]
        );
    }
}
