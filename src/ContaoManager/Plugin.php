<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\NewsBundle\ContaoNewsBundle;
use HeimrichHannot\SocialStatsBundle\HeimrichHannotSocialStatsBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(HeimrichHannotSocialStatsBundle::class)->setLoadAfter([
                ContaoCoreBundle::class,
                ContaoNewsBundle::class,
                'HeimrichHannot\NewsBundle\HeimrichHannotContaoNewsBundle',
            ]),
        ];
    }
}
