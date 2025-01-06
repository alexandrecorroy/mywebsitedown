<?php

namespace App\Service;

use App\Entity\WebLink;
use App\Entity\WebLinkDetail;
use App\Mailer\SendMailOnCronWebLinkScheduleError;
use App\Repository\WebLinkScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;

final class CheckWebLinksCronService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private WebLinkScheduleRepository $webLinkScheduleRepository,
        private GetLinkInformationService $getLinkInformationService,
        private SendMailOnCronWebLinkScheduleError $sendMailOnCronWebLinkScheduleError
    ) {}

    public function executeCronTask($interval)
    {
        $listWebLinksSchedule = $this->webLinkScheduleRepository->findBy([
            'active' => true,
            'cronInSeconds' => $interval
        ]);

        foreach ($listWebLinksSchedule as $webLinksSchedule) {
            if(!$webLinksSchedule->getActive())
                continue;

            $linkInfo = $this->getLinkInformationService->fetchLinkInformation($webLinksSchedule->getLink());

            $webLink = new WebLink();
            $webLink->setWebLinkSchedule($webLinksSchedule);

            $webLink->setStatusCode($linkInfo['statusCode']);

            $this->entityManager->persist($webLink);

            $this->entityManager->flush();

            if(!((int)strval($linkInfo['statusCode'])[0] === $webLinksSchedule->getStatusCodeVerifying()))
            {
                $webLinkDetail = new WebLinkDetail();

                $webLinkDetail->setWebLink($webLink);
                $webLinkDetail->setHeaders(json_encode($linkInfo['headers']));
                $webLinkDetail->setContent($linkInfo['content']);

                $this->entityManager->persist($webLinkDetail);

                $this->entityManager->flush();

                if($webLinksSchedule->getEmailAlert())
                {
                    $webLinksSchedule->setEmailAlert(false);

                    $this->entityManager->persist($webLinksSchedule);

                    $this->entityManager->flush();

                    $this->sendMailOnCronWebLinkScheduleError->sendMail($webLink);
                }

            }
        }
    }

}