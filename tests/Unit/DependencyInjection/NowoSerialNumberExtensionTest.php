<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\Tests\Unit\DependencyInjection;

use Nowo\SerialNumberBundle\Contract\SerialNumberGeneratorInterface;
use Nowo\SerialNumberBundle\DependencyInjection\NowoSerialNumberExtension;
use Nowo\SerialNumberBundle\Service\SerialNumberGenerator;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @covers \Nowo\SerialNumberBundle\DependencyInjection\NowoSerialNumberExtension
 */
final class NowoSerialNumberExtensionTest extends TestCase
{
    private NowoSerialNumberExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new NowoSerialNumberExtension();
    }

    public function testGetAlias(): void
    {
        self::assertSame('nowo_serial_number', $this->extension->getAlias());
    }

    public function testLoadSetsDefaultParameters(): void
    {
        $container = new ContainerBuilder();
        $this->extension->load([], $container);

        self::assertSame('*', $container->getParameter('nowo_serial_number.mask_char'));
        self::assertSame(4, $container->getParameter('nowo_serial_number.mask_visible_last'));
    }

    public function testLoadWithCustomConfig(): void
    {
        $container = new ContainerBuilder();
        $this->extension->load([
            [
                'mask_char'         => '•',
                'mask_visible_last' => 6,
            ],
        ], $container);

        self::assertSame('•', $container->getParameter('nowo_serial_number.mask_char'));
        self::assertSame(6, $container->getParameter('nowo_serial_number.mask_visible_last'));
    }

    public function testLoadRegistersSerialNumberGeneratorService(): void
    {
        $container = new ContainerBuilder();
        $this->extension->load([], $container);

        self::assertTrue($container->has(SerialNumberGenerator::class));
    }

    public function testGeneratorInterfaceIsAliasedAndHostCanOverrideIt(): void
    {
        $container = new ContainerBuilder();
        $this->extension->load([], $container);

        self::assertTrue($container->hasAlias(SerialNumberGeneratorInterface::class));
        self::assertSame(SerialNumberGenerator::class, (string) $container->getAlias(SerialNumberGeneratorInterface::class));
        self::assertSame('FAC-00042', (new SerialNumberGenerator())->generate(['prefix' => 'FAC'], '{prefix}-{id}', 42, 5));
        self::assertInstanceOf(SerialNumberGeneratorInterface::class, new SerialNumberGenerator());

        $container->register('app.generator', stdClass::class);
        $container->setAlias(SerialNumberGeneratorInterface::class, 'app.generator');
        self::assertSame('app.generator', (string) $container->getAlias(SerialNumberGeneratorInterface::class));
    }
}
