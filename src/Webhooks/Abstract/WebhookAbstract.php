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
  
    /**
     * @final
     */
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
    
    final public function supportsRepository(string $serviceName, ?string $repository = null, ?string $branch = null): bool {
        $pool = $this->config->profiles[$this->name]['repos'][$serviceName] ??= [];

        // if pool is empty or contains '*', allow all, or not respository specified
        if (empty($pool) || in_array('*', $pool)) {
            return true;
        }

        $variants = [];
        // if repository is specified, add applicable repository variants
        if($repository) {
            $variants = array_merge($variants, [
                $repository,
                implode(":" , [$repository, "*"]),
                implode("@" , [$repository, "*"]),
            ]);

            // additionally, if branch is specified, add repository:branch and repository@branch variants
            if($branch) {
                $variants = array_merge($variants, [
                    implode(":" , [$repository, $branch]),
                    implode("@" , [$repository, $branch]),
                ]);
            }
        }

        // map pool and variants to lowercase for case-insensitive comparison
        $pool = array_map('strtolower', $pool);
        $variants = array_map('strtolower', $variants);

        // check if any variant is in the pool
        foreach ($variants as $variant) {
            if (in_array($variant, $pool)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @final
     */
    final public function setPayload(?PayloadInterface $payload): self {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @final
     */
    final public function setConfig(Config $config): self {
        $this->validateConfig($config);
        return $this;
    }
    
    /**
     * @final
     */
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
