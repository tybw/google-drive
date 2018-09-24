<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Account;
use App\Factory\AccountFactory;
use App\Form\AccountFormType;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController.
 */
class UsersController extends Controller
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * UsersController constructor.
     *
     * @param AccountRepository      $accountRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(
        AccountRepository $accountRepository,
        EntityManagerInterface $em
    ) {
        $this->accountRepository = $accountRepository;
        $this->em = $em;
    }

    public function index()
    {
        $accounts = $this->accountRepository->findAll();

        return $this->render('@user/index.html.twig', ['accounts' => $accounts]);
    }

    public function addNew(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $account = AccountFactory::createAccount($encoder);

        $form = $this->createForm(AccountFormType::class, $account);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->accountRepository->findOneBy(
                [
                    'emailCanonical' => strtolower($account->getEmail())
                ]
            );
            if (!$user instanceof Account) {
                $account->setUsername($account->getEmail());
                $account->setEmailCanonical(strtolower($account->getEmail()));
                $this->em->persist($account);
                $this->em->flush();
                $this->redirectToRoute('user');
            }
        } else {
            $this->addFlash('error', 'User already exists');
        }

        return $this->render(
            '@user/add-user.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
