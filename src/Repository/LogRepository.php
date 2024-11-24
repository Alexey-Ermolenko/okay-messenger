<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    use RepositoryModifyTrait;

    public string $_entityName;
    public mixed $_em;

    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Log::class);
        $this->_entityName = Log::class;
        $this->_em = $this->getEntityManager();
    }

    public function getLog(int $log): Log
    {
        $log = $this->find($log);
        if (null === $log) {
            throw new EntityNotFoundException();
        }

        return $log;
    }
}
