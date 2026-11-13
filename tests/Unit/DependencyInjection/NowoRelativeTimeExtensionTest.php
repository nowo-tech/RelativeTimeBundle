<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\Tests\Unit\DependencyInjection;

use Nowo\RelativeTimeBundle\Contract\RelativeTimeFormatterInterface;
use Nowo\RelativeTimeBundle\DependencyInjection\NowoRelativeTimeExtension;
use Nowo\RelativeTimeBundle\Service\RelativeTimeFormatter;
use Nowo\RelativeTimeBundle\Twig\RelativeTimeTwigExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[CoversClass(NowoRelativeTimeExtension::class)]
final class NowoRelativeTimeExtensionTest extends TestCase
{
    public function testLoadRegistersServicesAndParameters(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        $extension = new NowoRelativeTimeExtension();
        $extension->load([[
            'just_now_threshold_seconds' => 15,
            'max_unit'                   => 'week',
        ]], $container);

        self::assertSame('nowo_relative_time', $extension->getAlias());
        self::assertSame(15, $container->getParameter('nowo_relative_time.just_now_threshold_seconds'));
        self::assertSame('week', $container->getParameter('nowo_relative_time.max_unit'));
        self::assertTrue($container->hasDefinition(RelativeTimeFormatter::class));
        self::assertTrue($container->hasDefinition(RelativeTimeTwigExtension::class));
    }

    public function testFormatterInterfaceIsAliasedAndHostCanOverrideIt(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        (new NowoRelativeTimeExtension())->load([[]], $container);

        self::assertTrue($container->hasAlias(RelativeTimeFormatterInterface::class));
        self::assertSame(RelativeTimeFormatter::class, (string) $container->getAlias(RelativeTimeFormatterInterface::class));
        self::assertInstanceOf(RelativeTimeFormatterInterface::class, (new ReflectionClass(RelativeTimeFormatter::class))->newInstanceWithoutConstructor());

        $container->register('app.formatter', stdClass::class);
        $container->setAlias(RelativeTimeFormatterInterface::class, 'app.formatter');
        self::assertSame('app.formatter', (string) $container->getAlias(RelativeTimeFormatterInterface::class));
    }
}
