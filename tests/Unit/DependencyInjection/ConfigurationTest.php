<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\Tests\Unit\DependencyInjection;

use Nowo\SerialNumberBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

/**
 * @covers \Nowo\SerialNumberBundle\DependencyInjection\Configuration
 */
final class ConfigurationTest extends TestCase
{
    private Configuration $configuration;

    private Processor $processor;

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor     = new Processor();
    }

    public function testGetConfigTreeBuilder(): void
    {
        $treeBuilder = $this->configuration->getConfigTreeBuilder();

        self::assertInstanceOf(TreeBuilder::class, $treeBuilder);
    }

    public function testDefaultConfiguration(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, []);

        self::assertSame('*', $config['mask_char']);
        self::assertSame(4, $config['mask_visible_last']);
    }

    public function testCustomConfiguration(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'mask_char'         => '•',
                'mask_visible_last' => 6,
            ],
        ]);

        self::assertSame('•', $config['mask_char']);
        self::assertSame(6, $config['mask_visible_last']);
    }

    public function testPartialConfigurationMergesWithDefaults(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            ['mask_visible_last' => 8],
        ]);

        self::assertSame('*', $config['mask_char']);
        self::assertSame(8, $config['mask_visible_last']);
    }
}
