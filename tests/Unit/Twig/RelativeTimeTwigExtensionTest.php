<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\Tests\Unit\Twig;

use DateTimeImmutable;
use DateTimeZone;
use Nowo\RelativeTimeBundle\Service\RelativeTimeFormatter;
use Nowo\RelativeTimeBundle\Twig\RelativeTimeTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

use function sprintf;

/**
 * @covers \Nowo\RelativeTimeBundle\Twig\RelativeTimeTwigExtension
 */
final class RelativeTimeTwigExtensionTest extends TestCase
{
    private function createExtension(): RelativeTimeTwigExtension
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('getLocale')->willReturn('en');
        $translator->method('trans')->willReturnCallback(
            static function (string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string {
                if ($id === 'relative_time.just_now') {
                    return 'just now';
                }
                $count = $parameters['%count%'] ?? 0;

                return sprintf('%s:%d', $id, $count);
            },
        );

        return new RelativeTimeTwigExtension(new RelativeTimeFormatter($translator));
    }

    public function testRegistersFiltersAndFunctions(): void
    {
        $extension = $this->createExtension();

        $filterNames   = array_map(static fn (TwigFilter $f): string => $f->getName(), $extension->getFilters());
        $functionNames = array_map(static fn (TwigFunction $f): string => $f->getName(), $extension->getFunctions());

        self::assertSame(['relative_time', 'ago'], $filterNames);
        self::assertSame(['relative_time', 'ago'], $functionNames);
    }

    public function testFormatDelegatesToService(): void
    {
        $extension = $this->createExtension();
        $now       = new DateTimeImmutable('2026-07-18 12:00:00', new DateTimeZone('UTC'));
        $date      = $now->modify('-5 minutes');

        self::assertSame('relative_time.ago.minute:5', $extension->format($date, 'es', $now));
    }
}
