<?php

namespace App\Scheduler;

use App\Scheduler\Message\CheckAndDeleteDailyOldRecordsMessage;
use App\Scheduler\Message\CheckWebLinksEvery300SecondsMessage;
use App\Scheduler\Message\CheckWebLinksEvery3600SecondsMessage;
use App\Scheduler\Message\CheckWebLinksEvery43200SecondsMessage;
use App\Scheduler\Message\CheckWebLinksEvery86400SecondsMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('default')]
final class DefaultScheduleProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(RecurringMessage::cron('*/5 * * * *', new CheckWebLinksEvery300SecondsMessage())) // every 5 minutes
            ->add(RecurringMessage::cron('0 * * * *', new CheckWebLinksEvery3600SecondsMessage())) // every hour
            ->add(RecurringMessage::cron('0 */12 * * *', new CheckWebLinksEvery43200SecondsMessage())) // twice a day
            ->add(RecurringMessage::cron('0 0 * * *', new CheckWebLinksEvery86400SecondsMessage())) // each day
            ->add(RecurringMessage::cron('0 0 * * *', new CheckAndDeleteDailyOldRecordsMessage())); // each day
    }
}
