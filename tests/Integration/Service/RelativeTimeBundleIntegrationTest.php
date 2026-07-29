<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\Tests\Integration\Service;

use DateTimeImmutable;
use DateTimeZone;
use Nowo\RelativeTimeBundle\DependencyInjection\NowoRelativeTimeExtension;
use Nowo\RelativeTimeBundle\Service\RelativeTimeFormatter;
use Nowo\RelativeTimeBundle\Twig\RelativeTimeTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

use function dirname;

/**
 * Integration tests: bundle loaded into a real container with a Translator.
 *
 * @covers \Nowo\RelativeTimeBundle\DependencyInjection\NowoRelativeTimeExtension
 * @covers \Nowo\RelativeTimeBundle\Service\RelativeTimeFormatter
 * @covers \Nowo\RelativeTimeBundle\Twig\RelativeTimeTwigExtension
 */
final class RelativeTimeBundleIntegrationTest extends TestCase
{
    /**
     * @param array<int, array<string, mixed>> $config
     */
    private function buildContainer(array $config = []): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $translatorDef = new Definition(Translator::class, ['en']);
        $translatorDef->setPublic(true);
        $translatorDef->addMethodCall('addLoader', ['yaml', new Definition(YamlFileLoader::class)]);
        $translationsDir = dirname(__DIR__, 3) . '/src/Resources/translations';
        foreach (['en', 'es'] as $locale) {
            $translatorDef->addMethodCall('addResource', [
                'yaml',
                $translationsDir . '/NowoRelativeTimeBundle.' . $locale . '.yaml',
                $locale,
                'NowoRelativeTimeBundle',
            ]);
        }
        $container->setDefinition('translator', $translatorDef);
        $container->setAlias(TranslatorInterface::class, 'translator')->setPublic(true);

        (new NowoRelativeTimeExtension())->load($config, $container);

        $container->addCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                foreach ([RelativeTimeFormatter::class, RelativeTimeTwigExtension::class] as $id) {
                    if ($container->hasDefinition($id)) {
                        $container->getDefinition($id)->setPublic(true);
                    }
                }
            }
        });

        $container->compile();

        return $container;
    }

    public function testContainerFormatsRelativeTimeInEnglishAndSpanish(): void
    {
        $container = $this->buildContainer();
        $formatter = $container->get(RelativeTimeFormatter::class);
        self::assertInstanceOf(RelativeTimeFormatter::class, $formatter);

        $now  = new DateTimeImmutable('2026-07-18 12:00:00', new DateTimeZone('UTC'));
        $past = $now->modify('-5 minutes');

        self::assertSame('5 minutes ago', $formatter->format($past, 'en', $now));
        self::assertSame('hace 5 minutos', $formatter->format($past, 'es', $now));
        self::assertSame('just now', $formatter->format($now->modify('-2 seconds'), 'en', $now));
        self::assertSame('hace un momento', $formatter->format($now->modify('-2 seconds'), 'es', $now));
    }

    public function testTwigExtensionUsesFormatter(): void
    {
        $container = $this->buildContainer();
        $twig      = $container->get(RelativeTimeTwigExtension::class);
        self::assertInstanceOf(RelativeTimeTwigExtension::class, $twig);

        $now    = new DateTimeImmutable('2026-07-18 12:00:00', new DateTimeZone('UTC'));
        $future = $now->modify('+2 hours');

        self::assertSame('in 2 hours', $twig->format($future, 'en', $now));
    }
}
