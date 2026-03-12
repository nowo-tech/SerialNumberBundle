<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\Twig;

use Nowo\SerialNumberBundle\Service\SerialNumberGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig extension that exposes serial number generation and masking.
 *
 * - serial_number(context, pattern, id, padding?): generates full serial from pattern + context + id.
 * - serial_number_mask(serial, visibleLast?, maskChar?): masks a serial leaving only the last N chars visible.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
final class SerialNumberTwigExtension extends AbstractExtension
{
    /** Maximum length for serial input and mask output to prevent DoS via str_repeat. */
    private const MAX_SERIAL_LENGTH = 2048;

    /**
     * @param SerialNumberGenerator $generator Service used to build serials from pattern, context and id
     * @param string $defaultMaskChar Default character for masking (e.g. '*')
     * @param int $defaultVisibleLast Default number of trailing characters to leave visible when masking
     */
    public function __construct(
        private readonly SerialNumberGenerator $generator,
        private readonly string $defaultMaskChar,
        private readonly int $defaultVisibleLast,
    ) {
    }

    /**
     * Returns the list of Twig functions (serial_number).
     *
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'serial_number',
                $this->generateSerialNumber(...),
                ['is_safe' => ['html']],
            ),
        ];
    }

    /**
     * Returns the list of Twig filters (serial_number_mask).
     *
     * @return list<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'serial_number_mask',
                $this->maskSerialNumber(...),
                ['is_safe' => ['html']],
            ),
        ];
    }

    /**
     * Generates a serial number from context, pattern and id (for use in templates).
     *
     * @param array<string, scalar> $context Variables to substitute in the pattern (e.g. ['year' => 2025, 'prefix' => 'FAC'])
     * @param string $pattern Pattern with {var} and {id} (e.g. "{prefix}-{year}-{id}")
     * @param int|string $id Numeric id for the {id} placeholder
     * @param int|null $padding Optional zero-padding length for id
     *
     * @return string The generated serial number
     */
    public function generateSerialNumber(
        array $context,
        string $pattern,
        int|string $id,
        ?int $padding = null,
    ): string {
        return $this->generator->generate($context, $pattern, $id, $padding);
    }

    /**
     * Masks a serial number string, leaving only the last N characters visible.
     *
     * @param string $serial The full serial number (e.g. "FAC-2025-01-00042")
     * @param int|null $visibleLast Number of trailing chars to show (default from config, typically 4)
     * @param string|null $maskChar Character used for masking (default from config, typically '*')
     *
     * @return string The masked string (e.g. "*************0042" when visibleLast is 4)
     */
    public function maskSerialNumber(
        string $serial,
        ?int $visibleLast = null,
        ?string $maskChar = null,
    ): string {
        $visible = $visibleLast ?? $this->defaultVisibleLast;
        $visible = max(0, $visible);

        $char = $maskChar ?? $this->defaultMaskChar;
        $char = mb_strlen($char) > 1 ? mb_substr($char, 0, 1) : $char;

        $length = mb_strlen($serial);
        if ($length > self::MAX_SERIAL_LENGTH) {
            $serial = mb_substr($serial, 0, self::MAX_SERIAL_LENGTH);
            $length = self::MAX_SERIAL_LENGTH;
        }
        if ($visible <= 0) {
            return str_repeat($char, $length);
        }
        if ($length <= $visible) {
            return $serial;
        }

        $visiblePart = mb_substr($serial, -$visible);
        $maskLength  = $length - $visible;

        return str_repeat($char, $maskLength) . $visiblePart;
    }
}
