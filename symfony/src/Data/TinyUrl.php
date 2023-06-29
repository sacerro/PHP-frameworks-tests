<?php

namespace App\Data;


use Throwable;
use Symfony\Component\HttpClient\HttpClient;

class TinyUrl
{
    public static function shortUrl(string $url): string
    {
        try {
            $client = HttpClient::create();
            $response = $client->request(
                'GET',
                sprintf('https://tinyurl.com/api-create.php?url=%s', $url)
            );
            return $response->getContent();
        } catch (Throwable $e) {
            return 'Error';
        }
    }
}
