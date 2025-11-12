<?php

namespace tei187\GitDisWebhook\Platform\Github\Payloads\Abstract;

use tei187\GitDisWebhook\Payloads\Abstract\GitHubPayloadAbstract;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesFork;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesRepo;

/**
 * Represents an abstract class for handling fork-related payloads.
 * This class extends `PayloadAbstract`.
 * 
 * @abstract
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesFork
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesRepo
 */
abstract class ForkAbstract extends GitHubPayloadAbstract {
    use PayloadUsesFork,
        PayloadUsesRepo;

    // designation
        protected  string $event   = 'fork';
        protected ?string $subject = null;
        protected ?string $action  = null;

    abstract protected function parse(string $payload): void;
}