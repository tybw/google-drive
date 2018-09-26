<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Changes;
use App\Repository\ChangesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ChangeHookController.
 */
class ChangeHookController extends Controller
{
    private const HDR_CHANNEL_ID = 'x-goog-channel-id';
    private const HDR_CHANNEL_TOKEN = 'x-goog-channel-token';
    private const HDR_CHANNEL_EXPIRATION = 'x-goog-channel-expiration';
    private const HDR_CHANNEL_NUMBER = 'x-goog-channel-number';
    private const HDR_MESSAGE_NUMBER = 'x-goog-message-number';
    private const HDR_RESOURCE_ID = 'x-goog-resource-id';
    private const HDR_RESOURCE_URI = 'x-goog-resource-uri';
    private const HDR_RESOURCE_STATE = 'x-goog-resource-state';
    private const REQUEST_MAP = [
        self::HDR_CHANNEL_ID         => 1,
        self::HDR_CHANNEL_TOKEN      => 1,
        self::HDR_CHANNEL_EXPIRATION => 1,
        self::HDR_CHANNEL_NUMBER     => 1,
        self::HDR_MESSAGE_NUMBER     => 1,
        self::HDR_RESOURCE_ID        => 1,
        self::HDR_RESOURCE_URI       => 1,
        self::HDR_RESOURCE_STATE     => 1
    ];

    /** @var EntityManagerInterface $em */
    private $em;

    /** @var ChangesRepository $changesRepository */
    private $changesRepository;

    public function __construct(
        EntityManagerInterface $em,
        ChangesRepository $changesRepository
    ) {
        $this->em = $em;
        $this->changesRepository = $changesRepository;
    }

    public function index(Request $request)
    {
        $content = array_intersect_key($request->headers->all(), self::REQUEST_MAP);

        if ($content) {
            $id = $content[self::HDR_CHANNEL_ID][0];
            $token = $content[self::HDR_CHANNEL_TOKEN][0];
            $messageNumber = (int) $content[self::HDR_MESSAGE_NUMBER][0];

            $changes = $this->changesRepository->findByChannelId($id);

            if (!$changes) {
                $changes = (new Changes())
                    ->setChannelId($id)
                    ->setToken($token)
                    ->setMessageNumber($messageNumber)
                    ->setExpireAt(
                        new \DateTimeImmutable($content[self::HDR_CHANNEL_EXPIRATION][0])
                    )
                    ->setPageToken(
                        $this->extractPageToken($content[self::HDR_RESOURCE_URI][0] ?? null)
                    )
                    ->setContent(json_encode($content));

                $this->em->persist($changes);
                $this->em->flush();
            }
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param null|string $uri
     *
     * @return int|null
     */
    private function extractPageToken(?string $uri): ?int
    {
        parse_str(parse_url($uri ?? '', PHP_URL_QUERY), $queries);

        return $queries['pageToken'] ? (int) $queries['pageToken'] : null;
    }
}
