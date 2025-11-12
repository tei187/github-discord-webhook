# Extending

In order to extend the functionality of GitDisWebhook, you can create custom services, webhooks, payloads, and message templates. This allows you to tailor the behavior of the application to fit your specific needs. This will require following on the interfaces and abstract classes provided by the application, ensuring that your custom implementations adhere to the expected structure and behavior.

## Service extension
In this application, a service constitutes an overall core logic for handling specific platforms or use-cases.

Creating a custom service involves implementing the `\tei187\GitDisWebhook\Interfaces\Service` interface or extending the `\tei187\GitDisWebhook\Services\Abstract\ServiceAbstract` abstract class. Your custom service should define how to handle incoming webhook requests, process payloads, and generate messages.

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
    final protected $name = "my_new_service";       // unique name for your service
    protected ?string $payloadFactoryClass = "..."; // set to your payload factory class
    protected ?string $messageFactoryClass = "..."; // set to your message factory class

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
}
```

## Payload extension
Payloads classes are responsible for parsing and extracting relevant information from incoming webhook data. You can use the generic payloads provided by the application, or create your own custom payload classes to handle specific data structures or just limiting the scope.

Creating a custom payload class involves implementing the `\tei187\GitDisWebhook\Interfaces\Payload` interface or extending the `\tei187\GitDisWebhook\Payloads\Abstract\PayloadAbstract` abstract class. Your custom payload should define how to parse and extract relevant information from the incoming request data, which will later be relayed to the message template.

Custom class should implement `parse()` method to define how to parse the incoming data and extract relevant information. This should be done carefully to ensure that the payload data is accurately represented and can be used effectively by the message templates. Also, `setEvent()` method has to be implemented in order to define the event path for the payload filtering in conjunction with message templates, followed with the getter method `getEvent()` to retrieve the event path in array form.

Steps to create a custom payload:
1. create a new class that implements the `Payload` interface and extends the `PayloadAbstract` class.
2. implement the `parse()`, `setEvent()`, and `getEvent()` methods.
3. add payload information to specific event path in `config/payloads.php`, `config/messages.php` and `config/allowed.php` configuration files to register your custom payload and define its behavior.

Template for a custom payload class:
```php
namespace tei187\GitDisWebhook\Payloads;
use tei187\GitDisWebhook\ValueObjects\Config;
use tei187\GitDisWebhook\Payloads\Abstract\PayloadAbstract;
use tei187\GitDisWebhook\Interfaces\PayloadInterface;

class NewPayload extends PayloadAbstract {

    public function __construct(?Config $config = null) {
        parent::__construct($config);
        // additional initialization if needed
    }

    public function parse(): void {
        // implement your parsing logic here
        // extract relevant information from $this->data and populate properties
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

Creating a custom webhook involves implementing the `\tei187\GitDisWebhook\Interfaces\Webhook` interface or extending the `\tei187\GitDisWebhook\Webhooks\Abstract\WebhookAbstract` abstract class. Your custom webhook should define how to send messages to the desired platform using the configured webhook URL.

Most setter and validation methods in the abstract webhook class have been declared as final to ensure consistent behavior across different webhook implementations. However, the `validateProfile()`, `validateSignature()`, and `send()` methods are left abstract and must be implemented in your custom webhook to define how to validate the webhook profile, verify the signature of incoming requests, and send messages to the desired platform. In some cases, profile and signature validation may not be necessary per se, and may just return `true`, but it's not advised. Sending method has to be implemented to define how to send the formatted message to the desired target platform.

Steps to create a custom webhook:
1. create a new class that implements the `Webhook` interface and extends the `WebhookAbstract` class.
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

Creating a custom message template involves implementing the `\tei187\GitDisWebhook\Interfaces\Message` interface or extending the `\tei187\GitDisWebhook\Messages\Abstract\MessageAbstract` abstract class. Your custom message template should define how to format and structure the notification message that will be sent to the desired platform.

To create a custom message template:
1. create a new class that implements the `Message` interface and extends the `MessageAbstract` class.
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