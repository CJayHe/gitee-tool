<?php

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

/**
 * Class ScriptHandler
 * @package RedUnicorn\SymfonyKernel\Composer
 *
 */
class ScriptHandler
{
    public static function gitFolderRemove(Event $event)
    {
        exec( 'rm -rf ' .__DIR__ . '/../vendor/redunicorn/**/.git');
    }
}