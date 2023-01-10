<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\StatSource\Concrete;

use Google_Client;
use Google_Service_Analytics;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_Metric;
use Google_Service_Exception;
use HeimrichHannot\SocialStatsBundle\Exception\InvalidSetupException;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceInterface;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceItem;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceResult;

class GoogleAnalyticsStatSource implements StatSourceInterface
{
    /**
     * @var string
     */
    private $keyfile;
    /**
     * @var Google_Service_AnalyticsReporting
     */
    private $analytics;

    public function __construct(array $bundleConfig)
    {
        $this->keyfile = $bundleConfig['google_analytics']['key_file'];
        $this->viewId = $bundleConfig['google_analytics']['view_id'];
    }

    public static function getName(): string
    {
        return 'Google Analytics';
    }

    public function prepare(): void
    {
        if (!file_exists($this->keyfile)) {
            throw new InvalidSetupException('Google Analytics keyfile does not exist at configured path. Given path: '.$this->keyfile);
        }

        if (!class_exists('Google_Client')) {
            throw new InvalidSetupException('Google APIs Client Library for PHP must be installed.');
        }
        $client = new Google_Client();
        $client->setApplicationName('Social Stats');
        $client->setAuthConfig($this->keyfile);
        $client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);
        $analytics = new Google_Service_AnalyticsReporting($client);

        $this->analytics = $analytics;
    }

    public function updateItem(StatSourceItem $item, array &$data): StatSourceResult
    {
        $count = 0;
        $result = new StatSourceResult($this::getName());

        foreach ($item->getUrls() as $url) {
            $urlCount = 0;
            $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
            $body->setReportRequests([$this->prepareRequest($url, $item)]);

            try {
                $responce = $this->analytics->reports->batchGet($body);
            } catch (Google_Service_Exception $e) {
                foreach ($e->getErrors() as $error) {
                    $result->addError($error['message']);
                }

                if (429 === $e->getCode()) {
                    break;
                }

                continue;
            }

            $report = $responce[0];
            $rows = $report->getData()->getRows();

            for ($rowIndex = 0; $rowIndex < \count($rows); ++$rowIndex) {
                $row = $rows[$rowIndex];
                $metrics = $row->getMetrics();
                $values = $metrics[0]->getValues();
                $urlCount += $values[0];
            }
            $count += $urlCount;
            $result->addVerboseMessage($url.': '.$urlCount);
        }

        if ($count >= ($data['google_analytics'] ?? 0)) {
            $data['google_analytics'] = $count;
        }

        $result->setCount($count);

        return $result;
    }

    public function prepareRequest(string $url, StatSourceItem $item)
    {
        $dataRange = new \Google_Service_AnalyticsReporting_DateRange();
        $dataRange->setStartDate(date('Y-m-d', $item->getStartDate()));
        $dataRange->setEndDate(date('Y-m-d'));

        $metric = new Google_Service_AnalyticsReporting_Metric();
        $metric->setExpression('ga:uniquePageviews');

        $dimension = new \Google_Service_AnalyticsReporting_Dimension();
        $dimension->setName('ga:pagePath');

        $dimensionFilter = new \Google_Service_AnalyticsReporting_DimensionFilter();
        $dimensionFilter->setDimensionName('ga:pagePath');
        $dimensionFilter->setExpressions([$url]);

        $dimensionFilterClause = new \Google_Service_AnalyticsReporting_DimensionFilterClause();
        $dimensionFilterClause->setFilters([$dimensionFilter]);

        $request = new \Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($this->viewId);
        $request->setDateRanges($dataRange);
        $request->setMetrics([$metric]);
        $request->setDimensions($dimension);
        $request->setDimensionFilterClauses($dimensionFilterClause);

        return $request;
    }
}
