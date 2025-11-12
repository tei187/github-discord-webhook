# Detection

This document provides an overview of the detection capabilities and features available in the application. It covers various detection methods, configurations, and best practices to ensure effective monitoring and identification of relevant events.

## Service detection

Service detection is primarily handled by the `\tei187\GitDisWebhook\Factories\ServiceFactory` factory class. The factory attempts to create the service based on the detection rules defined in the `config/services.php` configuration file, unless the service class to create is explicitly specified. If a matching service is found, it will be instantiated and returned to the worker.

The detection methods are assigned to specific service indentifiers within the configuration file. Each service can have multiple detection methods, which are evaluated in the order they are defined. The first matching method will determine the service to be used.

Methods available for service detection include:
- **header existence**: Checks if a specific HTTP header is present in the incoming request. Assignable as an array with keys as header names. 
- **header value match**: Checks if a specific HTTP header has a particular value. Assignable as an array with keys as header names and values as expected header values.
- **header value containment**: Checks if a specific HTTP header contains a particular substring. Assignable as an array with keys as header names and values as expected substrings.
- **request method**: Checks if the incoming request uses a specific HTTP method (e.g., GET, POST). Assignable as a string of HTTP methods.
- **payload origin match**: Checks if the payload origin matches a specific domain, IP address or URL. Assignable as an list array of string origins.

All checks are being handled by `\tei187\GitDisWebhook\Helpers\PlatformDetector` static class, which provides utility methods for evaluating the detection rules.

Example of GitHub service detection configuration:
```php
return [
    'registry' => [
        'github' => \tei187\GitDisWebhook\Services\GitHubService::class,
    ],
    'detectors' => [
        'github' => [
            'detection' => [
                'header existence' => [
                    'X-GitHub-Event',
                ],
                'header value match' => [
                    'User-Agent' => 'GitHub-Hookshot/*',
                ],
                'request method' => 'POST',
            ],
        ],
    ]
];
```

## Webhook detection
Webhook detection is managed by the `\tei187\GitDisWebhook\Factories\WebhookFactory` factory class. The factory determines which webhook profile to use for the current request based on the detection rules defined in the `config/webhooks.php` configuration file. The detection methods available for webhook detection are similar to those used for service detection, allowing for flexible and robust identification of the appropriate webhook profile.

Methods available for webhook detection include:
- **destination domain match**: Checks if the request origin matches a specific domain. Assignable as an array of string domains.
- **destination URL partial match**: Checks if the request origin matches a specific URL. Assignable as an array of string URLs.

All checks are being handled by `\tei187\GitDisWebhook\Helpers\WebhookDetector` static class, which provides utility methods for evaluating the detection rules.

Example of Discord webhook detection configuration:
```php
return [
    'registry' => [
        'discord' => \tei187\GitDisWebhook\ValueObjects\Webhooks\DiscordWebhook::class,
    ],
    'detectors' => [
        'discord' => [
            'detection' => [
                'destination domain match' => [
                    'discord.com',
                    'discordapp.com',
                ],
                'url' => [
                    'https://discord.com/api/webhooks/',
                    'https://discordapp.com/api/webhooks/',
                ]
            ],
        ],
    ]
];
```

## Profiles (no detection)
Profiles are pre-defined sets of configurations that bundle together service, webhook, payload, and message template settings. They allow for quick and consistent setup of webhook handling for specific use cases. Profiles can be defined in the `config/profiles.php` configuration file, where each profile can directly specify the service and webhook to be used, bypassing the automatic detection process. This may be useful for scenarios where a fixed configuration is required or when specific handling is necessary.

Example of a profile configuration with direct assignments, for Discord webhook and GitHub platform:
```php
return [
    'my_profile' => [
        'webhook' => [
            /* ... */
            'class' => \tei187\GitDisWebhook\Webhooks\DiscordWebhook::class, // direct assignment of webhook class
        ],
        'services' = [
            \tei187\GitDisWebhook\Services\GitHub\GitHubService::class, // direct assignment of service class
        ],
        /* ... */
    ],
];
```

**Caveat:** When using profiles with direct multiple service assignments, the detection is still happening, just limited to the pool of services defined in the profile. So if a service from outside of the pool matches the detection rules, it will not be selected.