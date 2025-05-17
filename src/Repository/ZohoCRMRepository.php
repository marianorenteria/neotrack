<?php

namespace App\Repository;

use App\Entity\ZohoCRM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ZohoCRM>
 *
 * @method ZohoCRM|null find($id, $lockMode = null, $lockVersion = null)
 * @method ZohoCRM|null findOneBy(array $criteria, array $orderBy = null)
 * @method ZohoCRM[]    findAll()
 * @method ZohoCRM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZohoCRMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ZohoCRM::class);
    }
    
    public function save(ZohoCRM $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ZohoCRM $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
