<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle;

use Nowo\RelativeTimeBundle\DependencyInjection\NowoRelativeTimeExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Symfony bundle that formats DateTime values as localized relative time strings.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
class NowoRelativeTimeBundle extends Bundle
{
    /**
     * Returns the DI extension for this bundle (NowoRelativeTimeExtension).
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new NowoRelativeTimeExtension();
        }

        return $this->extension instanceof ExtensionInterface ? $this->extension : null;
    }
}
