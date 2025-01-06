<?php

namespace App\Chart;

use App\Entity\WebLinkSchedule;
use App\Repository\WebLinkRepository;
use App\Service\GenerateColorService;
use App\Service\GenerateDateService;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class Last7DaysWebLinkLineChart
{
    public function __construct(
        private ChartBuilderInterface $chartBuilder,
        private WebLinkRepository $webLinkRepository,
        private GenerateDateService $generateDateService,
        private GenerateColorService $colorService
    )
    {
    }

    public function render(WebLinkSchedule $webLinkSchedule)
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $dates = $this->generateDateService->generateLast7Days();

        $reports = $this->webLinkRepository->findLast7DaysValuesForEachDayByWebLinkSchedule($webLinkSchedule);

        $datasets = array();
        foreach ($reports as $key => $report)
        {

            $color = $this->colorService->getStatusCodeColor($key);

            $datasets[] = [
                'label' => $key,
                'data' => $report,
                'tension' => 0.1,
                'backgroundColor' => $color['backgroundColor'],
                'borderColor' => $color['borderColor'],
            ];

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