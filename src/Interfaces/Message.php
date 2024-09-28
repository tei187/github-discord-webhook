<?php
namespace tei187\GithubDiscordWebhook\Interfaces;

interface Message {
    public function send(): void;
}