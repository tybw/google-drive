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
    private const REQUEST_MAP = [
        'x-goog-channel-expiration' => 1,
        'x-goog-channel-id'         => 1,
        'x-goog-channel-token'      => 1,
        'x-goog-channel-number'     => 1,
        'x-goog-message-number'     => 1,
        'x-goog-resource-id'        => 1,
        'x-goog-resource-uri'       => 1,
        'x-goog-resource-state'     => 1
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
            $pageToken = $this->extractPageToken($content['x-goog-resource-uri'] ?? null);

            $changes = (new Changes())
                ->setChannelId($content['x-goog-channel-id'][0])
                ->setToken($content['x-goog-channel-token'][0])
                ->setMessageNumber((int) $content['x-goog-message-number'][0])
                ->setPageToken($pageToken)
                ->setContent(json_encode($content));

            $this->em->persist($changes);
            $this->em->flush();
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param null|string $uri
     *
     * @return null
     */
    private function extractPageToken(?string $uri)
    {
        parse_str(parse_url($uri ?? '', PHP_URL_QUERY), $queries);

        return $queries['pageToken'] ?? null;
    }
}
