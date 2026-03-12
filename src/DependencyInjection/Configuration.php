<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Defines the configuration tree for the Serial Number Bundle (nowo_serial_number).
 *
 * Options: mask_char (character for masking), mask_visible_last (default number of trailing chars visible).
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Builds the config tree for nowo_serial_number (mask_char, mask_visible_last).
     *
     * @return TreeBuilder The config tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('nowo_serial_number');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('mask_char')
                    ->info('Single character used to mask the serial number when using the Twig filter (e.g. "*")')
                    ->defaultValue('*')
                    ->validate()
                        ->ifTrue(static fn ($v): bool => $v !== '' && mb_strlen((string) $v) > 1)
                        ->thenInvalid('mask_char must be a single character.')
                    ->end()
                ->end()
                ->integerNode('mask_visible_last')
                    ->info('Default number of trailing characters to leave visible when masking (e.g. 4 for last 4 digits)')
                    ->defaultValue(4)
                    ->min(0)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
