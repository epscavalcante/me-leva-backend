<?php

namespace App\Events\Ride;

use Core\Domain\Events\Event as DomainEvent;
use Core\Domain\Events\RideRequestedEvent as RideRequestedDomainEvent;
use Core\Domain\Events\RideAcceptedEvent as RideAcceptedDomainEvent;
use Core\Domain\Events\RideStartedEvent as RideStartedDomainEvent;
use Core\Domain\Events\RidePositionUpdatedEvent as RidePositionUpdatedDomainEvent;
use Core\Domain\Events\RideFinishedEvent as RideFinishedDomainEvent;

class RideEventFactory
{
    public static function create(DomainEvent $event): RideBaseEvent
    {
        return match ($event->getName()) {
            RideRequestedDomainEvent::name() => new RideRequestedEvent($event->getEntityId(), $event->getName(), $event->getData()),
            RideAcceptedDomainEvent::name() => new RideAcceptedEvent($event->getEntityId(), $event->getName(), $event->getData()),
            RideStartedDomainEvent::name() => new RideStartedEvent($event->getEntityId(), $event->getName(), $event->getData()),
            RidePositionUpdatedDomainEvent::name() => new RidePositionUpdatedEvent($event->getEntityId(), $event->getName(), $event->getData()),
            RideFinishedDomainEvent::name() => new RideCompletedEvent($event->getEntityId(), $event->getName(), $event->getData()),
        };
    }
}
