<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GetLinkInformationService
{
    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    public function fetchLinkInformation(string $link): array
    {
        try {
            $response = $this->client->request(
                'GET',
                $link,
                [
                    'max_redirects' => 0,
                    'timeout' => 10, // Timeout pour éviter des blocages
                ]
            );

            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false); // `false` empêche les exceptions pour les erreurs HTTP
            $headers = $response->getHeaders(false); // `false` pour obtenir tous les headers bruts

            return [
                'statusCode' => $statusCode,
                'content' => $content,
                'headers' => $headers,
            ];
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            // Gestion des erreurs HTTP (4xx, 3xx, ou 5xx)
            return [
                'statusCode' => $e->getCode(),
                'content' => $e->getMessage(),
                'headers' => [],
            ];
        } catch (TransportExceptionInterface $e) {
            // Gestion des erreurs réseau ou autres
            return [
                'statusCode' => 0,
                'content' => 'Transport error: ' . $e->getMessage(),
                'headers' => [],
            ];
        }
    }
}
