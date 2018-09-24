<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Account;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AccountFactory.
 */
class AccountFactory
{
    /**
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return Account
     */
    public static function createAccount(UserPasswordEncoderInterface $encoder)
    {
        $account = (new Account())
            ->setRoles('ROLE_USER')
            ->setEnabled(true)
            ->setDeleted(false)
            ->setCreatedAt(new \DateTimeImmutable());

        $account->setPassword($encoder->encodePassword(
            $account,
            bin2hex(random_bytes(20))));

        return $account;
    }
}
