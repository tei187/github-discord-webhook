<?php
include_once __DIR__ . '/../vendor/autoload.php';

// set root constant
const GHDWEBHOOK_ROOT = __DIR__ . '/../';

// load config
$config = new \tei187\GitDisWebhook\ValueObjects\Config();

// validate
try {
    $config->validate();
    echo "Configuration is VALID.\n";
    exit(0);
} catch (\Exception $e) {
    echo "Configuration is INVALID.\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}