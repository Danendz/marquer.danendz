<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Draft = 'draft';
    case Progress = 'progress';
    case Done = 'done';
    case Cancelled = 'cancelled';
}
