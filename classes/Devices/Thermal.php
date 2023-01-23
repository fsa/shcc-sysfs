<?php

namespace FSA\SysFSPlugin\Devices;

use FSA\SmartHome\DeviceInterface;

class Thermal implements DeviceInterface
{
    private $uid;
    private $temperature;
    private $events;
    private $updated;

    public function __construct()
    {
        $this->updated = 0;
    }

    public function getHwid(): string
    {
        return $this->uid;
    }

    public function getDescription(): string
    {
        return 'Данные с сенсоров температуры.';
    }

    public function getEventsList(): array
    {
        return ['temperature'];
    }

    public function init($device_id, $init_data): void
    {
        $this->uid = $device_id;
        foreach ($init_data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getInitDataList(): array
    {
        return [];
    }

    public function getInitDataValues(): array
    {
        return [];
    }

    public function getLastUpdate(): int
    {
        return $this->updated;
    }

    public function update(): bool
    {
        $this->updated = time();
        $m_deg = file_get_contents('/sys/class/thermal/' . $this->uid . '/temp');
        $deg = (float) $m_deg / 1000;
        if ($deg != $this->temperature) {
            $this->temperature = $deg;
            $this->events = ['temperature' => $deg];
            return true;
        }
        return false;
    }

    public function getEvents(): ?array
    {
        if (empty($this->events)) {
            return null;
        }
        $events = $this->events;
        $this->events = null;
        return $events;
    }

    public function getState(): array
    {
        return $this->temperature ? ['temperature' => $this->temperature] : [];
    }

    public function __toString(): string
    {
        return $this->temperature ? ('Температура ' . $this->temperature) : ('Нет данных');
    }
}
