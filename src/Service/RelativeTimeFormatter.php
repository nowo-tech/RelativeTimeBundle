<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\Service;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

use function is_float;
use function is_int;
use function sprintf;

/**
 * Formats a date/time as a human-readable relative string with i18n (e.g. "2 minutes ago").
 *
 * Supports past and future differences, configurable thresholds, and Symfony translation catalogues
 * for the seven Nowo locales (en, es, it, fr, pt, de, nl).
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
final class RelativeTimeFormatter
{
    /** @var list<string> */
    private const UNITS = ['year', 'month', 'week', 'day', 'hour', 'minute', 'second'];

    /** Average seconds per calendar unit (month ≈ 30 days, year ≈ 365 days). */
    private const UNIT_SECONDS = [
        'year'   => 31536000,
        'month'  => 2592000,
        'week'   => 604800,
        'day'    => 86400,
        'hour'   => 3600,
        'minute' => 60,
        'second' => 1,
    ];

    /**
     * @param TranslatorInterface $translator Symfony translator for pluralized relative-time messages
     * @param string $translationDomain Translation domain (default NowoRelativeTimeBundle)
     * @param int $justNowThresholdSeconds Absolute difference below this uses the "just now" message
     * @param string $maxUnit Largest unit considered (year|month|week|day|hour|minute|second)
     * @param string|null $defaultLocale Fallback locale when none is passed to format()
     * @param string|null $defaultTimezone Timezone used when parsing naive date strings (null = PHP default)
     */
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly string $translationDomain = 'NowoRelativeTimeBundle',
        private readonly int $justNowThresholdSeconds = 10,
        private readonly string $maxUnit = 'year',
        private readonly ?string $defaultLocale = null,
        private readonly ?string $defaultTimezone = null,
    ) {
    }

    /**
     * Formats a date as a relative time string in the given (or default) locale.
     *
     * @param DateTimeInterface|float|int|string|null $date Date to format (null returns empty string)
     * @param string|null $locale Locale override (e.g. "es"); null uses defaultLocale or translator locale
     * @param DateTimeInterface|float|int|string|null $now Reference "now"; null uses current time
     *
     * @return string Localized relative time (e.g. "hace 5 minutos", "in 2 hours")
     */
    public function format(
        DateTimeInterface|string|int|float|null $date,
        ?string $locale = null,
        DateTimeInterface|string|int|float|null $now = null,
    ): string {
        if ($date === null || $date === '') {
            return '';
        }

        $target         = $this->normalizeDate($date);
        $reference      = $this->normalizeDate($now ?? 'now');
        $diffSeconds    = $target->getTimestamp() - $reference->getTimestamp();
        $absSeconds     = abs($diffSeconds);
        $resolvedLocale = $locale ?? $this->defaultLocale ?? $this->translator->getLocale();

        if ($absSeconds < $this->justNowThresholdSeconds) {
            return $this->translator->trans(
                'relative_time.just_now',
                [],
                $this->translationDomain,
                $resolvedLocale,
            );
        }

        [$unit, $count] = $this->resolveUnitAndCount($absSeconds);
        $direction      = $diffSeconds < 0 ? 'ago' : 'in';
        $key            = sprintf('relative_time.%s.%s', $direction, $unit);

        return $this->translator->trans(
            $key,
            ['%count%' => $count],
            $this->translationDomain,
            $resolvedLocale,
        );
    }

    /**
     * Alias for past-oriented wording; still supports future dates.
     */
    public function ago(
        DateTimeInterface|string|int|float|null $date,
        ?string $locale = null,
        DateTimeInterface|string|int|float|null $now = null,
    ): string {
        return $this->format($date, $locale, $now);
    }

    private function normalizeDate(DateTimeInterface|string|int|float $value): DateTimeImmutable
    {
        if ($value instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($value);
        }

        $timezone = $this->defaultTimezone !== null && $this->defaultTimezone !== ''
            ? new DateTimeZone($this->defaultTimezone)
            : null;

        if (is_int($value) || is_float($value)) {
            return (new DateTimeImmutable('@' . (int) $value))
                ->setTimezone($timezone ?? new DateTimeZone(date_default_timezone_get()));
        }

        try {
            return new DateTimeImmutable($value, $timezone);
        } catch (Exception $e) {
            throw new InvalidArgumentException(sprintf('Invalid date value: "%s".', $value), 0, $e);
        }
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function resolveUnitAndCount(int $absSeconds): array
    {
        $maxIndex = array_search($this->maxUnit, self::UNITS, true);
        if ($maxIndex === false) {
            $maxIndex = 0;
        }

        foreach (self::UNITS as $index => $unit) {
            if ($index < $maxIndex) {
                continue;
            }
            $secondsPerUnit = self::UNIT_SECONDS[$unit];
            if ($absSeconds >= $secondsPerUnit) {
                return [$unit, max(1, (int) floor($absSeconds / $secondsPerUnit))];
            }
        }

        return ['second', max(1, $absSeconds)];
    }
}
