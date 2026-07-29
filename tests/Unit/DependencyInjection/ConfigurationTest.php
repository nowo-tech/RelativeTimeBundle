<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\Tests\Unit\DependencyInjection;

use Nowo\RelativeTimeBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
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
}
