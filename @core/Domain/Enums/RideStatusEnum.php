<?php

namespace Core\Domain\Enums;

enum RideStatusEnum: string
{
    case REQUESTED = 'requested';

    case ACCEPTED = 'accepted';

    case STARTED = 'in_progress';

    case COMPLETED = 'completed';

    case CANCELED = 'canceled';
}
