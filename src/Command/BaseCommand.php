<?php

declare(strict_types=1);

namespace App\Command;

use AppBundle\Exception\ResourceNotFoundException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class GoogleOAuthCommand.
 */
abstract class BaseCommand extends ContainerAwareCommand
{
    public const APP_NAME = 'Drive watch';
    private const KEY_PATH = __DIR__ . '/../../var/private/';
    private const OAUTH_KEY = 'client_secret.json';
    private const ACCESS_TOKEN_FILE = 'access_token.json';
    private const CHANGE_TOKEN_FILE = 'change_token.json';

    /** @var string $command */
    protected $command;

    /** @var string $keyFile */
    protected $keyFile;

    /** @var string $tokenFile */
    protected $tokenFile;

    /** @var string $changeFile */
    protected $changeFile;

    /** @var string $accessToken */
    protected $accessToken;

    /** @var string $changeToken */
    protected $changeToken;

    /** @var \Google_Client */
    protected $g;

    /** @var \Google_Service_Drive $gDrive */
    protected $gDrive;

    /**
     * @param InputInterface $input
     *
     * @return $this
     */
    protected function handleInputs(InputInterface $input)
    {
        $this->tokenFile = self::KEY_PATH .
                           ($input->hasArgument('username') ?
                               $input->getArgument('username') . '-' : '') .
                           self::ACCESS_TOKEN_FILE;

        if (file_exists($this->tokenFile)) {
            $this->accessToken = json_decode(file_get_contents($this->tokenFile), true);
        }

        $this->keyFile = $input->hasArgument('google-key') &&
                         $input->getArgument('google-key') ?
            $input->getArgument('google-key') : self::OAUTH_KEY;

        while (!file_exists($this->keyFile)) {
            $this->keyFile = self::KEY_PATH . $this->keyFile;
            if (!file_exists($this->keyFile)) {
                throw new FileNotFoundException(
                    'Google OAuth key not found - ' . $this->keyFile
                );
            }
        }

        $this->changeFile = self::KEY_PATH . '/' . self::CHANGE_TOKEN_FILE;
        if (file_exists($this->changeFile)) {
            $content = json_decode(file_get_contents($this->changeFile), true);
            $this->changeToken = $content['token'];
        }

        return $this;
    }

    /**
     * @param InputInterface $input
     *
     * @return $this
     */
    protected function init(InputInterface $input)
    {
        $this->handleInputs($input);
        $this->googleDriveClient();

        return $this;
    }

    /**
     * @return $this;
     */
    protected function googleDriveClient()
    {
        $this->g = $this->googleClient(
            self::APP_NAME,
            [\Google_Service_Drive::DRIVE],
            $this->keyFile
        );

        $this->gDrive = new \Google_Service_Drive($this->g);

        return $this;
    }

    /**
     * @param string $appName
     * @param array  $scopes
     * @param string $keyFile
     *
     * @return \Google_Client
     */
    private function googleClient(string $appName, array $scopes, string $keyFile)
    {
        $g = new \Google_Client();
        $g->setScopes($scopes);
        $g->setApplicationName($appName);
        $g->setAuthConfig($keyFile);

        if ($this->accessToken && array_key_exists('access_token', $this->accessToken)) {
            $g->setAccessToken($this->accessToken['access_token']);
        }

        return $g;
    }
}
