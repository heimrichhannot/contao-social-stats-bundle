<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle;

use HeimrichHannot\SocialStatsBundle\DependencyInjection\HeimrichHannotSocialStatsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotSocialStatsBundle extends Bundle
{
    public function getPath()
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension()
    {
        return new HeimrichHannotSocialStatsExtension();
    }
}
