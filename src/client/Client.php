<?php

namespace native\thinkphp\client;

use native\thinkphp\facade\Http;
use yzh52521\EasyHttp\Request;
use yzh52521\EasyHttp\Response;

class Client
{
    protected Request $client;

    public function __construct()
    {
        $this->client = Http::asJson()
            ->baseUrl(config('nativephp-internal.api_url', ''))
            ->timeout(60 * 60)
            ->withHeaders([
                'X-NativePHP-Secret' => config('nativephp-internal.secret'),
            ])
            ->asJson();
    }

    public function get(string $endpoint, array|string|null $query = null): Response
    {
        return $this->client->get($endpoint, $query);
    }

    public function post(string $endpoint, array $data = []): Response
    {
        return $this->client->post($endpoint, $data);
    }

    public function delete(string $endpoint, array $data = []): Response
    {
        return $this->client->delete($endpoint, $data);
    }
}
