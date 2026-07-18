<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\Tests\Unit\DependencyInjection;

use Nowo\RelativeTimeBundle\DependencyInjection\NowoRelativeTimeExtension;
use Nowo\RelativeTimeBundle\Service\RelativeTimeFormatter;
use Nowo\RelativeTimeBundle\Twig\RelativeTimeTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @covers \Nowo\RelativeTimeBundle\DependencyInjection\NowoRelativeTimeExtension
 */
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
}
