<?php

declare(strict_types=1);

namespace App\Command;

use AppBundle\Exception\ResourceNotFoundException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GoogleOAuthCommand
 *
 * @package App\Command
 */
class GoogleOAuthCommand extends BaseCommand
{
    protected function configure()
    {
        $this->command = 'app:google:oauth';
        $this
            ->setName($this->command)
            ->setDescription('Carry out Google OAuth operations')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Target Google user'
            )
            ->addArgument(
                'google-key',
                InputArgument::OPTIONAL,
                'Google OAuth client secret JSON file'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return $this|int|null
     *
     * @throw \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);

        if ($this->g->isAccessTokenExpired()) {
            $refreshToken = $this->g->getRefreshToken();
            if ($refreshToken) {
                $this->g->fetchAccessTokenWithRefreshToken($refreshToken);
            } else {
                $authUrl = $this->g->createAuthUrl();
                $output->writeln('Open the following link in your browser:\n');
                $output->writeln($authUrl);
                $output->writeln('Enter verification code: ');
                $token = $this->g->fetchAccessTokenWithAuthCode(trim(fgets(STDIN)));
                if (array_key_exists('error', $token)) {
                    throw new \RuntimeException(implode(', ', $token));
                }
                file_put_contents($this->tokenFile, json_encode($this->g->getAccessToken()));
                $output->writeln(json_encode($this->g->getAccessToken()));
            }
        }

        return $this;
    }
}
