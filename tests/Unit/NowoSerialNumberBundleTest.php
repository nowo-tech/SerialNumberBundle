<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\Tests\Unit;

use Nowo\SerialNumberBundle\DependencyInjection\NowoSerialNumberExtension;
use Nowo\SerialNumberBundle\NowoSerialNumberBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @covers \Nowo\SerialNumberBundle\NowoSerialNumberBundle
 */
final class NowoSerialNumberBundleTest extends TestCase
{
    private NowoSerialNumberBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new NowoSerialNumberBundle();
    }

    public function testGetContainerExtensionReturnsExtensionInterface(): void
    {
        $extension = $this->bundle->getContainerExtension();

        self::assertInstanceOf(ExtensionInterface::class, $extension);
        self::assertInstanceOf(NowoSerialNumberExtension::class, $extension);
    }

    public function testGetContainerExtensionReturnsSameInstance(): void
    {
        $extension1 = $this->bundle->getContainerExtension();
        $extension2 = $this->bundle->getContainerExtension();

        self::assertSame($extension1, $extension2);
    }

    public function testGetContainerExtensionAlias(): void
    {
        $extension = $this->bundle->getContainerExtension();

        self::assertNotNull($extension);
        self::assertSame('nowo_serial_number', $extension->getAlias());
    }
}
