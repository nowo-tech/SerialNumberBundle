<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\Tests\Unit\Service;

use InvalidArgumentException;
use Nowo\SerialNumberBundle\Service\SerialNumberGenerator;
use PHPUnit\Framework\TestCase;

use const STR_PAD_LEFT;

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
        self::assertSame('FAC-' . str_pad('1', 32, '0', STR_PAD_LEFT), $result);
    }

    public function testContextValueContainingPlaceholderIsNotReSubstituted(): void
    {
        $result = $this->generator->generate(
            ['prefix' => '{year}{id}', 'year' => 2025],
            '{prefix}-{year}-{id}',
            42,
            5,
        );

        self::assertSame('{year}{id}-2025-00042', $result);
    }

    public function testContextIdKeyKeepsPrecedenceOverId(): void
    {
        self::assertSame('X-ctx', $this->generator->generate(['id' => 'ctx'], 'X-{id}', 7));
    }

    public function testNegativeIdIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generator->generate([], '{id}', -5, 5);
    }

    public function testNegativeNumericStringIdIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generator->generate([], '{id}', '-42');
    }

    public function testZeroAndNonNumericStringIdsAreAccepted(): void
    {
        self::assertSame('000', $this->generator->generate([], '{id}', 0, 3));
        self::assertSame('A-1', $this->generator->generate([], '{id}', 'A-1'));
    }
}
