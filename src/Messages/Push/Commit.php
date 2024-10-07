<?php

namespace tei187\GitDisWebhook\Messages\Push;

use tei187\GitDisWebhook\Messages\MessageAbstract;

class Commit extends MessageAbstract {
    
    /**
     * Generates a message for a push event on a GitHub repository.
     *
     * The message includes information about the repository, branch, pusher, and a list of the new commits.
     * 
     * @return void
     */
    protected function create(): void {
        $commits_count = count($this->webhook->payload->commits);
        $pushType = ($this->webhook->payload->forced ? "**forced** " : "") . "push";

        $message = "New {$pushType} to **[{$this->webhook->payload->repo->fullname}](https://github.com/{$this->webhook->payload->repo->fullname})**"
                 . " on branch **{$this->webhook->payload->branch}**"
                 . " by *[{$this->webhook->payload->pusher->name}](https://github.com/{$this->webhook->payload->pusher->name})*\n"
                 . "{$commits_count} new commit(s):\n";
                
        foreach ($this->webhook->payload->commits as $commit) {
            $short_hash = substr($commit->id, 0, 7);
            $message .= "> - [{$short_hash}]({$commit->url}) {$commit->message}\n";
        }

        $this->message = $message;
    }
}
