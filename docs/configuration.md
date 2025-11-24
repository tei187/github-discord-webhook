# Configuration

Description of how to configure the application. It allows users to customize settings for services, webhooks, profiles, messages, and allowed events, as well as limit the scope of notifications to specific repositories and branches per profile.

---

## Types

### Allowed
`config/allowed.php`

This file specifies which events are allowed for each service. It holds an associative array where each key is a service name, and the corresponding value is an array of event types that are permitted for that service. Used for filtering out unwanted noisy events early in the process.

```php
// ...
    'service_name' => [
        'event_group' => [
            'event_subgroup' => [
              'event_final' => true,
            ]
            // ...
        ],
        // ...
    ],
// ...
```

### Messages
`config/messages.php`

This file defines message templates used for notifications sent by the application. Each template is associated with a specific event type and contains placeholders that can be dynamically replaced with actual data when the message is generated. Templates are grouped by service names, then by event types, ending with applicable message class name.

```php
// ...
    'service_name' => [
        'event_group' => [
            'event_subgroup' => [
              'event_final' => \Some\Message\Class::class,
            ]
            // ...
        ],
        // ...
    ],
// ...
```

### Payloads
`config/payloads.php`

This file configures how different payloads from various services are handled. Each service can have its own set of rules for validating and processing incoming payloads. These tend to be dependable on specific event that causes the call, due to which these are grouped by event types.

Routing starts with the identifier of the service, then nested event type, ending with applicable payload class name.

```php
// ...
    'service_name' => [
        'event_group_1' => [
            'event_subgroup' => [
              'event_final' => \Some\Payload\Class::class,
            ]
            // ...
        ],
        'event_group_2' => [
            '*' => \Some\Payload\GenericClass::class,
        ],
        // ...
    ],
// ...
```

### Profiles
`config/profiles.php`

This file defines profiles that group services and webhooks together. Each profile specifies which services it supports and which webhooks it uses for notifications. This allows merging single webhook handler with multiple services/platforms. It also allows additional filtering as well as direct mappings.

Each profile is defined with a unique name, which will also be the end-point of URL the platform sends requests to. These will have an array with keys: 
- `webhook`:
    - `url`: (required) targeted URL.
    - `secret`: (you may think it's "optional", but you're wrong about it) used for validating payloads.
    - `class`: (optional) a direct webhook class to use (otherwise it will try to automatically detect it through domain or link subpath).
- `services`: (optional) an array of service class names that the profile supports. If left empty or if it contains `*`, all detectable services are supported.
- `repos`: (optional) an associative array mapping services/platforms to arrays of repository names. If left empty, all validated payloads from all repositories will be accepted. For GitHub, you can also specify branches using `author/repo:branch` or `author/repo@branch` format, not case-sensitive.

```php
// ...
    'profile_name' => [
        'webhook' => [
            'url' => 'https://example.com/webhook',
            'secret' => 'your_secret_key',
            'class' => \Some\Webhook\Class::class, // optional
        ],
        'services' => [
            \Some\Service\Class::class,
            // ...
        ],
    ],
// ...
```

### Services
`config/services.php`

This file defines the services that the application can detect and handle. Each service is associated with a set of rules that determine how incoming requests are matched to the service.

It holds an array with two main keys: `registry` and `detectors`.

- `registry`: This key maps service names to their corresponding service class implementations. Keys have to be unique and should match the service names used in profiles.
- `detectors`: This key contains an array of rules for detecting services based on incoming HTTP requests. Each service has its own set of rules, which may include:
  - `headers`: An associative array of required headers that must be present in the request.
  - `method`: The HTTP request method (e.g., POST, GET).
  - `origin`: An array of allowed origin domains.
  - `values_contain`: An associative array of header values that must contain specific substrings.
  - `values_match`: An associative array of header values that must match exactly.

```php
// ...
    'registry' => [
        'service_name' => \Some\Service\Class::class,
        // ...
    ],
    'detectors' => [
      'service_name' => [
          'headers' => [
              'HTTP_HEADER_NAME' => 'ExpectedValue',
              // ...
          ],
          'method' => 'POST',
          'origin' => [
              'allowed-domain.com',
              // ...
          ],
          'values_contain' => [
              'HTTP_USER_AGENT' => 'SomeSubstring',
              // ...
          ],
          'values_match' => [
              'HTTP_X_EVENT_TYPE' => 'specific_event',
              // ...
          ],
      ],
      // ...
    ],
// ...
```

### Webhooks
`config/webhooks.php`

This file configures the webhooks that the application will use to send notifications. Each webhook is defined with a unique name and corresponding class name. For example, the `discord` webhook is associated with the `tei187\GitDisWebhook\Webhooks\DiscordWebhook` class.

It holds an array with two main keys: `registry` and `detectors`.

- `registry`: This key maps webhook names to their corresponding class implementations. Keys have to be unique and should match the webhook names used in profiles.
- `detectors`: This key contains an array of rules for detecting webhooks based on target URL. Each webhook has its own set of rules, which may include:
  - `domain`: An array of allowed target domains.
  - `url`: An array of allowed target URLs to match against.

Detectors are used to automatically identify the appropriate service or webhook based on the characteristics of incoming requests or target URLs.

```php
// ...
    'registry' => [
        'webhook_name' => \Some\Webhook\Class::class,
        // ...
    ],
    'detectors' => [
      'webhook_name' => [
          'domain' => [
              'allowed-domain.com',
              // ...
          ],
          'url' => [
              'https://allowed-domain.com/specific-path',
              // ...
          ],
      ],
      // ...
    ],
// ...
``` 

---

## Configuration instance

Configuration is being injected throughout the process lifecycle. The main configuration files are located in the `config/` directory. Each configuration file serves a specific purpose and is structured to facilitate easy management of settings related to services, webhooks, profiles, messages, and allowed events. It is compacted into a single configuration object that is passed around to various components that require access to these settings, instance of `tei187\GitDisWebhook\ValueObjects\Config`. This class has a method for validating the configuration data, ensuring that all required fields are present and correctly formatted. If any issues are found during validation, appropriate exceptions are thrown to alert the developer of misconfigurations. For further details, use `validate()` method of your configuration instance.