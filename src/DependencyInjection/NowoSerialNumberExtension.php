<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Dependency injection extension for the Serial Number Bundle.
 * Loads services and optional configuration.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
class NowoSerialNumberExtension extends Extension
{
    /**
     * Loads bundle services and registers configuration parameters.
     *
     * @param array<int, array<string, mixed>> $configs List of config arrays (from config files)
     * @param ContainerBuilder $container Container builder to register definitions and parameters
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter('nowo_serial_number.mask_char', $config['mask_char']);
        $container->setParameter('nowo_serial_number.mask_visible_last', $config['mask_visible_last']);
    }

    /**
     * Returns the alias name of the extension.
     *
     * @return string The alias (nowo_serial_number)
     */
    public function getAlias(): string
    {
        return 'nowo_serial_number';
    }
}
