# Contao Social Stats Bundle

This bundle collects data about news entries. Currently it contains a command to update some stats for news articles from some networks.

## Features
- following stats are collected:
  - Google Analytics: Unique page view
  - Facebook: share count
  - Matomo: hits

## Setup

### Requirements
- PHP 7.2 or higher (not tested with 8.0 and higher)
- Contao 4.4 or higher

Additional:
- Google Analytics: [Google APIs Client Library for PHP
  ](https://github.com/googleapis/google-api-php-client)

### Install 
1. Install with composer or contao manager

       composer require heimrichhannot/contao-social-stats-bundle

2. Updated database

       php vendor/bin/contao-console contao:migrate

3. Setup a cronjob for SocialStatsCommand (see Usage -> Command for more information)

       * */1 * * * php vendor/bin/contao-console huh:socialstats:update

### Configuration

Most platforms neeed additions configurtation like access tokens. See configuration reference about what you need. 

## Usage

### Command 

```
Usage:
  huh:socialstats:update [options]

Options:
  -p, --platforms[=PLATFORMS]  Limit to specific platform/network. See help for more information.
  -l, --limit=LIMIT            Limit the number of news article to update. [default: 20]
  -a, --age=AGE                Limit the age of articles to be updated to a number of days. 0 means no limit. [default: 0]
      --pid=PID                Limit the news articles to given archives. 0 means all archives. [default: 0]
  -h, --help                   Display this help message
  -q, --quiet                  Do not output any message
  -V, --version                Display this application version
      --ansi                   Force ANSI output
      --no-ansi                Disable ANSI output
  -n, --no-interaction         Do not ask any interactive question
  -e, --env=ENV                The Environment name. [default: "prod"]
      --no-debug               Switches off debug mode.
  -v|vv|vvv, --verbose         Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  This command updates the social statistics of your news entries.
  
  Following options are available for platforms option:
    fb - Facebook
    ga - Google Analytics
    ma - Matomo
```

### Work with the values

The values collected by the command are written into the `huh_socialstats_values` field of tl_news. You get them as array after a simple `\Contao\StringUtil::deserialze($newsItemModel->huh_socialstats_values, true)`. The time of the last update of the stats is written into `tl_news.huh_socialstats_last_updated`.

### Google Analytics
We use the [Google APIs Client Library for PHP
](https://github.com/googleapis/google-api-php-client) to obtain the values. For this to work you need:

- a Google API Console project
- a key file
- a view id

Here you find all informations how to gain these components: https://developers.google.com/analytics/devguides/reporting/core/v4/quickstart/web-php 

## Configuration reference

```yaml
# Default configuration for extension with alias: "huh_social_stats"
huh_social_stats:

    # Override the auto-determined base url.
    base_url:             null

    # The start date from which data should be counted. Needed for analytics services like matomo or google analytics. Default values is 2005-01-01 as timestamp.
    start_date:           1104534000
    matomo:

        # Set the matomo url
        url:                  ~

        # The matomo authorization token
        token:                ~
    facebook:

        # The facebook app id.
        app_id:               null

        # The facebook app secret.
        app_secret:           null
    google_analytics:

        # View ID
        view_id:              ~

        # Relative path to the google analytics keyfile.
        key_file:             files/bundles/huh_social_stats/google_analytics/privatekey.json
```