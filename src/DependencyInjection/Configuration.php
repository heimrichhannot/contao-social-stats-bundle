<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(HeimrichHannotSocialStatsExtension::ALIAS);

        // Keep compatibility with symfony/config < 4.2
        if (!method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->root(HeimrichHannotSocialStatsExtension::ALIAS);
        } else {
            $rootNode = $treeBuilder->getRootNode();
        }

        $rootNode
            ->children()
                ->scalarNode('base_url')->defaultNull()->info('Override the auto-determined base url.')->end()
                ->arrayNode('matomo')
                    ->children()
                        ->scalarNode('url')->info('Set the matomo url')->end()
                        ->scalarNode('token')->info('The matomo authorization token')->end()
                    ->end()
                ->end()
                ->arrayNode('facebook')
                    ->children()
                        ->scalarNode('app_id')->info('The facebook app id.')->defaultNull()->end()
                        ->scalarNode('app_secret')->info('The facebook app secret.')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('google_analytics')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('view_id')->info('View ID')->end()
                        ->scalarNode('key_file')
                            ->defaultValue('files/bundles/'.HeimrichHannotSocialStatsExtension::ALIAS.'/google_analytics/privatekey.json')
                            ->info('Relative path to the google analytics keyfile.')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
