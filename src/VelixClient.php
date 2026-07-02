<?php

declare(strict_types=1);

namespace Velix;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Velix\Exceptions\AuthException;
use Velix\Exceptions\VelixException;

class VelixClient
{
    private readonly Client $http;
    private readonly string $apiUrl;

    public function __construct(private readonly array $config)
    {
        $this->apiUrl = rtrim($config['apiUrl'] ?? 'https://api.velixbiometrics.com', '/');
        // Contrato oficial (x-velix-sdk-contract-notes.timeout_default_ms, public-api.yaml):
        // 30000ms (30s) default, sempre configurável via $config['timeout'] (segundos, unidade do Guzzle).
        $timeout = $config['timeout'] ?? 30;

        $stack = HandlerStack::create();
        $stack->push($this->retryMiddleware());

        $headers = ['User-Agent' => 'velix-php-sdk/0.1.0-alpha1', 'Accept' => 'application/json'];

        // Auth: x-api-key é o header oficial; Authorization: Bearer vlx_... é a alternativa
        // aceita pelo mesmo ApiKeyAuthGuard. Nunca outro mecanismo de auth (ver public-api.yaml).
        if (!empty($config['apiKey'])) {
            $headers['x-api-key'] = $config['apiKey'];
        } elseif (!empty($config['token'])) {
            $headers['Authorization'] = 'Bearer ' . $config['token'];
        }

        $this->http = new Client([
            'base_uri' => $this->apiUrl,
            'timeout'  => $timeout,
            'headers'  => $headers,
            'handler'  => $stack,
        ]);
    }

    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, ['query' => $query]);
    }

    public function post(string $path, array $body = []): array
    {
        return $this->request('POST', $path, ['json' => $body]);
    }

    public function put(string $path, array $body = []): array
    {
        return $this->request('PUT', $path, ['json' => $body]);
    }

    public function patch(string $path, array $body = []): array
    {
        return $this->request('PATCH', $path, ['json' => $body]);
    }

    public function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }

    private function request(string $method, string $path, array $options = []): array
    {
        try {
            $response = $this->http->request($method, $path, $options);
            $body = json_decode((string) $response->getBody(), true);
            // identity-core wraps responses in { data: T }
            return $body['data'] ?? $body;
        } catch (ConnectException $e) {
            // ConnectException extends TransferException, not RequestException,
            // and never carries a response — must be caught separately or it
            // escapes as a raw GuzzleException instead of a VelixException.
            throw new VelixException($e->getMessage(), 0, $e);
        } catch (RequestException $e) {
            $status = $e->getResponse()?->getStatusCode();
            $body = $e->getResponse() ? json_decode((string) $e->getResponse()->getBody(), true) : [];
            $message = $body['message'] ?? $e->getMessage();

            if ($status === 401 || $status === 403) {
                throw new AuthException($message, $status);
            }
            throw new VelixException($message, $status ?? 0, $e);
        }
    }

    private function retryMiddleware(): callable
    {
        return Middleware::retry(
            function (int $retries, Request $request, ?Response $response): bool {
                if ($retries >= 3) return false;
                if ($response && in_array($response->getStatusCode(), [429, 503])) return true;
                return false;
            },
            function (int $retries): int {
                return (int) (1000 * 2 ** $retries); // 1s, 2s, 4s
            }
        );
    }
}
