# Extending

In order to extend the functionality of GitDisWebhook, you can create custom services, webhooks, payloads, and message templates. This allows you to tailor the behavior of the application to fit your specific needs. This will require following on the interfaces and abstract classes provided by the application, ensuring that your custom implementations adhere to the expected structure and behavior.

## Service extension
In this application, a service constitutes an overall core logic for handling specific platforms or use-cases.

Creating a custom service involves implementing the `\tei187\GitDisWebhook\Interfaces\ServiceInterface` interface and extending the `\tei187\GitDisWebhook\Services\Abstract\ServiceAbstract` abstract class. Your custom service should define how to handle incoming webhook requests, process payloads, and generate messages.

The abstract for the service class provides several methods that can be overridden to customize the behavior of your service. As far as typical setters are concerned, for payload, message, webhook and names, as well as producing payload and messages through factories, tehse methods have been declared as final in order to prevent unintended overrides that could lead to inconsistent behavior. The `validateEvent()` method is unlikely to be changed, but is left public for abstraction if needed. However, the `validatePayload()` method is abstract and must be implemented in your custom service to define how to validate incoming payloads. This is due to difference in structure and requirements for calls of different platforms and use-cases.

That being said, the easiest way to create a custom service is to:
1. extend the `ServiceAbstract` class.
2. implement the `validatePayload()` method to define how to validate incoming payloads.
3. set default properties of `payloadFactoryClass` and `messageFactoryClass` to the desired factory classes for creating payloads and messages.
4. adding to configuration arrays, such as `config/services.php` (required), to register your custom service and define its behavior.

Template for a custom service class:
```php
namespace tei187\GitDisWebhook\Services;

use tei187\GitDisWebhook\ValueObjects\Config;
use tei187\GitDisWebhook\Services\Abstract\ServiceAbstract;
use tei187\GitDisWebhook\Interfaces\PayloadInterface;
use tei187\GitDisWebhook\Interfaces\WebhookInterface;
use tei187\GitDisWebhook\Interfaces\MessageInterface;

class NewService extends ServiceAbstract {
    final protected $name = "my_new_service";      // unique name for your service, as will be defined as a key in service configs
    protected ?string $payloadClass = null;        // set to your payload class if it is a one-fit-all structure (not using payload factory)
    protected ?string $payloadFactoryClass = null; // set to your payload factory class
    protected ?string $messageClass = null;        // set to your message class if it is a one-fit-all structure (not using message factory)
    protected ?string $messageFactoryClass = null; // set to your message factory class
    // factory properties are optional, as you can also set payload and message through setters
    // or within the constructor

    public function __construct(?Config $config = null, ?PayloadInterface $payload = null, 
                                ?MessageInterface $message = null, ?WebhookInterface $webhook = null)
    {
        parent::__construct($config, $payload, $message, $webhook);

        // additional initialization if needed
    }

    public function validatePayload(): bool {
        // implement your payload validation logic here
        return true; // or false based on validation
    }

    public function validateSignature(): bool {
        // implement your signature validation logic here if needed
        return true; // or false based on validation
    }

    // additionally, if your service/platform supports repository and branch use:
    // (only if you plan to use repository/branch filtering per profile)

    public function getRepositoryName(): ?string {
        // implement your logic to extract repository name from payload if needed
        return null; // or the repository name
    }

    public function getRepositoryBranch(): ?string {
        // implement your logic to extract repository platform from payload if needed
        return null; // or the repository platform
    }
}
```

## Payload extension
Payloads classes are responsible for parsing and extracting relevant information from incoming webhook data. You can use the generic payloads provided by the application, or create your own custom payload classes to handle specific data structures or just limiting the scope.

Creating a custom payload class involves implementing the `\tei187\GitDisWebhook\Interfaces\PayloadInterface` interface and extending the `\tei187\GitDisWebhook\Payloads\Abstract\PayloadAbstract` abstract class. Your custom payload should define how to parse and extract relevant information from the incoming request data, which will later be relayed to the message template.

Custom class should implement `parse()` method to define how to parse the incoming data and extract relevant information. This should be done carefully to ensure that the payload data is accurately represented and can be used effectively by the message templates. Also, `setEvent()` method has to be implemented in order to define the event path for the payload filtering in conjunction with message templates, followed with the getter method `getEvent()` to retrieve the event path in array form.

Steps to create a custom payload:
1. create a new class that extends the `PayloadAbstract` class.
2. implement the `parse()`, `setEvent()`, and `getEvent()` methods.
3. add payload information to specific event path in `config/payloads.php`, `config/messages.php` and `config/allowed.php` configuration files to register your custom payload and define its behavior.

