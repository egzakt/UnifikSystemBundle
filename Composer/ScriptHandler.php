<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flexy\SystemBundle\Composer;

use Symfony\Component\ClassLoader\ClassCollectionLoader;
use Symfony\Component\Process\Process;
use Composer\Script\CommandEvent;

use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as BaseScriptHandler;

class ScriptHandler extends BaseScriptHandler
{

    public static function installFlexy(CommandEvent $event)
    {
        $options = self::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        static::executeCommand($event, $appDir, 'flexy:install', $options['process-timeout']);
    }

}
