<?php

declare(strict_types=1);

namespace Nowo\RelativeTimeBundle\DependencyInjection;

use DateTimeZone;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use function in_array;
use function is_string;

/**
 * Defines the configuration tree for the Relative Time Bundle (nowo_relative_time).
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Builds the config tree for nowo_relative_time.
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('nowo_relative_time');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->integerNode('just_now_threshold_seconds')
                    ->info('Absolute difference (seconds) below which "just now" / "hace un momento" is used')
                    ->defaultValue(10)
                    ->min(0)
                ->end()
                ->enumNode('max_unit')
                    ->info('Largest unit used when formatting (year, month, week, day, hour, minute, second)')
                    ->values(['year', 'month', 'week', 'day', 'hour', 'minute', 'second'])
                    ->defaultValue('year')
                ->end()
                ->scalarNode('translation_domain')
                    ->info('Symfony translation domain for relative time catalogues')
                    ->defaultValue('NowoRelativeTimeBundle')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('default_locale')
                    ->info('Optional default locale; null uses the translator/request locale')
                    ->defaultNull()
                ->end()
                ->scalarNode('default_timezone')
                    ->info('Optional timezone identifier (e.g. Europe/Madrid) for parsing date strings; null uses PHP default timezone')
                    ->defaultNull()
                    ->validate()
                        ->ifTrue(static fn (mixed $value): bool => $value !== null && $value !== '' && (!is_string($value) || !in_array($value, DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC), true)))
                        ->thenInvalid('default_timezone must be a valid timezone identifier (see DateTimeZone::listIdentifiers()), got %s.')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