Template for a custom payload class:
```php
namespace tei187\GitDisWebhook\Payloads;
use tei187\GitDisWebhook\Payloads\Abstract\PayloadAbstract;
use tei187\GitDisWebhook\Interfaces\PayloadInterface;

class NewPayload extends PayloadAbstract {

    public function __construct(?string $payload = null) {
        parent::__construct($payload);
        // additional initialization if needed
    }

    public function parse(): void {
        // implement your parsing logic here
        // extract relevant information from $this->parsed or otherwise
    }

    public function setEvent(): self {
        // implement your event setting logic here
        return $this;
    }

    public function getEvent(): array {
        // implement your event getting logic here
        return [];  // return the event path as an array
    }
}
```

## Webhook extension
Webhooks classes are responsible for verifying the payload authenticity and sending the formatted messages to the desired platform.

Creating a custom webhook involves implementing the `\tei187\GitDisWebhook\Interfaces\WebhookInterface` interface and extending the `\tei187\GitDisWebhook\Webhooks\Abstract\WebhookAbstract` abstract class. Your custom webhook should define how to send messages to the desired platform using the configured webhook URL.

Most setter and validation methods in the abstract webhook class have been declared as final to ensure consistent behavior across different webhook implementations. However, the `validateProfile()`, `validateSignature()`, and `send()` methods are left abstract and must be implemented in your custom webhook to define how to validate the webhook profile, verify the signature of incoming requests, and send messages to the desired platform. In some cases, profile and signature validation may not be necessary per se, and may just return `true`, but it's not advised. Sending method has to be implemented to define how to send the formatted message to the desired target platform.

Steps to create a custom webhook:
1. create a new class that extends the `WebhookAbstract` class.
2. implement `validateProfile()`, `validateSignature()` and `send()` methods.
3. add webhook information to `config/webhooks.php` configuration file to register your custom webhook and define its behavior and detection rules.

Template for a custom webhook class:
```php
namespace tei187\GitDisWebhook\Webhooks;

use tei187\GitDisWebhook\Webhooks\Abstract\WebhookAbstract;
use tei187\GitDisWebhook\Interfaces\PayloadInterface;
use tei187\GitDisWebhook\ValueObjects\Config;

class NewWebhook extends WebhookAbstract {

    public function __construct(string $name, ?Config $config = null, ?PayloadInterface $payload = null) {
        parent::__construct($name, $config, $payload);
        // additional initialization if needed
    }

    public function validateProfile(): bool {
        // implement your profile validation logic here
        return true; // or false based on validation
    }

    public function validateSignature(): bool {
        // implement your signature validation logic here
        return true; // or false based on validation
    }

    public function send(string $message): bool {
        // implement your sending logic here
        return true; // or false based on sending result
    }
}
```

## Message extension
Message templates are responsible for formatting and structuring the notification messages that will be sent to the desired platform, based on the parsed payload data.

Creating a custom message template involves implementing the `\tei187\GitDisWebhook\Interfaces\MessageInterface` interface and extending the `\tei187\GitDisWebhook\Messages\Abstract\MessageAbstract` abstract class. Your custom message template should define how to format and structure the notification message that will be sent to the desired platform.

To create a custom message template:
1. create a new class that extends the `MessageAbstract` class.
2. implement the `create()` method, which will contain the logic to format and structure the notification message based on the payload data.

This application also includes Markdown helper utility that can be used within your custom message templates to assist with formatting messages in Markdown syntax. This can help ensure that your messages are well-structured and visually appealing when sent to platforms that support Markdown. Look up the `tei187\GitDisWebhook\Helpers\Markdown` class for available methods and usage examples.

Template for a custom message template class:
```php
namespace tei187\GitDisWebhook\Messages;

use tei187\GitDisWebhook\Messages\Abstract\MessageAbstract;

class NewMessage extends MessageAbstract {
    public function create(): void {
        $message = "";
        // implement your message creation logic here
        // format and structure the notification message based on $this->payload
        $this->message = $message;
    }
}
```

# Extension example

Lets say you want to create a service for a simple payload and send a noticifcation to a Discord webhook. For this example, we will assume that there is no specific event coincided with this platform, and the payload structure is as follows:
```json
{
    "checksum": "abc123",
    "author": "user123",
    "message": "This is a simple message."
}
```

