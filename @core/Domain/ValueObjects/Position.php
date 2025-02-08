<?php

namespace Core\Domain\ValueObjects;

use Exception;

class Position
{
    private string $latitude;

    private string $longitude;

    public function __construct(
        string $latitude,
        string $longitude
    ) {
        $this->validate($latitude, $longitude);
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    private function validate(string $latitude, string $longitude)
    {
        if ((float) $latitude < -90 || (float) $latitude > 90) {
            throw new Exception('Invalid latitude');
        }
        if ((float) $longitude < -180 || (float) $longitude > 180) {
            throw new Exception('Invalid longitude');
        }
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }
}
