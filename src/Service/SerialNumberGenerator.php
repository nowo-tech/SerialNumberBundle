<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\Service;

use const STR_PAD_LEFT;

/**
 * Generates human-readable serial numbers from a pattern, context variables and a numeric id.
 *
 * Use for invoices, receipts, tickets, etc. The pattern may contain placeholders like {year},
 * {prefix}, {office} and {id} (the numeric part, optionally zero-padded).
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
final class SerialNumberGenerator
{
    /** @var string Placeholder in the pattern replaced by the numeric id (optionally padded). */
    private const ID_PLACEHOLDER = '{id}';

    /** Maximum padding length to prevent DoS via str_pad. */
    private const MAX_ID_PADDING = 32;

    /**
     * Generates a serial number by substituting context variables and the id into the pattern.
     *
     * Pattern placeholders: {name} for context keys (e.g. {year}, {prefix}) and {id} for the numeric id.
     * Example: pattern "{prefix}-{year}-{office}-{id}", context ['prefix' => 'FAC', 'year' => 2025, 'office' => '01'], id 42, padding 5
     *          → "FAC-2025-01-00042"
     *
     * @param array<string, scalar> $context Key-value map for placeholders (e.g. ['year' => 2025, 'prefix' => 'FAC'])
     * @param string $pattern Pattern with {var} and {id} placeholders (e.g. "{prefix}-{year}-{id}")
     * @param int|string $id The numeric id to inject (used for {id}); will be zero-padded if idPadding is set
     * @param int|null $idPadding If set, id is zero-padded to this length (e.g. 5 → 00042)
     *
     * @return string The generated serial number
     */
    public function generate(
        array $context,
        string $pattern,
        int|string $id,
        ?int $idPadding = null,
    ): string {
        $idStr = (string) $id;
        if ($idPadding !== null && $idPadding > 0) {
            $idPadding = min($idPadding, self::MAX_ID_PADDING);
            $idStr     = str_pad($idStr, $idPadding, '0', STR_PAD_LEFT);
        }

        $result = $pattern;
        foreach ($context as $key => $value) {
            $result = str_replace('{' . $key . '}', (string) $value, $result);
        }

        return str_replace(self::ID_PLACEHOLDER, $idStr, $result);
    }
}
