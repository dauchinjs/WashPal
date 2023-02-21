<?php

namespace App\Services;

use App\Repositories\InformationRepository;

class InformationService
{
    private InformationRepository $repository;
    private array $previousTemperatures;

    public function __construct()
    {
        $this->repository = new InformationRepository();
        $this->previousTemperatures = [];
    }

    public function convertInformation(): string
    {
        $information = $this->repository->getInformation();
        $time = $information->getTime();
        $node = $information->getNode();
        $type = $information->getType();
        $fahrenheitTemperature = $information->getTemperature();

        if($type === 'unknown') {
            return '';
        }

        $celsiusTemperature = number_format(($fahrenheitTemperature - 32) * 5 / 9, 2);

        $machineID = $node . '_' . $type;

        if (isset($this->previousTemperatures[$machineID])) {
            $deltaTemperature = $celsiusTemperature - $this->previousTemperatures[$machineID];
        } else {
            $deltaTemperature = '';
        }

        $this->previousTemperatures[$machineID] = $celsiusTemperature;

        return "{$time}, {$node}, {$type}, {$celsiusTemperature}, {$deltaTemperature}" . "\n";
    }
}