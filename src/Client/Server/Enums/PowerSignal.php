<?php

namespace Gigabait93\Client\Server\Enums;

enum PowerSignal: string
{
    case Start   = 'start';
    case Stop    = 'stop';
    case Restart = 'restart';
    case Kill    = 'kill';
}
