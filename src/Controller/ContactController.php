<?php

namespace App\Controller;

use App\Form\Type\ContactType;
use App\Mailer\ContactMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * class ContactController
 */
final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, ContactMailer $mailer): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $mailer->send($data['email'], $data['message']);

                $this->addFlash('success', 'Your email has been sent!');

                return $this->redirectToRoute('contact');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Cannot send email : '.$e->getMessage());
            }
        }

        return $this->render('front/contact.html.twig', [
            'form' => $form,
        ]);

    }
}