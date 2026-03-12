<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\Tests\Service;

use Nowo\SerialNumberBundle\Service\SerialNumberGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Nowo\SerialNumberBundle\Service\SerialNumberGenerator
 */
final class SerialNumberGeneratorTest extends TestCase
{
    private SerialNumberGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new SerialNumberGenerator();
    }

    public function testGenerateReplacesContextAndId(): void
    {
        $context = [
            'prefix' => 'FAC',
            'year'   => 2025,
            'office' => '01',
        ];
        $pattern = '{prefix}-{year}-{office}-{id}';
        $result  = $this->generator->generate($context, $pattern, 42);

        self::assertSame('FAC-2025-01-42', $result);
    }

    public function testGeneratePadsIdWhenPaddingGiven(): void
    {
        $context = ['prefix' => 'REC'];
        $pattern = '{prefix}-{id}';
        $result  = $this->generator->generate($context, $pattern, 7, 5);

        self::assertSame('REC-00007', $result);
    }

    public function testGenerateAcceptsStringId(): void
    {
        $context = ['prefix' => 'FAC'];
        $pattern = '{prefix}-{id}';
        $result  = $this->generator->generate($context, $pattern, '123', 5);

        self::assertSame('FAC-00123', $result);
    }

    public function testGenerateWithEmptyContext(): void
    {
        $result = $this->generator->generate([], 'Order-{id}', 99, 3);

        self::assertSame('Order-099', $result);
    }

    public function testGenerateLeavesUnknownPlaceholdersAsIs(): void
    {
        $context = ['year' => 2025];
        $pattern = '{year}-{unknown}-{id}';
        $result  = $this->generator->generate($context, $pattern, 1);

        self::assertSame('2025-{unknown}-1', $result);
    }

    public function testGenerateIdPaddingCappedToPreventDoS(): void
    {
        $context = ['prefix' => 'FAC'];
        $pattern = '{prefix}-{id}';
        $result  = $this->generator->generate($context, $pattern, 1, 100);
        self::assertSame('FAC-' . str_pad('1', 32, '0', \STR_PAD_LEFT), $result);
    }
}
