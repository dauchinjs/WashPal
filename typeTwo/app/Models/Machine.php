<?php

namespace App\Models;

class Machine
{
    private string $time;
    private int $node;
    private string $type;
    private float $temperature;

    public function __construct(
        string $time,
        int $node,
        string $type,
        float $temperature
    )
    {
        $this->time = $time;
        $this->node = $node;
        $this->type = $type;
        $this->temperature = $temperature;
    }

    public function getTime(): string
    {
        return $this->time;
    }

    public function getNode(): int
    {
        return $this->node;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }
}