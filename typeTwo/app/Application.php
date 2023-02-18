<?php

namespace App;

use GuzzleHttp\Client;

class Application
{
    private string $url;
    private Client $client;
    private array $previousTemperatures;

    public function __construct()
    {
        $this->url = $_ENV['URL'];
        $this->client = new Client();
        $this->previousTemperatures = [];
    }

    public function run(): string
    {
        $output = '';

        $amountOfRequests = 100;
        for ($i = 0; $i < $amountOfRequests; $i++) {
            $output .= $this->convertInformation();
        }

        return 'Time, Node, Type, Temperature (°C), Δ' . PHP_EOL . $output;
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

    private function getInformation(): string
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

        return $response->getBody()->getContents();
    }

    private function convertInformation(): string
    {
        $token = simplexml_load_string($this->getToken());

        $requestDate = (string)$token->Created;
        $requestTime = date('H:i:s', strtotime($requestDate));

        $information = $this->getInformation();
        $data = json_decode($information, true);
        $node = $data['data']['node'];
        $type = $data['data']['type'];
        $fahrenheitTemperature = $data['data']['temp'];
        $celsiusTemperature = number_format(($fahrenheitTemperature - 32) * 5 / 9, 2);

        $machineID = $node . '_' . $type;

        if (isset($this->previousTemperatures[$machineID])) {
            $deltaTemperature = $celsiusTemperature - $this->previousTemperatures[$machineID];
        } else {
            $deltaTemperature = '';
        }

        $this->previousTemperatures[$machineID] = $celsiusTemperature;

        return "{$requestTime}, {$node}, {$type}, {$celsiusTemperature}, {$deltaTemperature}" . PHP_EOL;
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