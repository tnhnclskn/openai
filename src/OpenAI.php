<?php

namespace Tnhnclskn\OpenAI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class OpenAI
{
    private Client $client;
    public function __construct(
        private string $organizationId,
        private string $secretKey
    ) {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secretKey,
            ]
        ]);
    }

    public function request(string $method, string $uri, array $data = [])
    {
        try {
            $response = $this->client->request($method, $uri, [
                ($method === 'GET' ? 'query' : 'json') => $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $responseBodyAsArray = json_decode($responseBodyAsString, true);
            $error = $responseBodyAsArray['error'];
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
