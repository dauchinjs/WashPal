<?php

namespace App\Repositories;

use App\Models\Machine;
use GuzzleHttp\Client;

class InformationRepository
{
    private string $url;
    private Client $client;

    public function __construct()
    {
        $this->url = $_ENV['URL'];
        $this->client = new Client();
    }

    public function getInformation(): Machine
    {
        $this->start();

        $token = simplexml_load_string($this->getToken());

        $tokenValue = (string)$token->Value;
        $requestDate = (string)$token->Created;
        $requestTime = date('H:i:s', strtotime($requestDate));

        $response = $this->client->post($this->url . '/task', [
            'headers' => $this->getHeaders($tokenValue),
            'body' => "command=getvalue,request={$requestTime}"
        ]);

        $this->end();

        $response = json_decode($response->getBody()->getContents());

        return new Machine($requestTime, $response->data->node, $response->data->type, $response->data->temp);
    }

    private function getToken(): string
    {
        $response = $this->client->get($this->url . '/?token=renew', [
            'auth' => ['davidsj', 'burtiuncipari'],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $xml = simplexml_load_string($response->getBody());

        return $xml->asXML();
    }

    private function start(): void
    {
        $token = simplexml_load_string($this->getToken());

        $tokenValue = (string)$token->Value;

        $this->client->post($this->url . '/task', [
            'headers' => $this->getHeaders($tokenValue),
            'body' => 'command=begin'
        ]);
    }

    private function end(): void
    {
        $token = simplexml_load_string($this->getToken());

        $tokenValue = (string)$token->Value;

        $this->client->post($this->url . '/task', [
            'headers' => $this->getHeaders($tokenValue),
            'body' => 'command=end'
        ]);
    }

    private function getHeaders(string $tokenValue): array
    {
        return [
            'Content-Type' => 'text/plain;charset=utf-8',
            'Authorization' => "Bearer {$tokenValue}"
        ];
    }
}