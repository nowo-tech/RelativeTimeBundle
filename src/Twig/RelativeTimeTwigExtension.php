<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\Twig;

use DateTimeInterface;
use Nowo\RelativeTimeBundle\Contract\RelativeTimeFormatterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig filters and functions for relative time formatting.
 *
 * - relative_time / ago: human-readable past/future relative strings with i18n.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
final class RelativeTimeTwigExtension extends AbstractExtension
{
    /**
     * @param RelativeTimeFormatterInterface $formatter Bundle formatter service
     */
    public function __construct(
        private readonly RelativeTimeFormatterInterface $formatter,
    ) {
    }

    /**
     * @return list<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('relative_time', $this->format(...)),
            new TwigFilter('ago', $this->format(...)),
        ];
    }

    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('relative_time', $this->format(...)),
            new TwigFunction('ago', $this->format(...)),
        ];
    }

    /**
     * Formats a date as a localized relative time string.
     *
     * @param string|null $locale Optional locale override
     * @param DateTimeInterface|float|int|string|null $now Optional reference "now"
     */
    public function format(
        DateTimeInterface|string|int|float|null $date,
        ?string $locale = null,
        DateTimeInterface|string|int|float|null $now = null,
    ): string {
        return $this->formatter->format($date, $locale, $now);
    }
}
