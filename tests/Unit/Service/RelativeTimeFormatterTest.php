<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\Tests\Unit\Service;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Nowo\RelativeTimeBundle\Service\RelativeTimeFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

use function sprintf;

/**
 * @covers \Nowo\RelativeTimeBundle\Service\RelativeTimeFormatter
 */
final class RelativeTimeFormatterTest extends TestCase
{
    private TranslatorInterface&MockObject $translator;

    private RelativeTimeFormatter $formatter;

    private DateTimeImmutable $now;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->translator->method('getLocale')->willReturn('en');
        $this->translator->method('trans')->willReturnCallback(
            static function (string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string {
                $count = $parameters['%count%'] ?? null;
                if ($id === 'relative_time.just_now') {
                    return $locale === 'es' ? 'hace un momento' : 'just now';
                }
                if ($count === null) {
                    return $id;
                }

                return sprintf('%s:%s:%s', $locale ?? 'en', $id, $count);
            },
        );

        $this->formatter = new RelativeTimeFormatter($this->translator, 'NowoRelativeTimeBundle', 10, 'year', null, 'UTC');
        $this->now       = new DateTimeImmutable('2026-07-18 12:00:00', new DateTimeZone('UTC'));
    }

    public function testNullAndEmptyReturnEmptyString(): void
    {
        self::assertSame('', $this->formatter->format(null, null, $this->now));
        self::assertSame('', $this->formatter->format('', null, $this->now));
    }

    public function testJustNowWithinThreshold(): void
    {
        $date = $this->now->modify('-5 seconds');
        self::assertSame('just now', $this->formatter->format($date, 'en', $this->now));
        self::assertSame('hace un momento', $this->formatter->format($date, 'es', $this->now));
    }

    /**
     * @dataProvider provideUnitCases
     */
    public function testUnitSelection(string $modify, string $expectedKey, int $expectedCount): void
    {
        $date   = $this->now->modify($modify);
        $result = $this->formatter->format($date, 'en', $this->now);
        self::assertSame(sprintf('en:%s:%d', $expectedKey, $expectedCount), $result);
    }

    /**
     * @return iterable<string, array{0: string, 1: string, 2: int}>
     */
    public static function provideUnitCases(): iterable
    {
        yield 'seconds ago' => ['-45 seconds', 'relative_time.ago.second', 45];
        yield 'minutes ago' => ['-5 minutes', 'relative_time.ago.minute', 5];
        yield 'hours ago' => ['-3 hours', 'relative_time.ago.hour', 3];
        yield 'days ago' => ['-2 days', 'relative_time.ago.day', 2];
        yield 'weeks ago' => ['-2 weeks', 'relative_time.ago.week', 2];
        yield 'months ago' => ['-3 months', 'relative_time.ago.month', 3];
        yield 'years ago' => ['-2 years', 'relative_time.ago.year', 2];
        yield 'minutes in future' => ['+8 minutes', 'relative_time.in.minute', 8];
        yield 'days in future' => ['+1 day', 'relative_time.in.day', 1];
    }

    public function testAcceptsUnixTimestampAndString(): void
    {
        $ts = $this->now->modify('-2 hours')->getTimestamp();
        self::assertSame('en:relative_time.ago.hour:2', $this->formatter->format($ts, 'en', $this->now));

        $stringDate = $this->now->modify('-45 seconds')->format(DateTimeImmutable::ATOM);
        self::assertSame('en:relative_time.ago.second:45', $this->formatter->format($stringDate, 'en', $this->now));
    }

    public function testAgoAliasDelegatesToFormat(): void
    {
        $date = $this->now->modify('-1 hour');
        self::assertSame(
            $this->formatter->format($date, 'en', $this->now),
            $this->formatter->ago($date, 'en', $this->now),
        );
    }

    public function testMaxUnitCapsLargestUnit(): void
    {
        $formatter = new RelativeTimeFormatter($this->translator, 'NowoRelativeTimeBundle', 10, 'day', null, 'UTC');
        $date      = $this->now->modify('-400 days');
        // 400 days with max_unit=day → day count, not year
        self::assertSame('en:relative_time.ago.day:400', $formatter->format($date, 'en', $this->now));
    }

    public function testInvalidDateThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->formatter->format('not-a-valid-date', 'en', $this->now);
    }

    public function testNullDefaultTimezoneUsesPhpDefault(): void
    {
        $formatter = new RelativeTimeFormatter($this->translator, 'NowoRelativeTimeBundle', 10, 'year');
        $date      = $this->now->modify('-2 minutes')->format(DateTimeImmutable::ATOM);
        $now       = $this->now->format(DateTimeImmutable::ATOM);
        self::assertSame('en:relative_time.ago.minute:2', $formatter->format($date, 'en', $now));
    }

    public function testFloatTimestampIsAccepted(): void
    {
        $ts = (float) $this->now->modify('-3 hours')->getTimestamp();
        self::assertSame('en:relative_time.ago.hour:3', $this->formatter->format($ts, 'en', $this->now));
    }

    public function testInvalidMaxUnitFallsBackToYearScale(): void
    {
        $formatter = new RelativeTimeFormatter($this->translator, 'NowoRelativeTimeBundle', 10, 'not-a-unit', null, 'UTC');
        $date      = $this->now->modify('-2 years');
        self::assertSame('en:relative_time.ago.year:2', $formatter->format($date, 'en', $this->now));
    }

    public function testZeroDiffWithZeroThresholdUsesSecondFallback(): void
    {
        $formatter = new RelativeTimeFormatter($this->translator, 'NowoRelativeTimeBundle', 0, 'year', null, 'UTC');
        self::assertSame('en:relative_time.in.second:1', $formatter->format($this->now, 'en', $this->now));
    }
}
