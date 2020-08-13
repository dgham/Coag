<?php

namespace App\Repository;

use App\Entity\MedicationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MedicationType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicationType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicationType[]    findAll()
 * @method MedicationType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicationTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicationType::class);
    }

    // /**
    //  * @return MedicationType[] Returns an array of MedicationType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MedicationType
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
