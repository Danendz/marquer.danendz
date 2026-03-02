<?php

namespace App\Enums;

enum StudySessionStatus: string
{
    case Active = 'active';
    case Paused = 'paused';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
