# Contao Social Stats Bundle

This bundle collects data about news entries.

## Setup

### Requirements
- PHP 7.2 or higher (not tested with 8.0 and higher)
- Contao 4.4 or higher

Additional:
- Google Analytics: [Google APIs Client Library for PHP
  ](https://github.com/googleapis/google-api-php-client)

### Google Analytics
We use the [Google APIs Client Library for PHP
](https://github.com/googleapis/google-api-php-client) to obtain the values. For this to work you need:

- a Google API Console project
- a key file
- a view id

Here you find all informations how to gain these components: https://developers.google.com/analytics/devguides/reporting/core/v4/quickstart/web-php 