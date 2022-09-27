<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class HeimrichHannotSocialStatsExtension extends Extension
{
    const ALIAS = 'huh_social_stats';

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration(true);
        $processedConfig = $this->processConfiguration($configuration, $configs);

        if (isset($processedConfig['google_analytics']['key_file'])) {
            $processedConfig['google_analytics']['key_file'] = $container->getParameter('kernel.project_dir').'/'.$processedConfig['google_analytics']['key_file'];
        }

        $container->setParameter(static::ALIAS, $processedConfig);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );
        $loader->load('services.yml');
    }

    public function getAlias()
    {
        return static::ALIAS;
    }
}
