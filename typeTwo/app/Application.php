<?php
//
//namespace App;
//
//use GuzzleHttp\Client;
//
//class Application
//{
//    private string $url;
//    private Client $client;
//    private array $previousTemperatures;
//    private array $nodeCount;
//
//    public function __construct()
//    {
//        $this->url = $_ENV['URL'];
//        $this->client = new Client();
//        $this->previousTemperatures = [];
//        $this->nodeCount = [];
//    }
//
//    public function run(): void
//    {
//        $maxRequestsPerNode = 100;
//
//        $filename = 'view/output.csv';
//        $file = fopen($filename, 'w');
//        fwrite($file, "Time, Node, Type, Temperature (°C), Δ\n");
//
//        while (true) {
//            $information = $this->convertInformation();
//            if (empty($information)) {
//                continue;
//            }
//            fwrite($file, $information);
//            $node = explode(',', $information)[1];
//            $node = trim($node);
//            if (array_key_exists($node, $this->nodeCount)) {
//                $this->nodeCount[$node]++;
//                if ($this->nodeCount[$node] == $maxRequestsPerNode) {
//                    break;
//                }
//            } else {
//                $this->nodeCount[$node] = 1;
//            }
//        }
//
//        fclose($file);
//    }
//
//    private function getToken(): string
//    {
//        $response = $this->client->get($this->url . '/?token=renew', [
//            'auth' => ['davidsj', 'burtiuncipari'],
//            'headers' => [
//                'Accept' => 'application/json',
//            ],
//        ]);
//
//        $xml = simplexml_load_string($response->getBody());
//
//        return $xml->asXML();
//    }
//
//    private function getInformation(): string
//    {
//        $this->start();
//
//        $token = simplexml_load_string($this->getToken());
//
//        $tokenValue = (string)$token->Value;
//        $requestDate = (string)$token->Created;
//        $requestTime = date('H:i:s', strtotime($requestDate));
//
//        $response = $this->client->post($this->url . '/task', [
//            'headers' => $this->getHeaders($tokenValue),
//            'body' => "command=getvalue,request={$requestTime}"
//        ]);
//
//        $this->end();
//
//        return $response->getBody()->getContents();
//    }
//
//    private function convertInformation(): string
//    {
//        $token = simplexml_load_string($this->getToken());
//
//        $requestDate = (string)$token->Created;
//        $requestTime = date('H:i:s', strtotime($requestDate));
//
//        $information = $this->getInformation();
//        $data = json_decode($information, true);
//        $node = $data['data']['node'];
//        $type = $data['data']['type'];
//
//        if ($type === "unknown") {
//            return '';
//        }
//
//        $fahrenheitTemperature = $data['data']['temp'];
//        $celsiusTemperature = number_format(($fahrenheitTemperature - 32) * 5 / 9, 2);
//
//        $machineID = $node . '_' . $type;
//
//        if (isset($this->previousTemperatures[$machineID])) {
//            $deltaTemperature = $celsiusTemperature - $this->previousTemperatures[$machineID];
//        } else {
//            $deltaTemperature = '';
//        }
//
//        $this->previousTemperatures[$machineID] = $celsiusTemperature;
//
//        return "{$requestTime}, {$node}, {$type}, {$celsiusTemperature}, {$deltaTemperature}" . "\n";
//    }
//
//    private function start(): void
//    {
//        $token = simplexml_load_string($this->getToken());
//
//        $tokenValue = (string)$token->Value;
//
//        $this->client->post($this->url . '/task', [
//            'headers' => $this->getHeaders($tokenValue),
//            'body' => 'command=begin'
//        ]);
//    }
//
//    private function end(): void
//    {
//        $token = simplexml_load_string($this->getToken());
//
//        $tokenValue = (string)$token->Value;
//
//        $this->client->post($this->url . '/task', [
//            'headers' => $this->getHeaders($tokenValue),
//            'body' => 'command=end'
//        ]);
//    }
//
//    private function getHeaders(string $tokenValue): array
//    {
//        return [
//            'Content-Type' => 'text/plain;charset=utf-8',
//            'Authorization' => "Bearer {$tokenValue}"
//        ];
//    }
//}