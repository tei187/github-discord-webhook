<?php

require_once __DIR__ . '/vendor/autoload.php';

// define root path
define('GHDWEBHOOK_ROOT', __DIR__ . '/');

// instantiate config for dependency injection
$config = new \tei187\GitDisWebhook\ValueObjects\Config();

// instantiate worker and process the call
$worker = new \tei187\GitDisWebhook\ValueObjects\Worker($config);
$worker->processCall();