<?php

namespace Gigabait93\Applications\Nodes\Enums;

enum NodesInclude: string
{
    case Allocations = 'allocations';
    case Location    = 'location';
    case Servers     = 'servers';
}
