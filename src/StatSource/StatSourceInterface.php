<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\StatSource;

interface StatSourceInterface
{
    public static function getName(): string;

    public function updateItem(StatSourceItem $item, array &$data): StatSourceResult;
}
