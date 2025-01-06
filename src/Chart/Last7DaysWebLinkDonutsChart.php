<?php

namespace App\Chart;

use App\Entity\WebLinkSchedule;
use App\Repository\WebLinkRepository;
use App\Service\GenerateColorService;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class Last7DaysWebLinkDonutsChart
{
    public function __construct(
        private ChartBuilderInterface $chartBuilder,
        private WebLinkRepository $webLinkRepository,
        private GenerateColorService $colorService
    )
    {
    }

    public function render(WebLinkSchedule $webLinkSchedule)
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);

        $reports = $this->webLinkRepository->findLast7DaysValuesByWebLinkSchedule($webLinkSchedule);

        $labels = [];
        $backgroundColors = [];
        $datas = [];
        foreach ($reports as $key => $report)
        {

            $color = $this->colorService->getStatusCodeColor($key);

            $labels[] = $key;
            $backgroundColors[] = $color['backgroundColor'];
            $datas[] = $report;

        }

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Clics',
                    'data' => $datas,
                    'backgroundColor' => $backgroundColors,
                    'hoverOffset' => 4
                ],
            ]
        ]);

        $chart->setOptions([
            'maintainAspectRatio' => false,
            'responsive' => true
        ]);

        return $chart;

    }

}