<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\Tests\Unit\DependencyInjection;

use Nowo\RelativeTimeBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

/**
 * @covers \Nowo\RelativeTimeBundle\DependencyInjection\Configuration
 */
final class ConfigurationTest extends TestCase
{
    public function testDefaultConfiguration(): void
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), [[]]);

        self::assertSame(10, $config['just_now_threshold_seconds']);
        self::assertSame('year', $config['max_unit']);
        self::assertSame('NowoRelativeTimeBundle', $config['translation_domain']);
        self::assertNull($config['default_locale']);
        self::assertNull($config['default_timezone']);
    }

    public function testCustomConfiguration(): void
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), [[
            'just_now_threshold_seconds' => 30,
            'max_unit'                   => 'day',
            'translation_domain'         => 'App',
            'default_locale'             => 'es',
            'default_timezone'           => 'Europe/Madrid',
        ]]);

        self::assertSame(30, $config['just_now_threshold_seconds']);
        self::assertSame('day', $config['max_unit']);
        self::assertSame('App', $config['translation_domain']);
        self::assertSame('es', $config['default_locale']);
        self::assertSame('Europe/Madrid', $config['default_timezone']);
    }

    public function testDefaultTimezoneAcceptsLegacyIdentifiersAndEmptyString(): void
    {
        $processor = new Processor();

        self::assertSame('UTC', $processor->processConfiguration(new Configuration(), [['default_timezone' => 'UTC']])['default_timezone']);
        self::assertSame('Europe/Kiev', $processor->processConfiguration(new Configuration(), [['default_timezone' => 'Europe/Kiev']])['default_timezone']);
        self::assertSame('', $processor->processConfiguration(new Configuration(), [['default_timezone' => '']])['default_timezone']);
    }

    /**
     * Values PHP's DateTimeZone accepts that are not in listIdentifiers() worked before 1.1.0 and must keep working.
     *
     * @return iterable<string, array{string}>
     */
    public static function phpAcceptedTimezoneProvider(): iterable
    {
        yield 'positive offset' => ['+02:00'];
        yield 'negative offset' => ['-05:30'];
        yield 'abbreviation' => ['CEST'];
        yield 'legacy identifier' => ['Europe/Kiev'];
        yield 'utc' => ['UTC'];
        yield 'empty string' => [''];
    }

    /**
     * @dataProvider phpAcceptedTimezoneProvider
     */
    public function testDefaultTimezoneAcceptsEverythingDateTimeZoneAccepts(string $timezone): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), [['default_timezone' => $timezone]]);

        self::assertSame($timezone, $config['default_timezone']);
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidTimezoneProvider(): iterable
    {
        yield 'unknown identifier' => ['Mars/Olympus'];
        yield 'typo' => ['Europe/Madird'];
        yield 'garbage' => ['not a timezone!'];
        yield 'bad offset' => ['+99:99:99x'];
        yield 'non-string' => [123];
    }

    /**
     * @dataProvider invalidTimezoneProvider
     */
    public function testInvalidDefaultTimezoneIsRejected(mixed $timezone): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('default_timezone must be a timezone accepted by DateTimeZone');

        (new Processor())->processConfiguration(new Configuration(), [['default_timezone' => $timezone]]);
    }
}
