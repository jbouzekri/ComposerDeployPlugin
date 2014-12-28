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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Config
 * Contains all configuration for deploy plugin
 *
 * @author jobou
 */
class Config
{
    const PLUGIN_NAMESPACE = 'jb-composer-deploy';

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor
     *
     * @param \Composer\Composer $composer
     */
    public function __construct(Composer $composer)
    {
        $extras = $composer->getPackage()->getExtra();
        $this->config = $this->normalizeConfiguration($extras);
    }

    /**
     * Get all public folders to deploy
     *
     * @return array
     */
    public function getFolders()
    {
        return $this->config['folders'];
    }

    /**
     * Get target dir
     *
     * @return string
     */
    public function getTargetDir()
    {
        return $this->config['target-dir'];
    }

    /**
     * Get symlink
     *
     * @return bool
     */
    public function getSymlink()
    {
        return $this->config['symlink'];
    }

    /**
     * Make symlink relative
     *
     * @return bool
     */
    public function getRelative()
    {
        return $this->config['relative'];
    }

    /**
     * Get exclude
     *
     * @return array
     */
    public function getExclude()
    {
        return $this->config['exclude'];
    }

    /**
     * Normalize configuration
     *
     * @param array $extras
     * @return array
     */
    protected function normalizeConfiguration(array $extras)
    {
        $config = (isset($extras[self::PLUGIN_NAMESPACE])) ? $extras[self::PLUGIN_NAMESPACE] : array();
        $resolver = $this->getConfiguredResolver();

        return $resolver->resolve($config);
    }

    /**
     * Configure an options resolver
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    protected function getConfiguredResolver()
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(array('target-dir'));

        $resolver->setDefaults(array(
            'target-dir' => null,
            'exclude' => array(),
            'folders' => array(),
            'symlink' => false,
            'relative' => false
        ));

        $resolver->setAllowedTypes(array(
            'target-dir' => array('null', 'string'),
            'exclude' => array('array'),
            'folders' => array('array'),
            'symlink' => array('bool'),
            'relative' => array('bool')
        ));

        return $resolver;
    }
}
