<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotSocialStatsBundle extends Bundle
{
    public function getPath()
    {
        return \dirname(__DIR__);
    }
}
