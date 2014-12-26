<?php

/**
 * Copyright 2014 Jonathan Bouzekri. All rights reserved.
 *
 * @copyright Copyright 2014 Jonathan Bouzekri <jonathan.bouzekri@gmail.com>
 * @license https://github.com/jbouzekri/ComposerDeployPlugin/blob/master/LICENSE
 * @link https://github.com/jbouzekri/ComposerDeployPlugin
 */

namespace Jb\Composer\DeployPlugin;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\ScriptEvents;
use Composer\Script\Event;

/**
 * JbDeployPlugin
 *
 * @author Jonathan Bouzekri <jonathan.bouzekri@gmail.com>
 */
class JbDeployPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_INSTALL_CMD => array(
                array('onPostInstallCmd', 0)
            ),
            ScriptEvents::POST_UPDATE_CMD => array(
                array('onPostUpdateCmd', 0)
            ),
        );
    }

    public function onPostInstallCmd(Event $event)
    {
        var_dump('post install event');
    }

    public function onPostUpdateCmd(Event $event)
    {
        var_dump('post update event');
    }
}