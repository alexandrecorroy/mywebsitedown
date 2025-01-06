<?php

namespace App\Mailer;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class ContactMailer
{

    private string $email_admin;

    public function __construct(
        private MailerInterface $mailer,
        #[Autowire('%email_admin%')] string $email_admin
    ) {
        $this->email_admin = $email_admin;
    }

    public function send($emailAddress, $message)
    {
        $email = (new Email())
            ->from($emailAddress)
            ->to($this->email_admin)
            ->subject('You have a new message!')
            ->text($message);

        $this->mailer->send($email);
    }

}