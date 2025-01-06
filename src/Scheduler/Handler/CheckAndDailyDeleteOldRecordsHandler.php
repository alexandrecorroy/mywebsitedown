<?php

namespace App\Scheduler\Handler;

use App\Repository\WebLinkRepository;
use App\Repository\WebLinkTesterRepository;
use App\Scheduler\Message\CheckAndDeleteDailyOldRecordsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CheckAndDailyDeleteOldRecordsHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WebLinkRepository $webLinkRepository,
        private WebLinkTesterRepository $webLinkTesterRepository
    ) {}

    public function __invoke(CheckAndDeleteDailyOldRecordsMessage $task): void
    {
        $oldWebLinks = $this->webLinkRepository->findOlderThan7Days();

        foreach ($oldWebLinks as $webLink)
        {
            $this->entityManager->remove($webLink);
        }

        $oldWebLinksTester = $this->webLinkTesterRepository->findOlderThan10Days();

        foreach ($oldWebLinksTester as $webLinkTester)
        {
            $this->entityManager->remove($webLinkTester);
        }

        $this->entityManager->flush();
    }
}
