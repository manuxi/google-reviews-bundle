<?php

namespace manuxi\GoogleBusinessDataBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ManuxiGoogleBusinessDataExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        dump($configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config        = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('ma_go_bu_da.connector');

        $definition->setArgument(0, $config['connector']['cid']);
        $definition->setArgument(1, $config['connector']['api_key']);
        if (isset($config['connector']['locale'])) {
            $definition->setArgument(2, $config['connector']['locale']);
        }

        if (isset($config['cache']['enabled']) && $config['cache']['enabled']) {
            $definition = $container->getDefinition('ma_go_bu_da.cache');
            $definition->setArgument(0, new Reference($config['cache']['pool']));
            $definition->setArgument(1, $config['cache']['lifetime']);
        }
    }
}
