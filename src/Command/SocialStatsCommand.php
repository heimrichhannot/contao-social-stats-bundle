<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\Command;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Model\Collection;
use Contao\News;
use Contao\NewsModel;
use Contao\StringUtil;
use HeimrichHannot\SocialStatsBundle\Event\AddNewsArticleUrlsEvent;
use HeimrichHannot\SocialStatsBundle\Exception\InvalidSetupException;
use HeimrichHannot\SocialStatsBundle\StatSource\Concrete\FacebookStatSource;
use HeimrichHannot\SocialStatsBundle\StatSource\Concrete\GoogleAnalyticsStatSource;
use HeimrichHannot\SocialStatsBundle\StatSource\Concrete\MatomoStatSource;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceInterface;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceItem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Routing\Router;

class SocialStatsCommand extends Command
{
    protected static $defaultName = 'huh:socialstats:update';
    /**
     * @var ContaoFramework
     */
    protected $framework;
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;
    /**
     * @var array
     */
    protected $bundleConfig;
    /**
     * @var Router
     */
    protected $router;

    public function __construct(ContaoFramework $framework, EventDispatcher $eventDispatcher, array $bundleConfig, Router $router)
    {
        parent::__construct();
        $this->framework = $framework;
        $this->eventDispatcher = $eventDispatcher;
        $this->bundleConfig = $bundleConfig;
        $this->router = $router;
    }

    protected function configure()
    {
        $this
            ->setDescription('Update the social stats of your news.')
            ->addOption('platforms', 'p', InputOption::VALUE_OPTIONAL, 'Limit to specific platform/network. See help for more information.')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit the number of news article to update.', 20)
            ->addOption('age', 'a', InputOption::VALUE_REQUIRED, 'Limit the age of articles to be updated to a number of days. 0 means no limit.', 0)
            ->addOption('pid', null, InputOption::VALUE_REQUIRED, 'Limit the news articles to given archives. 0 means all archives.', 0)
            ->setHelp(
                "This command updates the social statistics of your news entries.\n\n".
                "Following options are available for platforms option:\n".
                "  fb - Facebook\n".
                "  ga - Google Analytics\n".
                '  ma - Matomo'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->framework->initialize();

        $io = new SymfonyStyle($input, $output);

        $io->title('Updating Social Stats');

        $baseUrl = $this->bundleConfig['base_url'] ?? null;

        if (null === $baseUrl) {
            $route = $this->router->getContext();
            $baseUrl = $route->getScheme().$route->getHost();
        }

        if ($io->isVerbose()) {
            $io->writeln('Base url: '.$baseUrl);
            $io->writeln('Start date: '.date('Y-m-d', $this->bundleConfig['start_date']));
            $io->newLine();
        }

        $newsList = $this->findNews($input);

        if (!$newsList) {
            $io->error('Found no news items.');

            return 1;
        }

        $networks = $this->getNetworks($input);

        foreach ($networks as $index => $network) {
            try {
                $network->prepare();
            } catch (InvalidSetupException $e) {
                $io->error('Network '.$network::getName()." was not correctly setup. It will not be used in the further processing.\n\nReason:\n".$e->getMessage());
                unset($networks[$index]);

                continue;
            }
        }

        foreach ($newsList as $news) {
            $io->writeln('<options=underscore>'.$news->headline.' (ID '.$news->id.')</>');

            if ($news) {
                $urls[] = News::generateNewsUrl($news);
            }

            $event = $this->eventDispatcher->dispatch(AddNewsArticleUrlsEvent::class, new AddNewsArticleUrlsEvent($news, $urls, $baseUrl));

            $urls = $event->getUrls();

            if ($io->isVerbose()) {
                $io->writeln('Found following urls for news article:');
                $io->listing($urls);
            }

            $item = new StatSourceItem($news, $urls, $baseUrl, $this->bundleConfig['start_date']);

            $data = StringUtil::deserialize($news->huh_socialstats_values, true);

            foreach ($networks as $network) {
                $result = $network->updateItem($item, $data);

                $io->writeln('<options=bold>'.$result->getNetwork().'</>: Found '.$result->getCount().' '.$result->getCountType());

                if ($io->isVerbose()) {
                    $io->listing($result->getVerboseMessages());
                }

                if (!empty($result->getErrors())) {
                    $io->writeln('<error>'.implode("</error>\n<error>", $result->getErrors()).'</error>');
                }

                if ($io->isVerbose()) {
                    $io->newLine();
                }
            }

            $news->huh_socialstats_values = serialize($data);
            $news->huh_socialstats_last_updated = time();
            $news->save();

            $io->newLine(2);
        }

        $io->success('Finished updating social stats');

        return 0;
    }

    /**
     * @return StatSourceInterface[]
     */
    protected function getNetworks(InputInterface $input): array
    {
        $platforms = ['fb', 'ga', 'ma'];

        if ($input->hasOption('platforms') && $input->getOption('platforms')) {
            $platforms = $input->getOption('platforms');
            $platforms = explode(',', $platforms);
        }

        $networks = [];

        if (\in_array('fb', $platforms)) {
            $networks[] = new FacebookStatSource($this->bundleConfig);
        }

        if (\in_array('ga', $platforms)) {
            $networks[] = new GoogleAnalyticsStatSource($this->bundleConfig);
        }

        if (\in_array('ma', $platforms)) {
            $networks[] = new MatomoStatSource($this->bundleConfig);
        }

        return $networks;
    }

    /**
     * @return Collection|NewsModel[]|NewsModel|null
     */
    private function findNews(InputInterface $input): ?Collection
    {
        $limit = $input->getOption('limit');
        $age = $input->getOption('age');
        $pids = $input->getOption('pid');

        $t = NewsModel::getTable();
        $time = \Date::floorToMinute();

        $columns = [
            "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'".($time + 60)."') AND $t.published='1'",
        ];
        $values = [];
        $options = [
            'limit' => (int) $limit,
            'order' => 'huh_socialstats_last_updated ASC, tstamp DESC',
        ];

        if (is_numeric($pids) && 0 == (int) $pids) {
            $pids = null;
        } else {
            $pids = explode(',', $pids);
            $columns[] = "$t.pid IN(".implode(',', array_map('\intval', $pids)).')';
        }

        if (0 !== (int) $age) {
            $age = (int) $age * 86400;
            $columns[] = "$t.date>=?";
            $values[] = $age;
        }

        return NewsModel::findBy($columns, $values, $options);
    }
}
