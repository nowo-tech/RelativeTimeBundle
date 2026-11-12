<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\Tests\Integration\Worker;

use DateTimeImmutable;
use DateTimeZone;
use Nowo\RelativeTimeBundle\DependencyInjection\NowoRelativeTimeExtension;
use Nowo\RelativeTimeBundle\Service\RelativeTimeFormatter;
use Nowo\RelativeTimeBundle\Twig\RelativeTimeTwigExtension;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

use function dirname;
use function gc_collect_cycles;
use function memory_get_usage;
use function sprintf;

/**
 * REQ-RUNTIME-002: ONE container (formatter, Twig extension and translator kept alive), `services_resetter` /
 * `kernel.reset` never called (FrankenPHP worker, kernel reset false). N iterations alternate two request contexts
 * (English / Spanish, with the host request locale switched on the shared translator like the locale listener does and
 * with an explicit locale argument): every Twig render must be in its own language and memory must stay flat.
 */
#[CoversNothing]
final class WorkerKeptAliveKernelTest extends TestCase
{
    private const ITERATIONS = 1200;
    private const WARM_UP    = 100;
    private const MARGIN     = 4 * 1024 * 1024;

    public function testAlternatingLocalesInOneContainerLeakNothingAndKeepMemoryFlat(): void
    {
        $container  = $this->buildContainer();
        $formatter  = $container->get(RelativeTimeFormatter::class);
        $extension  = $container->get(RelativeTimeTwigExtension::class);
        $translator = $container->get('translator');
        self::assertInstanceOf(RelativeTimeFormatter::class, $formatter);
        self::assertInstanceOf(RelativeTimeTwigExtension::class, $extension);
        self::assertInstanceOf(Translator::class, $translator);

        $twig = new Environment(new ArrayLoader([
            'default'  => '{{ date|relative_time(null, now) }}|{{ relative_time(date, null, now) }}|{{ date|ago(null, now) }}',
            'explicit' => '{{ date|relative_time(locale, now) }}|{{ ago(date, locale, now) }}',
        ]));
        $twig->addExtension($extension);

        $now      = new DateTimeImmutable('2026-07-18 12:00:00', new DateTimeZone('UTC'));
        $contexts = [
            'alpha' => ['locale' => 'en', 'date' => $now->modify('-5 minutes'), 'expected' => '5 minutes ago', 'foreign' => 'hace'],
            'beta'  => ['locale' => 'es', 'date' => $now->modify('+3 hours'), 'expected' => 'dentro de 3 horas', 'foreign' => 'in 3 hours'],
        ];
        // The expected Spanish text is read from the catalogue, not hard-coded.
        $contexts['beta']['expected'] = $formatter->format($contexts['beta']['date'], 'es', $now);
        self::assertNotSame('', $contexts['beta']['expected']);
        $contexts['alpha']['foreign'] = $formatter->format($contexts['beta']['date'], 'es', $now);
        $contexts['beta']['foreign']  = $formatter->format($contexts['alpha']['date'], 'en', $now);

        $baseline = 0;
        for ($i = 0; $i < self::ITERATIONS; ++$i) {
            if ($i === self::WARM_UP) {
                gc_collect_cycles();
                $baseline = memory_get_usage();
            }

            $who  = $i % 2 === 0 ? 'alpha' : 'beta';
            $data = $contexts[$who];

            // The host's locale listener sets the translator locale per request; the formatter must follow it.
            $translator->setLocale($data['locale']);
            $default = $twig->render('default', ['date' => $data['date'], 'now' => $now]);
            self::assertSame($data['expected'] . '|' . $data['expected'] . '|' . $data['expected'], $default, sprintf('iteration %d', $i));

            // Explicit locale wins over whatever the previous request left on the translator.
            $translator->setLocale($who === 'alpha' ? 'es' : 'en');
            $explicit = $twig->render('explicit', ['date' => $data['date'], 'now' => $now, 'locale' => $data['locale']]);
            self::assertSame($data['expected'] . '|' . $data['expected'], $explicit, sprintf('iteration %d', $i));
            self::assertStringNotContainsString($data['foreign'], $default . $explicit, sprintf('iteration %d: other context in output', $i));
        }

        gc_collect_cycles();
        $growth = memory_get_usage() - $baseline;
        self::assertLessThan(self::MARGIN, $growth, sprintf('memory grew by %d bytes over %d iterations', $growth, self::ITERATIONS - self::WARM_UP));
    }

    private function buildContainer(): ContainerBuilder
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

        (new NowoRelativeTimeExtension())->load([], $container);

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

        $container->compile(true);

        return $container;
    }
}
