<?php

namespace Gigabait93\Applications\Eggs\Enums;

enum EggsInclude: string
{
    case Nest      = 'nest';
    case Variables = 'variables';
    case Servers   = 'servers';
    case Config    = 'config';
    case Script    = 'script';
}
