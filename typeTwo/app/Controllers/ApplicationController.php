<?php

namespace App\Controllers;

use App\Services\InformationService;

class ApplicationController
{
    private InformationService $informationService;
    private array $nodeCount;

    public function __construct()
    {
        $this->informationService = new InformationService();
        $this->nodeCount = [];
    }

    public function run(): void
    {
        $maxRequestsPerNode = 100;

        $filename = 'view/output.csv';
        $file = fopen($filename, 'w');
        fwrite($file, "Time, Node, Type, Temperature (°C), Δ\n");

        while (true) {
            $information = $this->informationService->convertInformation();
            if (empty($information)) {
                continue;
            }
            fwrite($file, $information);
            $node = explode(',', $information)[1];
            $node = trim($node);
            if (array_key_exists($node, $this->nodeCount)) {
                $this->nodeCount[$node]++;
                if ($this->nodeCount[$node] == $maxRequestsPerNode) {
                    break;
                }
            } else {
                $this->nodeCount[$node] = 1;
            }
        }

        fclose($file);
    }
}