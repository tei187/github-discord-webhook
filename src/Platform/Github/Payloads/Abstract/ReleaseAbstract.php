<?php

namespace tei187\GitDisWebhook\Platform\Github\Payloads\Abstract;

use tei187\GitDisWebhook\Payloads\Abstract\GitHubPayloadAbstract;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesRepo;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesSender;

/**
 * Provides an abstract base class for handling release-related payloads.
 *
 * This class extends the `PayloadAbstract`.
 * It defines the `$event` and `$subject` properties, and provides two static methods:
 * - `makeRelease()`: Creates a new release object from the provided decoded release data.
 * - `makeAuthor()`: Creates a new author object from the provided decoded release data.
 * 
 * @abstract
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesSender
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesRepo
 */
abstract class ReleaseAbstract extends GitHubPayloadAbstract {
    use PayloadUsesSender,
        PayloadUsesRepo;

    // designation
        protected  string $event   = 'release';
        protected ?string $subject = null;
    
    /**
     * Creates a new release object from the provided decoded release data.
     *
     * @param object $decoded The decoded release data.
     * @return \stdClass The release object with name, tag, description, URL, and prerelease properties.
     */
    protected static function makeRelease(object $decoded): \stdClass {
        $release = new \stdClass();
        $release->name       = $decoded->release->name;
        $release->tag        = $decoded->release->tag_name;
        $release->desc       = $decoded->release->body;
        $release->url        = $decoded->release->html_url;
        $release->prerelease = $decoded->release->prerelease;
            
        return $release;
    }

    /**
     * Creates a new author object from the provided decoded release data.
     *
     * @param object $decoded The decoded release data.
     * @return \stdClass The author object with name and URL properties.
     */
    protected static function makeAuthor(object $decoded): \stdClass {
        $author = new \stdClass();
        $author->name   = (string) $decoded->release->author->login;
        $author->url    = (string) $decoded->release->author->html_url;
        $author->avatar = (string) $decoded->release->author->avatar_url;

        return $author;
    }

    abstract protected function parse(string $payload): void;
}