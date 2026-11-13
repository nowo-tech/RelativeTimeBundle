# Usage

## Table of contents

- [Service](#service)
- [Twig](#twig)
- [Units](#units)
- [Translation keys](#translation-keys)
- [Standard Symfony and DDD hosts](#standard-symfony-and-ddd-hosts)

## Service

```php
use Nowo\RelativeTimeBundle\Contract\RelativeTimeFormatterInterface;

final class ActivityPresenter
{
    public function __construct(
        private readonly RelativeTimeFormatterInterface $relativeTime,
    ) {
    }

    public function present(\DateTimeInterface $at, string $locale = 'es'): string
    {
        return $this->relativeTime->format($at, $locale);
    }
}
```

The concrete `Service\RelativeTimeFormatter` is final and implements `Contract\RelativeTimeFormatterInterface`; type-hint the interface (the old class id keeps working).

Accepted date inputs: `DateTimeInterface`, UNIX timestamp (`int`/`float`), parseable string, or `null`/`''` (returns empty string).

Optional third argument `$now` fixes the reference instant (useful in tests).

`ago()` is an alias of `format()`.

On FrankenPHP worker with sticky Kernel (`FRANKENPHP_RESET_KERNEL=false`), pass `$locale` explicitly when formatting outside a normal HTTP request (for example Messenger / emails). The shared formatter holds no per-request state; see [FRANKENPHP-WORKER-AUDIT.md](FRANKENPHP-WORKER-AUDIT.md).

## Twig

```twig
{{ post.createdAt|relative_time }}
{{ post.createdAt|ago }}
{{ post.createdAt|relative_time('es') }}
{{ relative_time(post.createdAt, 'fr') }}
{{ ago(post.createdAt, 'de', referenceNow) }}
```

Filter/function signature: `(date, locale = null, now = null)`.

## Units

From largest to smallest (subject to `max_unit`): year, month, week, day, hour, minute, second.

Differences below `just_now_threshold_seconds` use `relative_time.just_now`.

## Translation keys

Domain: `NowoRelativeTimeBundle`.

```yaml
relative_time:
  just_now: '...'
  ago:
    second: '%count% ...|%count% ...'
    minute: ...
    hour: ...
    day: ...
    week: ...
    month: ...
    year: ...
  in:
    second: ...
    # same units
```

Pluralization uses Symfony’s `|` / `%count%` convention.

## Standard Symfony and DDD hosts

The bundle works with no configuration in a standard Symfony app: autowire `RelativeTimeFormatterInterface` (or use the Twig filter) and you are done. It has no entities, no events and no database access, so there is nothing to map.

A DDD / hexagonal host keeps its domain free of framework types:

- **Port to depend on:** `Nowo\RelativeTimeBundle\Contract\RelativeTimeFormatterInterface`. Its signatures use only `DateTimeInterface`, scalars and `string`; no Symfony, Doctrine or HTTP type. Declare your own port in the application layer (for example `App\Application\Port\TimeAgo`) and implement it in Infrastructure by delegating to the interface:

  ```php
  final class RelativeTimeAdapter implements TimeAgo
  {
      public function __construct(private readonly RelativeTimeFormatterInterface $formatter) {}

      public function since(\DateTimeImmutable $at, string $locale): string
      {
          return $this->formatter->format($at, $locale);
      }
  }
  ```

- **Replace the implementation:** re-alias the interface to your own service (the Twig extension and every autowired consumer follow the alias):

  ```yaml
  services:
      Nowo\RelativeTimeBundle\Contract\RelativeTimeFormatterInterface: '@App\Infrastructure\Time\MyFormatter'
  ```

- **Time source:** pass `$now` explicitly or provide a PSR-20 `Psr\Clock\ClockInterface` service; the domain never calls the system clock through this bundle.
- **Persistence and events:** none (REQ-ARCH-001 rules 2, 4, 6 and 7 do not apply). Configuration comes from the bundle config tree only.
