<?php

namespace App\Mailer;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final class CreateUserMailer
{

    private string $email_admin;

    public function __construct(
        private MailerInterface $mailer,
        #[Autowire('%email_admin%')] string $email_admin
    )
    {
        $this->email_admin = $email_admin;
    }

    public function sendMail(User $user)
    {
        $email = (new TemplatedEmail())
            ->from($this->email_admin)
            ->to(new Address($user->getEmail()))
            ->subject('Welcome to MyWebSiteDown.com')

            // path of the Twig template to render
            ->htmlTemplate('email/create.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'login' => $user->getEmail(),
                'token' => $user->getTokenVerifiy(),
            ])
        ;

        $this->mailer->send($email);

    }

}