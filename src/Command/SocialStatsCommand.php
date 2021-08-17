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
use HeimrichHannot\SocialStatsBundle\Event\AddNewsArticleUrlsEvent;
use HeimrichHannot\SocialStatsBundle\StatSource\Concrete\FacebookStatSource;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceItem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SocialStatsCommand extends Command
{
    protected static $defaultName = 'huh:socialstats';
    /**
     * @var ContaoFramework
     */
    protected $framework;
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    public function __construct(ContaoFramework $framework, EventDispatcher $eventDispatcher)
    {
        parent::__construct();
        $this->framework = $framework;
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->framework->initialize();

        $io = new SymfonyStyle($input, $output);

        $io->title('Updating Social Stats');

        $baseUrl = 'https://anwaltauskunft.de';

        $model = NewsModel::findByPk(1165);

        $newsList = $this->findNews();

        if (!$newsList) {
            $io->error('Found no news items.');

            return 1;
        }

        foreach ($newsList as $news) {
            $io->writeln($news->headline);

            if ($model) {
                $urls[] = News::generateNewsUrl($model);
            }

            $event = $this->eventDispatcher->dispatch(AddNewsArticleUrlsEvent::class, new AddNewsArticleUrlsEvent($model, $urls, $baseUrl));

            $urls = $event->getUrls();

            $item = new StatSourceItem($model, $urls, $baseUrl);
            $data = [];

            $facebook = new FacebookStatSource();
            $result = $facebook->updateItem($item, $data);

            $io->write($result->getMessage());

            if (!empty($result->getErrors())) {
                $io->write($result->getErrors());
            }
            $io->newLine();
        }

        $urls = [
            'magazin/leben/ehe-familie/arbeiten-in-der-elternzeit-was-ist-erlaubt',
        ];

        $io->success('Finished updating social stats');

        return 0;
    }

    /**
     * @return Collection|NewsModel[]|NewsModel|null
     */
    private function findNews(): ?Collection
    {
        return NewsModel::findBy(['pid=?'], [4], ['limit' => 10]);
    }
}
