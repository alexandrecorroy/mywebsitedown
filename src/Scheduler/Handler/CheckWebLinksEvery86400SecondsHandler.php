<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\CheckWebLinksEvery86400SecondsMessage;
use App\Service\CheckWebLinksCronService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CheckWebLinksEvery86400SecondsHandler
{
    public function __construct(
        private CheckWebLinksCronService $checkWebLinksCronService
    ) {}

    public function __invoke(CheckWebLinksEvery86400SecondsMessage $task): void
    {
        $this->checkWebLinksCronService->executeCronTask(86400);
    }
}
