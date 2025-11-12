<?php

namespace tei187\GitDisWebhook\Platform\Github\Payloads\Abstract;

use tei187\GitDisWebhook\Payloads\Abstract\GitHubPayloadAbstract;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesBranch;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesPusher;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesRepo;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesSender;

/**
 * Represents an abstract class for handling commit-related payloads from a GitHub webhook.
 * This class extends `PayloadAbstract`.
 *
 * The `$commits` property holds an array of the commits associated with the GitHub webhook request.
 * The `$forced` property indicates whether the push was a forced push.
 * 
 * @abstract
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesBranch
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesPusher
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesSender
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesRepo
 */
abstract class CommitAbstract extends GitHubPayloadAbstract {
    use PayloadUsesPusher,
        PayloadUsesSender,
        PayloadUsesBranch,
        PayloadUsesRepo;

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

    abstract protected function parse(string $payload): void;
}