<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Changes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ChangesRepository extends ServiceEntityRepository
{
    /**
     * ChangesRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Changes::class);
    }
}
