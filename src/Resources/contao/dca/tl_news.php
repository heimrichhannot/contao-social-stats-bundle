<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

$dc = &$GLOBALS['TL_DCA']['tl_news'];

/**
 * Fields.
 */
$fields = [
    'huh_socialstats_last_updated' => [
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ],
    'huh_socialstats_values' => [
        'sql' => 'blob NULL',
    ],

    'facebook_counter' => [
        'sql' => ['type' => 'integer', 'default' => '0'],
    ],
    'facebook_updated_at' => [
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ],
    'twitter_counter' => [
        'sql' => ['type' => 'integer', 'default' => '0'],
    ],
    'twitter_updated_at' => [
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ],
    'google_plus_counter' => [
        'sql' => ['type' => 'integer', 'default' => '0'],
    ],
    'google_plus_updated_at' => [
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ],
    'disqus_counter' => [
        'sql' => ['type' => 'integer', 'default' => '0'],
    ],
    'disqus_updated_at' => [
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ],
    'google_analytic_counter' => [
        'sql' => ['type' => 'integer', 'default' => '0'],
    ],
    'google_analytic_updated_at' => [
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ],
];

$dc['fields'] = array_merge($dc['fields'], $fields);
