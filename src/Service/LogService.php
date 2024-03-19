<?php

namespace App\Service;

use App\Repository\LogRepository;
use App\Response\CountResponse;

class LogService
{
    public function __construct(
        private LogRepository $logRepository
    ) {
    }

    public function count(array $serviceNames, ?\DateTime $startDate, ?\DateTime $endDate, ?int $statusCode): CountResponse
    {
        $queryBuilder = $this->logRepository->createQueryBuilder('log');

        // Apply filters based on parameters
        if (!empty($serviceNames)) {
            $queryBuilder->andWhere('log.serviceName IN (:serviceNames)')
                ->setParameter('serviceNames', $serviceNames);
        }

        if ($startDate) {
            $queryBuilder->andWhere('log.timestamp >= :startDate')
                ->setParameter('startDate', $startDate);
        }

        if ($endDate) {
            $queryBuilder->andWhere('log.timestamp <= :endDate')
                ->setParameter('endDate', $endDate);
        }

        if ($statusCode) {
            $queryBuilder->andWhere('log.statusCode = :statusCode')
                ->setParameter('statusCode', $statusCode);
        }

        // Execute the query to count matching results
        $count = $queryBuilder->select('COUNT(log.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $countResponse = new CountResponse();
        $countResponse->setCounter($count);
        return $countResponse;
    }
}
