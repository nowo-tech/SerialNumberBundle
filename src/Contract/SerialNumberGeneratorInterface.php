<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\Contract;

use InvalidArgumentException;

/**
 * Public contract of the serial-number generator (REQ-ARCH-001 rule 1).
 *
 * Type-hint this interface, not the final SerialNumberGenerator. A host (standard Symfony or DDD) may replace the
 * implementation by re-aliasing it. No Symfony or Doctrine type appears in the signature.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
interface SerialNumberGeneratorInterface
{
    /**
     * Generates a serial number by substituting context variables and the id into the pattern.
     *
     * @param array<string, scalar> $context Key-value map for {name} placeholders
     * @param string $pattern Pattern with {var} and {id} placeholders (e.g. "{prefix}-{year}-{id}")
     * @param int|string $id The numeric id to inject for {id}
     * @param int|null $idPadding If set, the id is zero-padded to this length
     *
     * @throws InvalidArgumentException When $id is a negative number
     *
     * @return string The generated serial number
     */
    public function generate(
        array $context,
        string $pattern,
        int|string $id,
        ?int $idPadding = null,
    ): string;
}
