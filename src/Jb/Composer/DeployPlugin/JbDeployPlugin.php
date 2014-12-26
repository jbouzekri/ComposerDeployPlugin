<?php

namespace Jb\Composer\DeployPlugin;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class JbDeployPlugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        var_dump('here');
    }
}