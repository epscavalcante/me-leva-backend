<?php

namespace Core\Domain\Services;

use Core\Domain\ValueObjects\Coordinate;

class DistanceCalculator
{
    public static function calculate(Coordinate $start, Coordinate $end): float
    {
        $startLatitude = deg2rad((float) $start->getLatitude());
        $startLongitude = deg2rad((float) $start->getLongitude());
        $endLatitude = deg2rad((float) $end->getLatitude());
        $endLongitude = deg2rad((float) $end->getLongitude());
        $dist = (6371 * acos(cos($startLatitude) * cos($endLatitude) * cos($endLongitude - $startLongitude) + sin($startLatitude) * sin($endLatitude)));
        return $dist;
    }

    /**
     * @param  Position[]  $positions
     */
    public static function calculateByPositions(array $positions): float
    {
        $distance = 0;
        if (count($positions) === 0) {
            return $distance;
        }
        foreach ($positions as $key => $value) {
            $nextPositionKey = $key + 1;
            if (! array_key_exists($nextPositionKey, $positions)) {
                continue;
            }
            $nextPosition = $positions[$nextPositionKey];
            $distance += DistanceCalculator::calculate($value->getCoordinate(), $nextPosition->getCoordinate());
        }
        return $distance;
    }
}
