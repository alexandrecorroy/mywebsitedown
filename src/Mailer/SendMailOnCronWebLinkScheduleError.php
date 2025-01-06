<?php

namespace App\Mailer;

use App\Entity\WebLink;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final class SendMailOnCronWebLinkScheduleError
{

    public function __construct(
        private MailerInterface $mailer
    )
    { }

    public function sendMail(WebLink $webLink)
    {
        $email = (new TemplatedEmail())
            ->from('andi@galaxiemedia.fr')
            ->to(new Address($webLink->getWebLinkSchedule()->getUser()->getEmail()))
            ->subject('New Alert for '.$webLink->getWebLinkSchedule()->getName())

            // path of the Twig template to render
            ->htmlTemplate('email/webLink_cron_alert.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'webLink' => $webLink
            ])
        ;

        $this->mailer->send($email);
    }
}