<?php

namespace App\Repository;

use App\Entity\FileReadHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FileReadHistory>
 *
 * @method FileReadHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileReadHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileReadHistory[]    findAll()
 * @method FileReadHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileReadHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileReadHistory::class);
    }

    public function findLatest(): ?FileReadHistory
    {
        return $this->findOneBy([], ['readTill' => 'DESC']);
    }
}
