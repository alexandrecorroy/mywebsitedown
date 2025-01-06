<?php

namespace App\Service;

final class GenerateColorService
{

    function getStatusCodeColor(int $statusCode): array
    {
        // Dégradé de couleurs pour chaque groupe de status codes

        if ($statusCode >= 100 && $statusCode < 200) {
            // 1xx - Informational (Bleu clair)
            $backgroundColor = 'rgb(' . (173 + $statusCode - 100) . ', ' . (216 - ($statusCode - 100) * 2) . ', 230)';
            $borderColor = 'rgb(' . (135 + $statusCode - 100) . ', ' . (206 - ($statusCode - 100) * 2) . ', 235)';
        } elseif ($statusCode >= 200 && $statusCode < 300) {
            // 2xx - Success (Vert)
            $backgroundColor = 'rgb(' . (60 + ($statusCode - 200) * 10) . ', ' . (179 - ($statusCode - 200) * 3) . ', ' . (113 - ($statusCode - 200) * 2) . ')';
            $borderColor = 'rgb(' . (50 + ($statusCode - 200) * 10) . ', ' . (160 - ($statusCode - 200) * 3) . ', ' . (80 - ($statusCode - 200)) . ')';
        } elseif ($statusCode >= 300 && $statusCode < 400) {
            // 3xx - Redirection (Orange)
            $backgroundColor = 'rgb(' . (255 - ($statusCode - 300) * 10) . ', ' . (165 - ($statusCode - 300) * 5) . ', 0)';
            $borderColor = 'rgb(' . (255 - ($statusCode - 300) * 10) . ', ' . (140 - ($statusCode - 300) * 5) . ', 0)';
        } elseif ($statusCode >= 400 && $statusCode < 500) {
            // 4xx - Client Error (Rouge)
            $backgroundColor = 'rgb(' . (255 - ($statusCode - 400) * 10) . ', ' . (99 + ($statusCode - 400) * 3) . ', 132)';
            $borderColor = 'rgb(' . (255 - ($statusCode - 400) * 10) . ', ' . (70 + ($statusCode - 400) * 3) . ', 58)';
        } elseif ($statusCode >= 500 && $statusCode < 600) {
            // 5xx - Server Error (Bleu foncé ou bleu clair)
            $backgroundColor = 'rgb(' . (0 + ($statusCode - 500) * 10) . ', ' . (153 - ($statusCode - 500) * 5) . ', 255)';
            $borderColor = 'rgb(' . (0 + ($statusCode - 500) * 10) . ', ' . (120 - ($statusCode - 500) * 5) . ', 200)';
        } else {
            // Code de statut inconnu
            $backgroundColor = 'rgb(169, 169, 169)';
            $borderColor = 'rgb(169, 169, 169)';
        }

        return [
            'backgroundColor' => $backgroundColor,
            'borderColor' => $borderColor
        ];

    }

    public function getColorForNumber($number) {
        // Tableau des couleurs prédéfinies
        $colors = [
            0 => ['rgb(54, 162, 235)'],  // Bleu
            1 => ['rgb(255, 206, 86)'],  // Jaune
            2 => ['rgb(169, 169, 169)'], // Gris
            3 => ['rgb(75, 192, 192)'],  // Turquoise
            4 => ['rgb(255, 99, 132)'],  // Rouge
            5 => ['rgb(153, 102, 255)'], // Violet
            6 => ['rgb(255, 159, 64)'],  // Orange
            7 => ['rgb(201, 203, 207)'], // Gris clair
            8 => ['rgb(0, 128, 0)'],     // Vert foncé
            9 => ['rgb(128, 0, 128)'],   // Violet foncé
            10 => ['rgb(0, 0, 255)'],    // Bleu foncé
        ];

        // Couleur par défaut (Vert clair)
        $defaultColor = 'rgb(144, 238, 144)'; // LimeGreen

        // Sélectionne la couleur pour le numéro donné ou utilise la couleur par défaut
        $backgroundColor = $colors[$number][0] ?? $defaultColor;

        // Calcul du borderColor en fonçant légèrement le backgroundColor
        $borderColor = $this->darkenColor($backgroundColor, 0.3);

        return ['backgroundColor' => $backgroundColor, 'borderColor' => $borderColor];
    }

    public function darkenColor($rgb, $percentage) {
        // Extraire les valeurs RGB de la chaîne
        preg_match('/rgb\((\d+), (\d+), (\d+)\)/', $rgb, $matches);
        $r = max(0, (int)($matches[1] * (1 - $percentage)));
        $g = max(0, (int)($matches[2] * (1 - $percentage)));
        $b = max(0, (int)($matches[3] * (1 - $percentage)));

        // Retourner la couleur modifiée
        return "rgb($r, $g, $b)";
    }


}