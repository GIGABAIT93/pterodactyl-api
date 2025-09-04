<?php

namespace Gigabait93\Client\Subusers\Enums;

enum SubuserPermission: string
{
    // Control
    case ControlConsole = 'control.console';
    case ControlStart   = 'control.start';
    case ControlStop    = 'control.stop';
    case ControlRestart = 'control.restart';
    case ControlKill    = 'control.kill';

    // Files
    case FilesRead        = 'file.read';
    case FilesReadContent = 'file.read-content';
    case FilesCreate      = 'file.create';
    case FilesUpdate      = 'file.update';
    case FilesDelete      = 'file.delete';
    case FilesArchive     = 'file.archive';
    case FilesSftp        = 'file.sftp';

    // Databases
    case DbRead   = 'database.read';
    case DbCreate = 'database.create';
    case DbDelete = 'database.delete';
    case DbUpdate = 'database.update';

    // Schedules
    case SchedulesRead    = 'schedule.read';
    case SchedulesCreate  = 'schedule.create';
    case SchedulesUpdate  = 'schedule.update';
    case SchedulesDelete  = 'schedule.delete';
    case SchedulesExecute = 'schedule.execute';

    // Backups
    case BackupsRead     = 'backup.read';
    case BackupsCreate   = 'backup.create';
    case BackupsDelete   = 'backup.delete';
    case BackupsDownload = 'backup.download';
    case BackupsRestore  = 'backup.restore';

    // Network/Allocations
    case AllocationRead       = 'allocation.read';
    case AllocationCreate     = 'allocation.create';
    case AllocationUpdate     = 'allocation.update';
    case AllocationDelete     = 'allocation.delete';
    case AllocationSetPrimary = 'allocation.set-primary';

    // Settings / Startup
    case SettingsRename      = 'settings.rename';
    case SettingsReinstall   = 'settings.reinstall';
    case SettingsDockerImage = 'settings.docker-image';
    case StartupRead         = 'startup.read';
    case StartupUpdate       = 'startup.update';

    // Subusers
    case SubuserRead   = 'user.read';
    case SubuserCreate = 'user.create';
    case SubuserUpdate = 'user.update';
    case SubuserDelete = 'user.delete';
}
