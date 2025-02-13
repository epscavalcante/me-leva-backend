<?php

use Core\Domain\Entities\Position;
use Core\Domain\Services\DistanceCalculator;
use Core\Domain\ValueObjects\Coordinate;
use Core\Domain\ValueObjects\Uuid;

describe('Distance Calculator Service Unit Test', function () {
    test('Deve calcular a distância entre duas coordenadas', function () {
        $start = new Coordinate('-27.584905257808835', '-48.545022195325124');
        $end = new Coordinate('-27.496887588317275', '-48.522234807851476');
        $distance = DistanceCalculator::calculate($start, $end);
        expect($distance)->toBe(10.04168245097873);
    });

    test('Deve calcular a distância atraves das posições', function () {

        $rideId = Uuid::create();

        $positions = [
            Position::create(
                rideId: $rideId,
                latitude: '-27.584905257808835',
                longitude: '-48.545022195325124'
            ),

            Position::create(
                rideId: $rideId,
                latitude: '-27.496887588317275',
                longitude: '-48.522234807851476'
            ),

            Position::create(
                rideId: $rideId,
                latitude: '-27.496887588317275',
                longitude: '-48.522234807851476'
            ),

            Position::create(
                rideId: $rideId,
                latitude: '-27.584905257808835',
                longitude: '-48.545022195325124'
            ),

            Position::create(
                rideId: $rideId,
                latitude: '-27.584905257808835',
                longitude: '-48.545022195325124'
            ),

            Position::create(
                rideId: $rideId,
                latitude: '-27.496887588317275',
                longitude: '-48.522234807851476'
            ),
        ];

        $distance = DistanceCalculator::calculateByPositions($positions);
        expect($distance)->toBe(30.12504735293619);
    });
});
