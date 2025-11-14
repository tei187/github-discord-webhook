<?php

namespace tei187\GitDisWebhook\Platform\Github\Payloads\Abstract;

use tei187\GitDisWebhook\Payloads\Abstract\GitHubPayloadAbstract;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesPusher;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesRepo;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesSender;

/**
 * Represents an abstract class for handling branch-related payloads.
 * This class extends the `PayloadAbstract` class.
 *
 * @abstract
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesPusher
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesSender
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesRepo
 */
abstract class BranchAbstract extends GitHubPayloadAbstract {
    use PayloadUsesPusher,
        PayloadUsesSender,
        PayloadUsesRepo;
    
    // designation
        protected  string $event   = 'push';
        protected ?string $subject = 'branch';

    /**
     * Checks the action performed on the branch.
     *
     * @param object $decoded The decoded JSON payload.
     * @return self
     */
    protected function makeAction(object $decoded): ?string {
        $action = $decoded->created ? 'created' : null;
        $action = $decoded->deleted ? 'deleted' : $action;

        return $action;
    }

    abstract protected function parse(string $payload): void;
}