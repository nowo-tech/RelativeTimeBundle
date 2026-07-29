<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\Tests\Unit;

use Nowo\RelativeTimeBundle\DependencyInjection\NowoRelativeTimeExtension;
use Nowo\RelativeTimeBundle\NowoRelativeTimeBundle;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Nowo\RelativeTimeBundle\NowoRelativeTimeBundle
 */
final class NowoRelativeTimeBundleTest extends TestCase
{
    public function testGetContainerExtension(): void
    {
        $bundle    = new NowoRelativeTimeBundle();
        $extension = $bundle->getContainerExtension();

        self::assertInstanceOf(NowoRelativeTimeExtension::class, $extension);
        self::assertSame($extension, $bundle->getContainerExtension());
    }
}
