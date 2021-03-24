<?php

namespace Manuxi\GoogleReviewsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('manuxi_google_business_data');
        $rootNode    = $treeBuilder->getRootNode();

        $this->addConnectionSection($rootNode);
        $this->addCacheSection($rootNode);

        return $treeBuilder;
    }

    private function addConnectionSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('connector')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('locale')->defaultValue('en')->end()
                        ->scalarNode('cid')->cannotBeEmpty()->defaultValue('')->end()
                        ->scalarNode('api_key')->cannotBeEmpty()->defaultValue('')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addCacheSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('cache')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('pool')->defaultValue('cache.app')->end()
                        ->integerNode('ttl')
                            ->info('Lifetime, between 60 and 2419200 (1min and 28 days')
                            ->min(60)
                            ->max(2419200)
                            ->defaultValue(86400)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

}
