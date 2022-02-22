<?php


namespace App\Models\Enums;


enum TaskStatus: string
{
    case NOT_STARTED = 'NOT_STARTED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case READY_FOR_TEST = 'READY_FOR_TEST';
    case COMPLETED = 'COMPLETED';
}
