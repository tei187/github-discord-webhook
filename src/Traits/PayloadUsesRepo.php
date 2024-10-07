<?php

namespace tei187\GitDisWebhook\Traits;

/**
 * Provides functionality for working with GitHub repository information extracted from a webhook payload.
 */
trait PayloadUsesRepo {
    /**
     * @var \stdClass The name of the GitHub repository that triggered the webhook. 
     *                If the payload is parsed, it will have the following properties: `name`, `fullname`, `url`.
     */
    protected object $repo;

    /**
     * Creates a new repository object from the provided decoded payload.
     *
     * @param object $decoded The decoded payload object.
     * @return \stdClass The repository object.
     */
    static public function makeRepo(object $decoded): \stdClass {
        $repo = new \stdClass();
        $repo->name     = (string) $decoded->repository->name;
        $repo->fullname = (string) strtolower($decoded->repository->full_name);
        $repo->url      = (string) $decoded->repository->html_url;
        
        return $repo;
    }
}