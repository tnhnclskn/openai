<?php

namespace Tnhnclskn\OpenAI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class OpenAI
{
    const DEFAULT_CONFIG = [
        'chat_model' => 'gpt-3.5-turbo-0613',
        'retry' => false,
        'retry_delay' => 0,
    ];

    private Client $client;
    private array $config;

    public function __construct(
        private string $organizationId,
        private string $secretKey,
        array $config = []
    ) {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secretKey,
            ]
        ]);

        $this->config = array_merge(self::DEFAULT_CONFIG, $config);
    }

    public function config(string $key)
    {
        return $this->config[$key] ?? null;
    }

    public function request(string $method, string $uri, array $data = [], int $try = 0)
    {
        $retry = $this->config('retry');
        $retryDelay = $this->config('retry_delay');

        try {
            $response = $this->client->request($method, $uri, [
                ($method === 'GET' ? 'query' : 'json') => $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseCode = $response->getStatusCode();
            $responseBodyAsString = $response->getBody()->getContents();
            $responseBodyAsArray = json_decode($responseBodyAsString, true);
            $error = $responseBodyAsArray['error'];

            if ($responseCode === 500 && $retry > 0) {
                if ($try < $retry) {
                    if ($retryDelay) sleep($retryDelay);
                    return $this->request($method, $uri, $data, $try + 1);
                }
            }

            throw new Exception(json_encode($error, JSON_PRETTY_PRINT));
        }
    }

    public function get(string $uri, array $data = [])
    {
        return $this->request('GET', $uri, $data);
    }

    public function post(string $uri, array $data = [])
    {
        return $this->request('POST', $uri, $data);
    }

    public function chat(string $systemMessage = ''): Chat
    {
        return new Chat($this, $systemMessage);
    }
}
