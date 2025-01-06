<?php

namespace App\Controller;

use App\Repository\WebLinkTesterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/link')]
final class LinkController extends AbstractController
{
    #[Route('/{slug}', name: 'link_show')]
    public function show(string $slug, WebLinkTesterRepository $webLinkRepository): Response
    {
        $webLink = $webLinkRepository->findOneBy(['slug' => $slug]);

        if (!$webLink) {
            throw $this->createNotFoundException('Link not found');
        }

        return $this->render('front/link.html.twig', [
            'webLink' => $webLink,
        ]);
    }
}