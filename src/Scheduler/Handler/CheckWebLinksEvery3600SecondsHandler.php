<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\CheckWebLinksEvery3600SecondsMessage;
use App\Service\CheckWebLinksCronService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CheckWebLinksEvery3600SecondsHandler
{
    public function __construct(
        private CheckWebLinksCronService $checkWebLinksCronService
    ) {}

    public function __invoke(CheckWebLinksEvery3600SecondsMessage $task): void
    {
        $this->checkWebLinksCronService->executeCronTask(3600);
    }
}
