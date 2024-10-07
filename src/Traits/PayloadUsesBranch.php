<?php

namespace tei187\GitDisWebhook\Traits;

/**
 * Provides functionality for working with the Git branch associated with a GitHub webhook request.
 */
trait PayloadUsesBranch {
    /**
     * @var string The name of the Git branch associated with the GitHub webhook request.
     */
    protected string $branch;

    /**
     * Checks the branch name to which the commit was done.
     *
     * @param object $decoded The decoded payload data.
     * @return ?string
     */
    public static function makeBranch(object $decoded): ?string {
        return explode('/', $decoded->ref)[2] ?: null;
    }
}