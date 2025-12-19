<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class ManuxiGoogleReviewsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('manuxi_google_reviews.connector');
        $definition->setArgument(0, $config['connector']['cid']);
        $definition->setArgument(1, $config['connector']['api_key']);

        if (isset($config['connector']['locale'])) {
            $definition->setArgument(2, $config['connector']['locale']);
        }

        $definition = $container->getDefinition('manuxi_google_reviews.cache');
        $definition->setArgument(0, new Reference('monolog.logger.cache'));

        if (isset($config['cache']['enabled']) && $config['cache']['enabled']) {
            $definition->setArgument(1, new Reference($config['cache']['pool']));
            $definition->setArgument(2, $config['cache']['ttl']);
        }
    }
}
