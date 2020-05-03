<?php

namespace fabienChn\FileHandlerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('file_handler');

        $rootNode
            ->children()
                ->scalarNode('temp_folder')->isRequired()->end()
                ->scalarNode('upload_folder')->isRequired()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
