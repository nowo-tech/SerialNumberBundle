<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle;

use Nowo\SerialNumberBundle\DependencyInjection\NowoSerialNumberExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Symfony bundle that provides serial number generation and Twig masking for invoices, receipts, etc.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
class NowoSerialNumberBundle extends Bundle
{
    /**
     * Returns the DI extension for this bundle (NowoSerialNumberExtension).
     *
     * @return ExtensionInterface|null The extension instance
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new NowoSerialNumberExtension();
        }

        return $this->extension instanceof ExtensionInterface ? $this->extension : null;
    }
}
