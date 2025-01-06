<?php

namespace App\Mailer;

use App\Entity\WebLink;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final class SendMailOnCronWebLinkScheduleError
{
    private string $email_admin;

    public function __construct(
        private MailerInterface $mailer,
        #[Autowire('%email_admin%')] string $email_admin
    )
    {
        $this->email_admin = $email_admin;
    }

    public function sendMail(WebLink $webLink)
    {
        $email = (new TemplatedEmail())
            ->from($this->email_admin)
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