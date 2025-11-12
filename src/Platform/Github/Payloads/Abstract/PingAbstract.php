<?php

namespace tei187\GitDisWebhook\Platform\Github\Payloads\Abstract;

use tei187\GitDisWebhook\Payloads\Abstract\GitHubPayloadAbstract;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesRepo;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesSender;

/**
 * Defines the base abstract class for handling 'ping' payloads in the GitDisWebhook package.
 * This class extends `PayloadAbstract`.
 * 
 * @abstract
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesSender
 */
abstract class PingAbstract extends GitHubPayloadAbstract {
    use PayloadUsesSender,
        PayloadUsesRepo;

    // designation
        protected  string $event   = 'ping';
        protected ?string $subject = null;
        protected ?string $action  = null;

    abstract protected function parse(string $payload): void;
}