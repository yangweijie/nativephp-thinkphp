<?php

namespace native\thinkphp\support;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use think\helper\Str;
use yzh52521\EasyHttp\ConnectionException;

class Http extends \yzh52521\EasyHttp\Http
{
    private string $baseUrl;

    /**
     * \GuzzleHttp\Client;
     * @var Client
     */
    protected $client;

    /**
     * Body格式
     * @var string
     */
    protected $bodyFormat;

    /**
     * The raw body for the request.
     *
     * @var string
     */
    protected $pendingBody;

    protected $isRemoveBodyFormat = false;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $promises = [];


    /**
     * Set the base URL for the pending request.
     *
     * @param  string  $url
     * @return $this
     */
    public function baseUrl(string $url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    protected function request(string $method, string $url, array $options = [])
    {
        if (! Str::startsWith($url, ['http://', 'https://'])) {
            $url = ltrim(rtrim($this->baseUrl, '/').'/'.ltrim($url, '/'), '/');
        }
        if (isset($this->options[$this->bodyFormat])) {
            $this->options[$this->bodyFormat] = $options;
        } else {
            $this->options[$this->bodyFormat] = $this->pendingBody;
        }
        if ($this->isRemoveBodyFormat) {
            unset($this->options[$this->bodyFormat]);
        }
        try {
            $response = $this->client->request($method, $url, $this->options);
            return $this->response($response);
        } catch (ConnectException $e) {
            throw new ConnectionException($e->getMessage(), 0, $e);
        }
    }

}