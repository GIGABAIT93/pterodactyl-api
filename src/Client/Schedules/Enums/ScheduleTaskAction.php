<?php

namespace Gigabait93\Client\Schedules\Enums;

enum ScheduleTaskAction: string
{
    case Command = 'command';
    case Power   = 'power';
    case Backup  = 'backup';
}
