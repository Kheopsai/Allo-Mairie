<?php

namespace App\Services\Scrapper;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;

class Scrapper
{
    public Client $client;

    public string $url;

    public function __construct(string $url)
    {
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Referer' => 'https://duckduckgo.com/',
                'Upgrade-Insecure-Requests' => '1',
            ],
            'cookies' => new CookieJar,
            'delay' => 2000,
            'allow_redirects' => true,
        ]);
        $this->url = $url;
    }

    /**
     * @throws GuzzleException
     */
    public function handle(): string|bool
    {
        try {
            $response = $this->client->request('GET', $this->url, [
                'form_params' => [
                    'q' => $this->extractQueryFromUrl(),
                    'dc' => 10,
                ],
            ]);

            return $response->getStatusCode() === 200
                ? (string) $response->getBody()
                : false;

        } catch (Exception $exception) {
            return false;
        }
    }

    private function extractQueryFromUrl(): string
    {
        parse_str(parse_url($this->url, PHP_URL_QUERY), $params);

        return $params['q'] ?? '';
    }
}
