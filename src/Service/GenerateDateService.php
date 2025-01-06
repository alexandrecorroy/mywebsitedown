<?php

namespace App\Service;

final class GenerateDateService
{
    public function generateLast30Days(): array
    {
        $dates = [];

        $endDate = new \DateTime();
        for ($i = 0; $i < 30; $i++) {
            $date = clone $endDate;
            $date->modify("-$i days");
            $formattedDate = $date->format('d/m');
            $dates[] = $formattedDate;
        }

        return $dates;
    }

    public function generateLast7Days(): array
    {
        $dates = [];

        $endDate = new \DateTime();
        for ($i = 0; $i < 7; $i++) {
            $date = clone $endDate;
            $date->modify("-$i days");
            $formattedDate = $date->format('d/m');
            $dates[] = $formattedDate;
        }

        return $dates;
    }

    public function generateLast24Hours(): array
    {
        $dates = [];

        $endDate = new \DateTime();
        for ($i = 0; $i < 24; $i++) {
            $date = clone $endDate;
            $date->modify("-$i hours");
            $formattedDate = $date->format('H');
            $dates[] = $formattedDate.'h';
        }

        return $dates;
    }

}