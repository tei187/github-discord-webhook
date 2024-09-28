# Github-Discord Webhook

This project implements a webhook integration between GitHub and Discord. It allows to handle multiple webhooks for different repositories and Discord channels, ran from the same script.

## Features

- Seamless integration between GitHub and Discord
- Customizable notifications for different GitHub events
- Easy setup and configuration

## Setup
1. Register  a new webhook in your Discord server. You can find more information on how to do this [here](https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks).
2. Configure your webhook in the `config\webhooks.php` file.
    ```php
    <?php
    
    return [
        // webhook name that will be used in payload URL in GitHub
        'sample-webhook' => [
            // Discord webhook URL
            'url' => 'https://discord.com/api/webhooks/(...)',
            // GitHub secret key
            'secret' => 'your-github-secret-key',
            // Array of repositories to filter through
            // May be left empty to listen to all repositories
            'repos' => [ 'username/repository-name' ],
        ]
    ];
    ```
3. Set up rest of the config files (or if you want to run default, just remove the `.example` extensions).
4. Register a new webhook in your GitHub repository. You can find more information about registering webhooks [here](https://docs.github.com/en/developers/webhooks-and-events/webhooks/creating-webhooks).
    1. Enter your payload URL. If you have this package hosted i.e. under `http://example.com/webhook`, then the payload URL should be ```http://example.com/webhook/sample-webhook```.
    2. Select ```"application/json"``` as the content type. 
    3. Pick you secret and make sure to copy it somewhere, alternatively update the 'secret' key in `config\webhooks.php`.
    4. Alternatively pick which events you want to listen to.
5. Believe it or not, that's it.

## More settings
All configuration options are located in `config` directory. There you can set up classes for handlinng messages, payloads and webhooks. Also, there's a configuration file setting allowed events and actions.


## Usage

Once set up, you'll receive notifications in your Discord server for events concerning:
- Commits
- Tags
- Releases
- Branches

Future plans include:
- Forks
- Pull requests
- Issues
- Comments
- And more!

## License

This project is licensed under the MIT License - see the LICENSE file for details.