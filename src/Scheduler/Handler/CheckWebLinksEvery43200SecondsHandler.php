<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\CheckWebLinksEvery43200SecondsMessage;
use App\Service\CheckWebLinksCronService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CheckWebLinksEvery43200SecondsHandler
{
    public function __construct(
        private CheckWebLinksCronService $checkWebLinksCronService
    ) {}

    public function __invoke(CheckWebLinksEvery43200SecondsMessage $task): void
    {
        $this->checkWebLinksCronService->executeCronTask(43200);
    }
}
