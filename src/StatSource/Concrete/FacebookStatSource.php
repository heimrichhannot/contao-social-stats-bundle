<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\StatSource\Concrete;

use Contao\NewsModel;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceInterface;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceResult;

class FacebookStatSource implements StatSourceInterface
{
    public static function getName(): string
    {
        return 'Facebook';
    }

    public function updateItem(NewsModel $newsModel): StatSourceResult
    {
        return new StatSourceResult(0, static::getName());

//        $this->count = 0;
//        $count = 0;
//        foreach ($this->getUrls() as $url)
//        {
//            try {
//                $response = $this->client->request('GET', 'https://graph.facebook.com/?id=' . $url);
//            } catch (ClientException $e)
//            {
//                $this->setErrorCode(static::ERROR_BREAKING);
//                $error = json_decode($e->getResponse()->getBody()->getContents());
//                $this->setErrorMessage($error->error->message);
//                return $this->error;
//            }
//
//            if ($response && $response->getStatusCode() == 200)
//            {
//                $data = json_decode($response->getBody()->getContents(), true);
//                $count += intval($data['share']['share_count']);
//            }
//        }
//        $this->count = $count;
//        return $count;

//        $this->item->facebook_counter = $this->count;
//        $this->item->facebook_updated_at = time();
//        $this->item->save();
    }
}
