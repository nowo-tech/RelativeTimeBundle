# Usage

## Service

```php
use Nowo\RelativeTimeBundle\Service\RelativeTimeFormatter;

final class ActivityPresenter
{
    public function __construct(
        private readonly RelativeTimeFormatter $relativeTime,
    ) {
    }

    public function present(\DateTimeInterface $at, string $locale = 'es'): string
    {
        return $this->relativeTime->format($at, $locale);
    }
}
```

Accepted date inputs: `DateTimeInterface`, UNIX timestamp (`int`/`float`), parseable string, or `null`/`''` (returns empty string).

Optional third argument `$now` fixes the reference instant (useful in tests).

`ago()` is an alias of `format()`.

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
