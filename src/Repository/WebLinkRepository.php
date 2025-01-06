<?php

namespace App\Repository;

use App\Entity\WebLink;
use App\Entity\WebLinkSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WebLink>
 */
final class WebLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebLink::class);
    }

    public function findLast7DaysValuesForEachDayByWebLinkSchedule(WebLinkSchedule $webLinkSchedule): array
    {
        $results = [];

        for ($i = 0; $i < 7; $i++) {
            $date = new \DateTime();
            $date->modify("-$i days");

            $queryBuilder = $this->createQueryBuilder('e');
            $queryBuilder->select('e.statusCode, COUNT(e.id) AS count')
                ->where('e.webLinkSchedule = :val')
                ->setParameter('val', $webLinkSchedule->getId())
                ->andWhere('e.dateVisited >= :start_date')
                ->andWhere('e.dateVisited <= :end_date')
                ->groupBy('e.statusCode')
                ->setParameter('start_date', $date->format('Y-m-d 00:00:00'))
                ->setParameter('end_date', $date->format('Y-m-d 23:59:59'));

            $dayResults = $queryBuilder->getQuery()->getResult();

            foreach ($dayResults as $row) {
                $statusCode = $row['statusCode'];
                $count = (int)$row['count'];

                if (!isset($results[$statusCode])) {
                    $results[$statusCode] = array_fill(0, 7, 0);
                }

                $results[$statusCode][$i] = $count;
            }
        }

        foreach ($results as &$counts) {
            $counts = array_reverse($counts);
        }

        return $results;
    }

    public function findLast7DaysValuesByWebLinkSchedule(WebLinkSchedule $webLinkSchedule): array
    {
        $startDate = new \DateTime();
        $startDate->modify('-6 days')->setTime(0, 0, 0);

        $endDate = new \DateTime();
        $endDate->setTime(23, 59, 59);

        $queryBuilder = $this->createQueryBuilder('e');
        $queryBuilder->select('e.statusCode, COUNT(e.id) AS count')
            ->where('e.webLinkSchedule = :val')
            ->setParameter('val', $webLinkSchedule->getId())
            ->andWhere('e.dateVisited >= :start_date')
            ->andWhere('e.dateVisited <= :end_date')
            ->groupBy('e.statusCode')
            ->setParameter('start_date', $startDate->format('Y-m-d H:i:s'))
            ->setParameter('end_date', $endDate->format('Y-m-d H:i:s'));

        $results = $queryBuilder->getQuery()->getResult();

        $statusCounts = [];
        foreach ($results as $row) {
            $statusCounts[$row['statusCode']] = (int)$row['count'];
        }

        return $statusCounts;
    }

    public function findLast10LinkToFetchWithStatusCode(WebLinkSchedule $webLinkSchedule): array
    {

        $queryBuilder = $this->createQueryBuilder('e');
        $queryBuilder->select('e')
            ->where('e.statusCode NOT LIKE :status_code')
            ->andWhere('e.webLinkSchedule = :val')
            ->setParameter('status_code', $webLinkSchedule->getStatusCodeVerifying() . '%')
            ->setParameter('val', $webLinkSchedule->getId())
            ->orderBy('e.dateVisited', 'DESC')
            ->setMaxResults(10);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findOlderThan7Days(): array
    {
        $sevenDaysAgo = (new \DateTime())->modify('-7 days');

        return $this->createQueryBuilder('w')
            ->where('w.dateVisited < :sevenDaysAgo')
            ->setParameter('sevenDaysAgo', $sevenDaysAgo)
            ->getQuery()
            ->getResult();
    }

}
