<?php

namespace native\thinkphp\facade;

use GuzzleHttp\Promise\PromiseInterface;
use think\Facade;
use native\thinkphp\support\Http as Request;
use yzh52521\EasyHttp\Response;

/**
     * @method static Request baseUrl(string $url)
     * @method static Request asJson()
     * @method static Request asForm()
     * @method static Request asMultipart(string $name, string $contents, string|null $filename = null, array $headers)
     * @method static Request attach(string $name, string $contents, string|null $filename = null, array $headers)
     *
     * @method static Request withRedirect(bool|array $redirect)
     * @method static Request withStream(bool $boolean)
     * @method static Request withVerify(bool|string $verify)
     * @method static Request withHost(string $host)
     * @method static Request withHeaders(array $headers)
     * @method static Request withBody($content, $contentType='application/json')
     * @method static Request withBasicAuth(string $username, string $password)
     * @method static Request withDigestAuth(string $username, string $password)
     * @method static Request withUA(string $ua)
     * @method static Request withToken(string $token, string $type = 'Bearer')
     * @method static Request withCookies(array $cookies, string $domain)
     * @method static Request withProxy(string|array $proxy)
     * @method static Request withVersion(string $version)
     * @method static Request withOptions(array $options)
     * @method static Request withMiddleware(callable $middleware)
     * @method static Request withRequestMiddleware(callable $middleware)
     * @method static Request withResponseMiddleware(callable $middleware)
     *
     * @method static Request debug($class)
     * @method static Request retry(int $retries=1, int $sleep=0)
     * @method static Request delay(int $seconds)
     * @method static Request timeout(float $seconds)
     * @method static Request connectTimeout(float $seconds)
     * @method static Request sink(string|resource $to)
     * @method static Request concurrency(int $times)
     * @method static Request removeBodyFormat()
     * @method static Request maxRedirects(int $max)
     *
     * @method static Response get(string $url, array $query = [])
     * @method static Response post(string $url, array $data = [])
     * @method static Response patch(string $url, array $data = [])
     * @method static Response put(string $url, array $data = [])
     * @method static Response delete(string $url, array $data = [])
     * @method static Response head(string $url, array $data = [])
     * @method static Response options(string $url, array $data = [])
     * @method static Response client(string $method, string $url, array $options = [])
     * @method static Response clientAsync(string $method, string $url, array $options = [])
     *
     * @method static PromiseInterface getAsync(string $url, array|null $query = null, callable $success = null, callable $fail = null)
     * @method static PromiseInterface postAsync(string $url, array|null $data = null, callable $success = null, callable $fail = null)
     * @method static PromiseInterface patchAsync(string $url, array|null $data = null, callable $success = null, callable $fail = null)
     * @method static PromiseInterface putAsync(string $url, array|null $data = null, callable $success = null, callable $fail = null)
     * @method static PromiseInterface deleteAsync(string $url, array|null $data = null, callable $success = null, callable $fail = null)
     * @method static PromiseInterface headAsync(string $url, array|null $data = null, callable $success = null, callable $fail = null)
     * @method static PromiseInterface optionsAsync(string $url, array|null $data = null, callable $success = null, callable $fail = null)
     * @method static \GuzzleHttp\Pool multiAsync(array $promises, callable $success = null, callable $fail = null)
     * @method static void wait()
     */

class Http extends Facade
{
    protected string $facade = Request::class;
}