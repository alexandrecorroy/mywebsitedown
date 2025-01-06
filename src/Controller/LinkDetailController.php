<?php

namespace App\Controller;

use App\Entity\WebLinkDetail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard/link_detail')]
final class LinkDetailController extends AbstractController
{
    #[Route('/{id}', name: 'app_dashboard_view_link_detail')]
    #[IsGranted('view', 'webLinkDetail')]
    public function view(
        WebLinkDetail $webLinkDetail,
    ) : Response
    {
        return $this->render('back/view_link_detail.html.twig', [
            'webLinkSchedule' => $webLinkDetail->getWebLink()->getWebLinkSchedule(),
            'webLinkDetail' => $webLinkDetail,
        ]);
    }
}
