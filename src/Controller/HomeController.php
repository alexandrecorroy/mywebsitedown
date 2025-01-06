<?php

namespace App\Controller;

use App\Entity\WebLinkTester;
use App\Form\Type\WebLinkTesterType;
use App\Repository\WebLinkTesterRepository;
use App\Service\GetLinkInformationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * class HomeController
 */
final class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, GetLinkInformationService $getLinkInformation, EntityManagerInterface $entityManager, WebLinkTesterRepository $linkRepository): Response
    {
        $webLink = new WebLinkTester();

        $form = $this->createForm(WebLinkTesterType::class, $webLink);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $webLink = $form->getData();

            try {
                $linkInformation = $getLinkInformation->fetchLinkInformation($webLink->getLink());

                $statusCode = $linkInformation['statusCode'];
                $webLink->setStatusCode($statusCode);
                $headers = $linkInformation['headers'];
                $webLink->setHeaders(json_encode($headers));



                $webLink->setSlug(md5($webLink->getLink() . $webLink->getCreatedDate()->getTimestamp()));

                try {
                    $webLink->setContent(substr($linkInformation['content'], 0, WebLinkTester::LENGTH_CONTENT));
                } catch (\Exception $e){
                    // silent is golden
                }

                $entityManager->persist($webLink);
                $entityManager->flush();

                return $this->redirectToRoute('link_show', ['slug' => $webLink->getSlug()]);

            } catch (\Exception $e) {
                $this->addFlash('error', 'Cannot resolve '.$webLink->getLink());
            }
        }

        $last10webLinks = $linkRepository->findBy([], ['createdDate' => 'DESC'], 10);

        return $this->render('front/index.html.twig', [
            'form' => $form,
            'last10webLinks' => $last10webLinks
        ]);

    }
}