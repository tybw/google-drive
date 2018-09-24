<?php

declare(strict_types=1);

namespace App\Command;

use AppBundle\Exception\ResourceNotFoundException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WatchChangesCommand.
 */
class WatchChangesCommand extends BaseCommand
{
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
        if (!$this->changeToken) {
            $response = $this->gDrive->changes->getStartPageToken();
            $this->changeToken = $response->getStartPageToken();
            file_put_contents($this->changeFile, $this->changeToken);
        }
        $initToken = $this->changeToken;
        $nextPageToken = null;

        $allChanges = [];
        $pageToken = $initToken;
        try {
            while ($pageToken !== null) {
                $response = $this->gDrive->changes->listChanges($pageToken);
                $pageToken = $response->getNextPageToken();
                if ($pageToken === null) {
                    $nextPageToken = $response->getNewStartPageToken();
                    file_put_contents($this->changeFile, $nextPageToken);
                }
                $changes = $response->getChanges();
                foreach ($changes as $change) {
                    $allChanges[] = $change;
                }
            }
        } catch (\Google_Service_Exception $e) {
            $output->writeln('Error in fetching changes - ' . json_encode($e));
        }

        $output->writeln(json_encode($allChanges));
        $output->writeln($initToken);
        $output->writeln($nextPageToken);

        return $this;
    }
}
