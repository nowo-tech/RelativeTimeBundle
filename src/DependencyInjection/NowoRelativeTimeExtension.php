<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Dependency injection extension for the Relative Time Bundle.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
class NowoRelativeTimeExtension extends Extension
{
    /**
     * Loads bundle services and registers configuration parameters.
     *
     * @param array<int, array<string, mixed>> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter('nowo_relative_time.just_now_threshold_seconds', $config['just_now_threshold_seconds']);
        $container->setParameter('nowo_relative_time.max_unit', $config['max_unit']);
        $container->setParameter('nowo_relative_time.translation_domain', $config['translation_domain']);
        $container->setParameter('nowo_relative_time.default_locale', $config['default_locale']);
        $container->setParameter('nowo_relative_time.default_timezone', $config['default_timezone']);
    }

    public function getAlias(): string
    {
        return 'nowo_relative_time';
    }
}
