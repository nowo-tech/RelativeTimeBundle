#!/usr/bin/env php
<?php

declare(strict_types=1);

require dirname(__DIR__).'/vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$translationsDir = dirname(__DIR__).'/src/Resources/translations';
$domain = 'NowoRelativeTimeBundle';
$requiredLocales = ['en', 'es', 'it', 'fr', 'pt', 'de', 'nl'];

/**
 * @param array<string, mixed> $data
 *
 * @return list<string>
 */
function flattenKeys(array $data, string $prefix = ''): array
{
    $keys = [];
    foreach ($data as $key => $value) {
        $fullKey = '' === $prefix ? (string) $key : $prefix.'.'.$key;
        if (\is_array($value)) {
            $keys = array_merge($keys, flattenKeys($value, $fullKey));
        } else {
            $keys[] = $fullKey;
        }
    }
    sort($keys);

    return $keys;
}

$referenceFile = $translationsDir.'/'.$domain.'.en.yaml';
if (!is_file($referenceFile)) {
    fwrite(STDERR, "Missing reference catalogue: {$referenceFile}\n");
    exit(1);
}

$referenceKeys = flattenKeys(Yaml::parseFile($referenceFile));
$exitCode = 0;

foreach ($requiredLocales as $locale) {
    $file = $translationsDir.'/'.$domain.'.'.$locale.'.yaml';
    if (!is_file($file)) {
        fwrite(STDERR, "Missing locale file: {$file}\n");
        $exitCode = 1;

        continue;
    }

    try {
        $keys = flattenKeys(Yaml::parseFile($file));
    } catch (\Throwable $exception) {
        fwrite(STDERR, "Invalid YAML in {$file}: {$exception->getMessage()}\n");
        $exitCode = 1;

        continue;
    }

    $missing = array_diff($referenceKeys, $keys);
    $extra = array_diff($keys, $referenceKeys);

    if ([] !== $missing) {
        fwrite(STDERR, "[{$locale}] Missing keys: ".implode(', ', $missing)."\n");
        $exitCode = 1;
    }
    if ([] !== $extra) {
        fwrite(STDERR, "[{$locale}] Extra keys: ".implode(', ', $extra)."\n");
        $exitCode = 1;
    }
}

if (0 === $exitCode) {
    echo 'Translation syntax and key parity OK ('.count($requiredLocales)." locales).\n";
}

exit($exitCode);
