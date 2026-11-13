<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\Contract;

use DateTimeInterface;

/**
 * Public contract of the relative-time formatter (REQ-ARCH-001 rule 1).
 *
 * Type-hint this interface, not the final RelativeTimeFormatter. A host (standard Symfony or DDD) may replace the
 * implementation by re-aliasing it. No Symfony or Doctrine type appears in the signatures.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
interface RelativeTimeFormatterInterface
{
    /**
     * Formats a date as a relative time string in the given (or default) locale.
     *
     * @param DateTimeInterface|float|int|string|null $date Date to format (null returns an empty string)
     * @param string|null $locale Locale override (e.g. "es"); null uses the configured or translator locale
     * @param DateTimeInterface|float|int|string|null $now Reference "now"; null uses the current time
     *
     * @return string Localized relative time (e.g. "5 minutes ago", "in 2 hours")
     */
    public function format(
        DateTimeInterface|string|int|float|null $date,
        ?string $locale = null,
        DateTimeInterface|string|int|float|null $now = null,
    ): string;

    /**
     * Alias of format() for past-oriented wording; future dates are still supported.
     */
    public function ago(
        DateTimeInterface|string|int|float|null $date,
        ?string $locale = null,
        DateTimeInterface|string|int|float|null $now = null,
    ): string;
}
