<?php

declare(strict_types=1);

namespace App\Controller;

use App\Chart\Last7DaysWebLinkLineByForAllScheduleChart;
use App\Entity\User;
use App\Repository\WebLinkScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class DashboardController extends AbstractController
{
  #[Route('/dashboard')]
  public function dashboard(
      WebLinkScheduleRepository $webLinkScheduleRepository,
      Last7DaysWebLinkLineByForAllScheduleChart $last7DaysWebLinkLineByForAllScheduleChart,
      #[CurrentUser] ?User $user
  ): Response
  {

    $weblinksSchedule = $webLinkScheduleRepository->findBy(['user' => $user]);

    return $this->render('back/dashboard.html.twig', [
        'last7DaysWebLinkLineByForAllScheduleChart' => $last7DaysWebLinkLineByForAllScheduleChart->render($user),
        'weblinksSchedule' => $weblinksSchedule
    ]);
  }
}
