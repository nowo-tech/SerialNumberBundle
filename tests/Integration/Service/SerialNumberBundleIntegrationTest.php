<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\Tests\Integration\Service;

use Nowo\SerialNumberBundle\DependencyInjection\NowoSerialNumberExtension;
use Nowo\SerialNumberBundle\Service\SerialNumberGenerator;
use Nowo\SerialNumberBundle\Twig\SerialNumberTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Integration tests: bundle loaded into a real container, services resolved and used.
 *
 * @covers \Nowo\SerialNumberBundle\DependencyInjection\NowoSerialNumberExtension
 * @covers \Nowo\SerialNumberBundle\Service\SerialNumberGenerator
 * @covers \Nowo\SerialNumberBundle\Twig\SerialNumberTwigExtension
 */
final class SerialNumberBundleIntegrationTest extends TestCase
{
    /**
     * Build and compile container with bundle extension; make required services public for testing.
     *
     * @param array<int, array<string, mixed>> $config
     */
    private function buildContainer(array $config = []): ContainerBuilder
    {
        $container = new ContainerBuilder();
        (new NowoSerialNumberExtension())->load($config, $container);

        $container->addCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                foreach ([SerialNumberGenerator::class, SerialNumberTwigExtension::class] as $id) {
                    if ($container->hasDefinition($id)) {
                        $container->getDefinition($id)->setPublic(true);
                    }
                }
            }
        });

        $container->compile();

        return $container;
    }

    public function testContainerProvidesSerialNumberGeneratorAndGeneratesSerial(): void
    {
        $container = $this->buildContainer();

        $generator = $container->get(SerialNumberGenerator::class);
        self::assertInstanceOf(SerialNumberGenerator::class, $generator);

        $result = $generator->generate(
            ['prefix' => 'FAC', 'year' => 2025, 'office' => '01'],
            '{prefix}-{year}-{office}-{id}',
            42,
            5,
        );
        self::assertSame('FAC-2025-01-00042', $result);
    }

    public function testContainerProvidesTwigExtensionWithConfigDefaults(): void
    {
        $container = $this->buildContainer();

        $twigExtension = $container->get(SerialNumberTwigExtension::class);
        self::assertInstanceOf(SerialNumberTwigExtension::class, $twigExtension);
        /** @var SerialNumberTwigExtension $twigExtension */
        $serial = 'FAC-2025-01-00042';
        $masked = $twigExtension->maskSerialNumber($serial);
        self::assertSame('*************0042', $masked);
    }

    public function testContainerWithCustomConfigUsesCustomMaskCharAndVisibleLast(): void
    {
        $container = $this->buildContainer([
            [
                'mask_char'         => '•',
                'mask_visible_last' => 6,
            ],
        ]);

        $twigExtension = $container->get(SerialNumberTwigExtension::class);
        self::assertInstanceOf(SerialNumberTwigExtension::class, $twigExtension);
        /** @var SerialNumberTwigExtension $twigExtension */
        $serial = 'INV-2025-000123';
        $masked = $twigExtension->maskSerialNumber($serial);
        self::assertSame('•••••••••000123', $masked);
    }
}
