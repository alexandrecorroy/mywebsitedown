<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('json_decode', array($this, 'jsonDecode')),
            new TwigFilter('bootstrap_color', array($this, 'bootstrapColor')),
            new TwigFilter('convert_seconds_to_delay', array($this, 'convertSecondsToDelay')),
        );
    }

    public function jsonDecode($string): array
    {

        return json_decode($string, true);
    }

    public function bootstrapColor(int $statusCode): string
    {
        $firstNumber = substr($statusCode, 0, 1);

        $color = '';
        switch ($firstNumber) {
            case 1:
                $color = 'info';
                break;
            case 2:
                $color = 'success';
                break;
            case 3:
                $color = 'warning';
                break;
            case 4:
                $color = 'primary';
                break;
            case 5:
                $color = 'danger';
                break;
            default:
                $color = 'secondary';
        }

        return $color;
    }

    public function convertSecondsToDelay($seconds): string
    {
        return match ($seconds) {
            300 => 'Five minutes',
            3600 => 'One hour',
            43200 => 'Twice Day',
            86400 => 'One day',
            default => $seconds . ' seconds',
        };

    }
}