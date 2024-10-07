<?php

namespace tei187\GitDisWebhook\Enums;

/**
 * Represents various path extracts that can be obtained from a URL.
 * 
 * @see tei187\GitDisWebhook\Helpers\UrlParser
 */
enum PathExtracts: string {
    case WEBHOOK  = 'webhook';
    case DOMAIN   = 'domain';
    case PROTOCOL = 'protocol';
    case PORT     = 'port';
    case PATH     = 'path';
    case QUERY    = 'query';
    case FRAGMENT = 'fragment';
    case USER     = 'user';
    case PASS     = 'pass';
}
