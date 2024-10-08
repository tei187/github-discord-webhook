<?php

namespace tei187\GitDisWebhook\Payloads\Abstract;

use tei187\GitDisWebhook\Payloads\Abstract\PayloadAbstract;
use tei187\GitDisWebhook\Traits\PayloadUsesBranch;
use tei187\GitDisWebhook\Traits\PayloadUsesPusher;
use tei187\GitDisWebhook\Traits\PayloadUsesSender;

abstract class CommitAbstract extends PayloadAbstract {
    use PayloadUsesPusher,
        PayloadUsesSender,
        PayloadUsesBranch;

    /**
     * @var array The commits associated with the GitHub webhook request.
     */
    protected array $commits;

    /**
     * @var bool Indicates whether the push was a forced push.
     */
    protected bool $forced;

    // designation
        protected  string $event   = 'push';
        protected ?string $subject = 'commit';
        protected ?string $action  = null;
}