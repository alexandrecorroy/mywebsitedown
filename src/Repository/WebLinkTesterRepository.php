<?php

namespace App\Repository;

use App\Entity\WebLinkTester;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
final class WebLinkTesterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebLinkTester::class);
    }

    public function findOlderThan10Days(): array
    {
        $tenDaysAgo = (new \DateTime())->modify('-10 days');

        return $this->createQueryBuilder('w')
            ->where('w.createdDate < :tenDaysAgo')
            ->setParameter('tenDaysAgo', $tenDaysAgo)
            ->getQuery()
            ->getResult();
    }
}
