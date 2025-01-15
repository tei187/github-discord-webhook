<?php

namespace tei187\GitDisWebhook\Payloads\Abstract;

use tei187\GitDisWebhook\Payloads\Abstract\PayloadAbstract;
use tei187\GitDisWebhook\Traits\PayloadUsesFork;
use tei187\GitDisWebhook\Traits\PayloadUsesRepo;

/**
 * Represents an abstract class for handling fork-related payloads.
 * This class extends `PayloadAbstract`.
 * 
 * @abstract
 * @uses \tei187\GitDisWebhook\Traits\PayloaddUsesFork
 * @uses \tei187\GitDisWebhook\Traits\PayloadUsesRepo
 */
abstract class ForkAbstract extends PayloadAbstract {
    use PayloadUsesFork,
        PayloadUsesRepo;

    // designation
        protected  string $event   = 'fork';
        protected ?string $subject = null;
        protected ?string $action  = null;
}