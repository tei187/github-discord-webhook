<?php

namespace tei187\GitDisWebhook\Payloads\Abstract;

use tei187\GitDisWebhook\Traits\PayloadUsesSender;

/**
 * Defines the base abstract class for handling 'ping' payloads in the GitDisWebhook package.
 * This class extends `PayloadAbstract`.
 * 
 * @abstract
 * @uses \tei187\GitDisWebhook\Traits\PayloadUsesSender
 */
abstract class PingAbstract extends PayloadAbstract {
    use PayloadUsesSender;

    // designation
        protected  string $event   = 'ping';
        protected ?string $subject = null;
        protected ?string $action  = null;
}