Given that, we can create the custom payload class as follows:
```php
namespace tei187\GitDisWebhook\Payloads;
use tei187\GitDisWebhook\ValueObjects\Config;
use tei187\GitDisWebhook\Payloads\Abstract\PayloadAbstract;
use tei187\GitDisWebhook\Interfaces\PayloadInterface;

class SimplePayload extends PayloadAbstract {

    protected ?string $event = "*"; // default event name for this payload, as there is no event specificity
    protected object ;
    protected ?string $message;
    protected ?string $checksum;

    public function __construct(?string $payload = null) {
        parent::__construct($payload);
    }

    public function parse(string $payload): void {
        // assign plain payload string
        $this->plain = $payload;

        // parse JSON payload into associative array
        // and extract relevant information
        $data = json_decode($payload, true);
        $this->author = $data['author'] ?? null;
        $this->message = $data['message'] ?? null;
        $this->checksum = $data['checksum'] ?? null;
    }

    public function setEvent(): self {
        $this->event = "*";
        return $this;
    }

    public function getEvent(): array {
        return [ $this->event ]; // return the event path as an array
    }
}
```

Next, we can create the custom service class, to handle this payload platform:
```php
namespace tei187\GitDisWebhook\Services;

use tei187\GitDisWebhook\ValueObjects\Config;
use tei187\GitDisWebhook\Services\Abstract\ServiceAbstract;
use tei187\GitDisWebhook\Interfaces\PayloadInterface;
use tei187\GitDisWebhook\Interfaces\WebhookInterface;
use tei187\GitDisWebhook\Interfaces\MessageInterface;

class SimpleService extends ServiceAbstract {
    final protected $name = "simple_json";
    protected ?string $payloadClass = \tei187\GitDisWebhook\Payloads\SimplePayload::class; // using our custom payload class
    protected ?string $messageClass = \tei187\GitDisWebhook\Messages\NewMessage::class; // using our custom message class (implemented below)

    public function __construct(?Config $config = null, ?PayloadInterface $payload = null, 
                                ?MessageInterface $message = null, ?WebhookInterface $webhook = null)
    {
        parent::__construct($config, $payload, $message, $webhook);
    }

    public function validatePayload(): bool {
        // we will just check if required fields are present
        if (empty($this->payload->author) || empty($this->payload->message)) {
            return false;
        }
        return true;
    }

    public function validateSignature(): bool {
        // we will verify the cheksum from payload
        // for this example, let's assume we have a predefined expected checksum
        $expectedChecksum = "abc123"; // example expected checksum
        if ($this->payload->checksum !== $expectedChecksum) {
            return false;
        }
        return true;
    }

    public function getRepositoryName(): ?string {
        // this payload does not have repository information
        return null;
    }

    public function getRepositoryBranch(): ?string {
        // this payload does not have branch information
        return null;
    }
}
```

Having both new service and payload, we can add them to configuration files accordingly:
- `config/services.php`
    ```php
    // ...
    'registry' => [
        'simple_json' => \tei187\GitDisWebhook\Services\SimpleService::class,
        // other services...
    ],
    'detectors' => [
        'simple_json' => [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'method' => 'POST',
            'origin' => [
                'example.com',
            ],
        ],
    ],
    // ...
    ```

- `config/payloads.php`
    ```php
    // ...
        'simple_service' => [
            '*' => \tei187\GitDisWebhook\Payloads\SimplePayload::class,
        ],
    // ...
    ```

Finally, we can create the custom message template class:
```php
namespace tei187\GitDisWebhook\Messages;

use tei187\GitDisWebhook\Messages\Abstract\MessageAbstract;

class NewMessage extends MessageAbstract {
    public function create(): void {
        $this->message = "**New Message from {$this->payload->author}**\n\n"
                       . "{$this->payload->message}";
    }
}
```

...and then adding it to configuration, finishing with creating a profile to use this service and webhook:

- `config/messages.php`
    ```php
    // ...
        'simple_service' => [
            '*' => \tei187\GitDisWebhook\Messages\NewMessage::class
        ],
    // ...
    ```

- `config/profiles.php`
    ```php
    // ...
        'simple_profile' => [
            'webhook' => [
                'url' => "https://discord.com/api/webhooks/your_webhook_url",
                'secret' => "your_webhook_secret",
                'class' => \tei187\GitDisWebhook\Webhooks\DiscordWebhook::class
            ],
            'services' => [
                'simple_json',
            ],
        ],
    // ...
    ```

This will wrap it up for creating a custom service, payload, message template, and configuring them to work together within the application. You can now handle incoming webhook requests with your custom logic and send formatted notifications to your Discord platform.