<?php

namespace App\Controller;

use App\Chart\Last7DaysWebLinkDonutsChart;
use App\Chart\Last7DaysWebLinkLineChart;
use App\Entity\User;
use App\Entity\WebLinkSchedule;
use App\Form\Type\WebLinkScheduleType;
use App\Repository\WebLinkRepository;
use App\Service\GetLinkInformationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard/link_schedule')]
final class LinkScheduleController extends AbstractController
{
    #[Route('/add', name: 'app_dashboard_add_link')]
    public function add(
        Request $request,
        GetLinkInformationService $getLinkInformation,
        EntityManagerInterface $entityManager,
        #[CurrentUser] ?User $user
    ): Response
    {
        $webLinkSchedule = new WebLinkSchedule();

        $form = $this->createForm(WebLinkScheduleType::class, $webLinkSchedule);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $webLinkSchedule = $form->getData();

            if(sizeof($user->getWebLinkSchedules()) >= 10)
            {
                $this->addFlash('notice', [
                    'alert' => 'danger',
                    'message' => 'You have exceeded the number of sites to monitor. (Max 10)'
                ]);

                return $this->redirectToRoute('app_dashboard_dashboard');
            }

            try {

                $getLinkInformation->fetchLinkInformation($webLinkSchedule->getLink());

                $webLinkSchedule->setUser($user);

                $webLinkSchedule->setActive(true);

                $entityManager->persist($webLinkSchedule);
                $entityManager->flush();

                $this->addFlash('notice', [
                    'alert' => 'success',
                    'message' => 'New monitoring link added'
                ]);

                return $this->redirectToRoute('app_dashboard_dashboard');

            } catch (\Exception $e) {
                $this->addFlash('notice', [
                    'alert' => 'danger',
                    'message' => 'Cannot resolve '.$webLinkSchedule->getLink()
                ]);
            }
        }

        return $this->render('back/add_link.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route('/{id}', name: 'app_dashboard_view_link')]
    #[IsGranted('view', 'webLinkSchedule')]
    public function view(
        WebLinkSchedule $webLinkSchedule,
        Last7DaysWebLinkLineChart $last7DaysWebLinkLineChart,
        Last7DaysWebLinkDonutsChart $last7DaysWebLinkDonutsChart,
        WebLinkRepository $webLinkRepository
    ) : Response
    {
        return $this->render('back/view_link.html.twig', [
            'webLinkSchedule' => $webLinkSchedule,
            'last7DaysWebLinkLineChart' => $last7DaysWebLinkLineChart->render($webLinkSchedule),
            'last7DaysWebLinkDonutsChart' => $last7DaysWebLinkDonutsChart->render($webLinkSchedule),
            'last10LinkToFetch' => $webLinkRepository->findLast10LinkToFetchWithStatusCode($webLinkSchedule)
        ]);
    }

    #[Route('/{id}/toggle_monitoring', name: 'app_dashboard_toggle_monitoring_link')]
    #[IsGranted('edit', 'webLinkSchedule')]
    public function toggleMonitoring(
        WebLinkSchedule $webLinkSchedule, EntityManagerInterface $entityManager
    ) : Response
    {

        if($webLinkSchedule->getActive())
        {
            $webLinkSchedule->setActive(false);
        } else {
            $webLinkSchedule->setActive(true);
        }

        $entityManager->persist($webLinkSchedule);
        $entityManager->flush();

        $activeStatus = $webLinkSchedule->getActive() ? 'enabled' : 'disabled';

        $this->addFlash(
            'notice',
            [
                'alert' => 'info',
                'message' => 'Monitoring for '.$webLinkSchedule->getName().' has been correctly '.$activeStatus
            ]
        );


        return $this->redirectToRoute('app_dashboard_view_link', ['id' => $webLinkSchedule->getId()]);
    }

    #[Route('/{id}/toggle_email_alert', name: 'app_dashboard_toggle_email_alert_link')]
    #[IsGranted('edit', 'webLinkSchedule')]
    public function toggleEmailAlert(
        WebLinkSchedule $webLinkSchedule, EntityManagerInterface $entityManager
    ) : Response
    {

        if($webLinkSchedule->getEmailAlert())
        {
            $webLinkSchedule->setEmailAlert(false);
        } else {
            $webLinkSchedule->setEmailAlert(true);
        }

        $entityManager->persist($webLinkSchedule);
        $entityManager->flush();

        $emailStatus = $webLinkSchedule->getEmailAlert() ? 'enabled' : 'disabled';

        $this->addFlash(
            'notice',
            [
                'alert' => 'info',
                'message' => 'Email alerts has been correctly '.$emailStatus
            ]
        );

        return $this->redirectToRoute('app_dashboard_view_link', ['id' => $webLinkSchedule->getId()]);
    }

    #[Route('/ajax/{id}', name: 'show_all_ajax_by_schedule')]
    public function returnAllByScheduleInAjax(WebLinkSchedule $webLinkSchedule, WebLinkRepository $webLinkRepository): JsonResponse
    {
        $datas = $webLinkRepository->findBy(['webLinkSchedule' => $webLinkSchedule->getId()], ['id' => 'DESC']);

        $array = [];

        $i = 0;
        foreach ($datas as $data)
        {
            $array['data'][$i] = [
                'id' => $data->getId(),
                'status_code' => $data->getStatusCode(),
                'date' => $data->getDateVisited()->format('Y/m/d  H:i'),
            ];

            $i++;
        }

        return new JsonResponse($array);
    }

    #[Route('/delete/{id}', name: 'app_dashboard_delete_link')]
    #[IsGranted('delete', 'webLinkSchedule')]
    public function deleteClient(WebLinkSchedule $webLinkSchedule, EntityManagerInterface $entityManager)
    {
        $webLinkScheduleName = $webLinkSchedule->getName();

        $entityManager->remove($webLinkSchedule);

        $entityManager->flush();

        $this->addFlash(
            'notice',
             [
                 'alert' => 'success',
                 'message' => 'We have delete everything about '.$webLinkScheduleName
             ]
        );

        return $this->redirectToRoute('app_dashboard_dashboard');
    }
}