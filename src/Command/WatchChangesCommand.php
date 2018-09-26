<?php

declare(strict_types=1);

namespace App\Command;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WatchChangesCommand.
 */
class WatchChangesCommand extends BaseCommand
{
    private const CHANNEL_TYPE = 'web_hook';
    private const RESOURCE_URI = 'https://www.googleapis.com/drive/v3/changes';

    protected function configure()
    {
        $this->command = 'app:google:drive:changes:watch';
        $this
            ->setName($this->command)
            ->setDescription('Watch file changes in Google Drive')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('resource-id', InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return $this|int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
        $resourceId = $input->getArgument('resource-id');

        if (!$this->changeToken) {
            $response = $this->gDrive->changes->getStartPageToken();
            $this->changeToken = $response->getStartPageToken();
            file_put_contents($this->changeFile, $this->changeToken);
        }

        $channelId = Uuid::uuid4();

        $channel = new \Google_Service_Drive_Channel();
        $channel->setId($channelId);
        $channel->setType(self::CHANNEL_TYPE);
        $channel->setResourceId($resourceId);
        $channel->setResourceUri(self::RESOURCE_URI);
        $channel->setToken($this->username);
        $channel->setExpiration((new \DateTimeImmutable('+1 hrs'))->getTimestamp() * 1000);
        $channel->setAddress(getenv('WATCH_WEB_HOOK'));

        $this->gDrive->changes->watch($this->changeToken, $channel);

        $output->writeln('Channel ID: ' . $channelId);
        $output->writeln('Change token: ' . $this->changeToken);
        $output->writeln(json_encode($channel));

        return $this;
    }
}
