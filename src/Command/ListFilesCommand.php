<?php

declare(strict_types=1);

namespace App\Command;

use AppBundle\Exception\ResourceNotFoundException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class ListFilesCommand.
 *
 * List files of a user in GoogleDrive
 */
class ListFilesCommand extends BaseCommand
{
    protected function configure()
    {
        $this->command = 'app:google:drive:list';
        $this
            ->setName($this->command)
            ->setDescription('List files in Google Drive')
            ->addArgument('username', InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return $this|int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);

        $list = $this->gDrive->files->listFiles(
            [
                'optParams' => [
                    'q' => "name = 'Drive Watch' and " .
                           "mimeType = 'application/vnd.google-apps.folder'"
                ]
            ]
        );
        $files = $list->getFiles();

        $data = [];
        /** @var \Google_Service_Drive_DriveFile $file */
        foreach ($files as $file) {
            $f = $file;
            $parents = $f->getParents();
            $data[] = implode(
                ', ',
                [
                    $f->getId(),
                    $f->getName(),
                    $f->getOwnedByMe(),
                    implode(', ', \is_array($parents) ? $parents : [$parents])
                ]
            );
        }

        $output->writeln(implode("\n", $data));

        return $this;
    }
}
