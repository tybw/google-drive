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
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var ChangesRepository $changesRepository */
    private $changesRepository;

    public function __construct(EntityManagerInterface $em, ChangesRepository $changesRepository)
    {
        $this->em = $em;
        $this->changesRepository = $changesRepository;
    }

    public function index(Request $request)
    {
        $content = json_decode($request->getContent() ?? []);
        $changes = new Changes();
        $changes->setChannelId($content->id);
        $changes->setToken($content->token);
        $changes->setContent(json_encode($content));
        $this->em->persist($changes);
        $this->em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
