<?php

namespace App\Enums;

enum TimerMode: string
{
    case CountUp = 'count_up';
    case CountDown = 'count_down';
    case Pomodoro = 'pomodoro';
}
