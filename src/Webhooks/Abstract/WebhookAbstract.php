<?php

namespace tei187\GitDisWebhook\Webhooks\Abstract;

use tei187\GitDisWebhook\Interfaces\WebhookInterface;
use tei187\GitDisWebhook\Interfaces\PayloadInterface;
use tei187\GitDisWebhook\Interfaces\ServiceInterface;
use tei187\GitDisWebhook\Traits\UsesMagicGetter;
use tei187\GitDisWebhook\ValueObjects\Config;

abstract class WebhookAbstract implements WebhookInterface {
    use UsesMagicGetter;

    protected Config $config;
    protected string $name;
    protected string $url;
    protected string $secret;
    protected array $repos = [];
    protected array $supportedServices = [];
    protected ?PayloadInterface $payload = null;

    public function __construct(string $name, Config|null $config = null, ?PayloadInterface $payload = null) {
        $this->name = $name;
        $this->setPayload($payload);

        // assign config
        if($config === null) {
            $this->config = new Config();
        } else {
            $this->config = $config;
        }

        $this->validateConfig();
    }
  
    final public function supportsService(ServiceInterface|string $service): bool {
        if($service instanceof ServiceInterface) {
            $service = $service::class;
        } elseif (is_string($service) && class_exists($service)) {
            // $service is already a class name
        } else {
            return false;
        }
        return empty($this->supportedServices) || 
               in_array($service, $this->supportedServices) && 
               is_subclass_of($service, ServiceInterface::class);
    }
    
    final public function supportsRepository(string $repository): bool {
        return empty($this->repos) || in_array($repository, $this->repos);
    }

    final public function setPayload(?PayloadInterface $payload): self {
        $this->payload = $payload;
        return $this;
    }

    final public function setConfig(Config $config): self {
        $this->validateConfig($config);
        return $this;
    }
    
    final protected function validateConfig(): void {
        $config = $this->config->profiles[$this->name];

        if (!isset($config['webhook']['url']) || !filter_var($config['webhook']['url'], FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid webhook URL');
        }
        if (!isset($config['webhook']['secret'])) {
            throw new \InvalidArgumentException('Webhook secret is required');
        }

        $this->url    = $config['webhook']['url'];
        $this->secret = $config['webhook']['secret'];
        $this->repos  = $config['repos'] ?? [];
        $this->supportedServices = $config['services'] ?? [];
    }

    abstract public function validateSignature(): bool;
}
