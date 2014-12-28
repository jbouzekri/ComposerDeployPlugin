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
use Composer\Package\PackageInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * JbDeployPlugin
 *
 * @author Jonathan Bouzekri <jonathan.bouzekri@gmail.com>
 */
class JbDeployPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * {@inheritDoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_INSTALL_CMD => array(
                array('deployAssets', 0)
            ),
            ScriptEvents::POST_UPDATE_CMD => array(
                array('deployAssets', 0)
            ),
        );
    }

    /**
     * Deploy assets by hard coping or symlinking folder from vendor
     * to a configured root folder
     *
     * @param \Composer\Script\Event $event
     */
    public function deployAssets(Event $event)
    {
        $this->dump('Trying to deploy package');

        $targetDir = $this->getConfig()->getTargetDir();

        if ($targetDir === null) {
            $this->dump('No target dir configured', 'error');
            return;
        }

        if (!is_dir($targetDir)) {
            $this->dump(sprintf('Target dir %s does not exists', $targetDir), 'error');
            return;
        }

        $installedPackages = $event
            ->getComposer()
            ->getRepositoryManager()
            ->getLocalRepository()
            ->getCanonicalPackages();

        $excludePackages = $this->getConfig()->getExclude();

        foreach ($installedPackages as $package) {
            if (in_array($package->getName(), $excludePackages)) {
                continue;
            }

            $this->deployPackage($event, $package);
        }

        $this->dump('End');
    }

    /**
     * Deploy a single package
     *
     * @param Event $event
     * @param PackageInterface $package
     */
    protected function deployPackage(Event $event, PackageInterface $package)
    {
        $path = $event->getComposer()->getInstallationManager()->getInstallPath($package);
        $packageDirName = $this->getPackageDirName($package);
        $configuredTargetDir = $this->getConfig()->getTargetDir();

        foreach ($this->getConfig()->getFolders() as $folder) {
            if (!is_dir($originDir = $path.'/'.$folder)) {
                continue;
            }

            $targetDir = sprintf('%s/%s/%s', $configuredTargetDir, $packageDirName, $folder);

            $this->getFilesystem()->remove($targetDir);

            $this->dump(
                sprintf(
                    'Folder <comment>%s</comment> from package <comment>%s</comment>'
                    . ' deployed in <comment>%s</comment>',
                    $folder,
                    $package->getName(),
                    $targetDir
                )
            );
        }
    }

    /**
     * Compile a dir name from a package
     *
     * @param \Composer\Package\PackageInterface $package
     *
     * @return string
     */
    protected function getPackageDirName(PackageInterface $package)
    {
        return str_replace('/', '-', $package->getName());
    }

    /**
     * Get loaded and normalized config object
     *
     * @return Config
     */
    protected function getConfig()
    {
        if ($this->config === null) {
            $this->config = new Config($this->composer);
        }

        return $this->config;
    }

    /**
     * Get the filesystem service
     *
     * @return Filesystem
     */
    protected function getFilesystem()
    {
        if ($this->filesystem === null) {
            $this->filesystem = new Filesystem();
        }

        return $this->filesystem;
    }

    /**
     * Write a message to stdout
     *
     * @param string $message
     * @param string $type
     */
    protected function dump($message, $type = null)
    {
        if ($type !== null) {
            $message = sprintf("<%s>%s</%s>", $type, $message, $type);
        }

        $this->io->write(sprintf("%s >> %s", Config::PLUGIN_NAMESPACE, $message));
    }
}