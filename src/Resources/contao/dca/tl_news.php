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
];

$dc['fields'] = array_merge($dc['fields'], $fields);
