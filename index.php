<?php

require_once __DIR__ . '/vendor/autoload.php';

use tei187\GithubDiscordWebhook\Factories\MessageFactory;
use tei187\GithubDiscordWebhook\Factories\PayloadFactory;
use tei187\GithubDiscordWebhook\Factories\WebhookFactory;

// instantiate PayloadAllowed with config values.
define('GHDWEBHOOK_ROOT', __DIR__ . '/');

// assign factories
$messageFactory = new MessageFactory();
$payloadFactory = new PayloadFactory();
$webhookFactory = new WebhookFactory();

// get the GitHub event and payload
$data  = file_get_contents('php://input');
$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? null;

// create the appropriate payload, webhook and message profiles
$payload = $payloadFactory->createPayload($event, $data);
$webhook = $webhookFactory->createWebhook($payload);
$message = $messageFactory->createMessage($event, $webhook);

// if all factories produced objects, send message
if ($payload && $webhook && $message) {
    $message->send();
}