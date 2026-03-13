<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\Tests\Unit\Twig;

use Nowo\SerialNumberBundle\Service\SerialNumberGenerator;
use Nowo\SerialNumberBundle\Twig\SerialNumberTwigExtension;
use PHPUnit\Framework\TestCase;

use function strlen;

/**
 * @covers \Nowo\SerialNumberBundle\Twig\SerialNumberTwigExtension
 */
final class SerialNumberTwigExtensionTest extends TestCase
{
    private SerialNumberTwigExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new SerialNumberTwigExtension(
            new SerialNumberGenerator(),
            '*',
            4,
        );
    }

    public function testGenerateSerialNumber(): void
    {
        $context = ['prefix' => 'FAC', 'year' => 2025];
        $pattern = '{prefix}-{year}-{id}';
        $result  = $this->extension->generateSerialNumber($context, $pattern, 42, 5);

        self::assertSame('FAC-2025-00042', $result);
    }

    public function testMaskSerialNumberLeavesLastNVisible(): void
    {
        $serial = 'FAC-2025-01-00042';
        $result = $this->extension->maskSerialNumber($serial, 4, '*');

        self::assertSame('*************0042', $result);
    }

    public function testMaskSerialNumberUsesDefaults(): void
    {
        $serial = 'REC-00007';
        $result = $this->extension->maskSerialNumber($serial);

        self::assertSame('*****0007', $result);
    }

    public function testMaskSerialNumberShortStringReturnsAsIs(): void
    {
        $serial = 'AB';
        $result = $this->extension->maskSerialNumber($serial, 4, '*');

        self::assertSame('AB', $result);
    }

    public function testMaskSerialNumberZeroVisibleReturnsFullMask(): void
    {
        $serial = 'FAC-42';
        $result = $this->extension->maskSerialNumber($serial, 0, '*');

        self::assertSame('******', $result);
    }

    public function testGetFunctionsReturnsSerialNumber(): void
    {
        $functions = $this->extension->getFunctions();
        self::assertCount(1, $functions);
        self::assertSame('serial_number', $functions[0]->getName());
    }

    public function testGetFiltersReturnsSerialNumberMask(): void
    {
        $filters = $this->extension->getFilters();
        self::assertCount(1, $filters);
        self::assertSame('serial_number_mask', $filters[0]->getName());
    }

    public function testMaskSerialNumberNegativeVisibleLastTreatedAsZero(): void
    {
        $serial = 'FAC-42';
        $result = $this->extension->maskSerialNumber($serial, -10, '*');
        self::assertSame('******', $result);
    }

    public function testMaskSerialNumberLongStringTruncatedToPreventDoS(): void
    {
        $serial = str_repeat('A', 5000);
        $result = $this->extension->maskSerialNumber($serial, 4, '*');
        self::assertLessThanOrEqual(2048, strlen($result));
        self::assertStringEndsWith('AAAA', $result);
    }

    public function testMaskSerialNumberMultiCharMaskUsesOnlyFirstChar(): void
    {
        $serial = 'FAC-2025-00042'; // 14 chars; last 4 visible → 10 mask chars
        $result = $this->extension->maskSerialNumber($serial, 4, '**');
        self::assertSame('**********0042', $result);
    }
}
