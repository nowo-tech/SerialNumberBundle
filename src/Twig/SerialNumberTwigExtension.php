<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\Twig;

use InvalidArgumentException;
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
     * Encoding used for all mb_* calls so masking does not depend on process-wide
     * mb_internal_encoding() (important under FrankenPHP worker with no kernel reset).
     */
    private const MB_ENCODING = 'UTF-8';

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
     * @throws InvalidArgumentException When the mask character is empty
     *
     * @return string The masked string (e.g. "*************0042" when visibleLast is 4)
     */
    public function maskSerialNumber(
        string $serial,
        ?int $visibleLast = null,
        ?string $maskChar = null,
    ): string {
        $visible = $visibleLast ?? $this->defaultVisibleLast;
        $visible = min(max(0, $visible), self::MAX_SERIAL_LENGTH);

        $char = $maskChar ?? $this->defaultMaskChar;
        if ($char === '') {
            throw new InvalidArgumentException('serial_number_mask: the mask character must not be empty.');
        }
        $char = mb_strlen($char, self::MB_ENCODING) > 1
            ? mb_substr($char, 0, 1, self::MB_ENCODING)
            : $char;

        $length = mb_strlen($serial, self::MB_ENCODING);
        if ($length <= $visible) {
            return $serial;
        }

        // The visible suffix always comes from the original serial; only the masked part is capped
        // so the output never exceeds MAX_SERIAL_LENGTH characters.
        $visiblePart = $visible > 0 ? mb_substr($serial, -$visible, null, self::MB_ENCODING) : '';
        $maskLength  = min($length - $visible, self::MAX_SERIAL_LENGTH - $visible);

        return str_repeat($char, $maskLength) . $visiblePart;
    }
}
