<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\StatSource\Concrete;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceInterface;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceItem;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceResult;

class FacebookStatSource implements StatSourceInterface
{
    const GRAPH_URL = 'https://graph.facebook.com/v11.0/?id=%url%&fields=engagement&access_token=345243152579662%7CDMavAfdcWsa5scrgdMEWltbGUkE';

    public static function getName(): string
    {
        return 'Facebook';
    }

    public function updateItem(StatSourceItem $item, array &$data): StatSourceResult
    {
        $client = new Client([]);
        $result = new StatSourceResult(static::getName());
        $count = 0;

        foreach ($item->getUrls() as $url) {
            try {
                $fbUrl = str_replace('%url%', urlencode($item->getBaseUrl().'/'.$url), static::GRAPH_URL);
                $response = $client->request('GET', $fbUrl);
            } catch (ClientException $e) {
                $error = json_decode($e->getResponse()->getBody()->getContents());
                $result->addError($error->error->message);

                continue;
            }

            if ($response && 200 == $response->getStatusCode()) {
                $data = json_decode($response->getBody()->getContents(), true);
                $count += (int) ($data['engagement']['share_count']);
            }
        }

        $data['facebook'] = $count;
        $result->setCount($count);

        return $result;
    }
}
