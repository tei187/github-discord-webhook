<?php

namespace tei187\GitDisWebhook\Payloads\Abstract;

use tei187\GitDisWebhook\Payloads\Abstract\PayloadAbstract;
use tei187\GitDisWebhook\Traits\PayloadUsesSender;

abstract class ReleaseAbstract extends PayloadAbstract {
    use PayloadUsesSender;

    // designation
        protected  string $event   = 'release';
        protected ?string $subject = null;
    
    /**
     * Creates a new release object from the provided decoded release data.
     *
     * @param object $decoded The decoded release data.
     * @return \stdClass The release object with name, tag, description, URL, and prerelease properties.
     */
    static function makeRelease(object $decoded): \stdClass {
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
    static function makeAuthor(object $decoded): \stdClass {
        $author = new \stdClass();
        $author->name   = (string) $decoded->release->author->login;
        $author->url    = (string) $decoded->release->author->html_url;
        $author->avatar = (string) $decoded->release->author->avatar_url;

        return $author;
    }
}