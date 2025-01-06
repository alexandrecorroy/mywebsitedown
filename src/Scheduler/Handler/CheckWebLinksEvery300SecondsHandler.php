<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\CheckWebLinksEvery300SecondsMessage;
use App\Service\CheckWebLinksCronService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CheckWebLinksEvery300SecondsHandler
{
    public function __construct(
        private CheckWebLinksCronService $checkWebLinksCronService
    ) {}

    public function __invoke(CheckWebLinksEvery300SecondsMessage $task): void
    {
        $this->checkWebLinksCronService->executeCronTask(300);
    }
}
