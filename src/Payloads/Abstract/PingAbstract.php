<?php

namespace tei187\GitDisWebhook\Payloads\Abstract;

use tei187\GitDisWebhook\Traits\PayloadUsesSender;

abstract class PingAbstract extends PayloadAbstract {
    use PayloadUsesSender;

    // designation
        protected  string $event   = 'ping';
        protected ?string $subject = null;
        protected ?string $action  = null;
}