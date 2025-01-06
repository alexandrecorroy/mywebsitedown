<?php

namespace App\Chart;

use App\Entity\User;
use App\Repository\WebLinkRepository;
use App\Service\GenerateColorService;
use App\Service\GenerateDateService;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class Last7DaysWebLinkLineByForAllScheduleChart
{
    public function __construct(
        private ChartBuilderInterface $chartBuilder,
        private WebLinkRepository $webLinkRepository,
        private GenerateDateService $generateDateService,
        private GenerateColorService $colorService
    )
    {
    }

    public function render(User $user)
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $dates = $this->generateDateService->generateLast7Days();
        $datasets = array();
        foreach ($user->getWebLinkSchedules() as $key => $webLinkSchedule)
        {
            $reports = $this->webLinkRepository->findLast7DaysValuesForEachDayByWebLinkSchedule($webLinkSchedule);

            $color = $this->colorService->getColorForNumber($key);

            foreach ($reports as $statusCode => $report)
            {

                $datasets[] = [
                    'label' => $webLinkSchedule->getName().' ('.$statusCode.')',
                    'data' => $report,
                    'tension' => 0.1,
                    'backgroundColor' => $color['backgroundColor'],
                    'borderColor' => $color['borderColor'],
                ];

            }

        }

        $chart->setData([
            'labels' => array_reverse($dates),
            'datasets' => $datasets
        ]);

        $chart->setOptions([
            'maintainAspectRatio' => false,
            'responsive' => true,
            'scales' => [
                'y' => [
                    'beginAtZero' => true
                ]
            ],
        ]);

        return $chart;

    }

}