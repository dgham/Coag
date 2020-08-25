<?php

namespace App\Repository;

use App\Entity\MedicationList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MedicationList|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicationList|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicationList[]    findAll()
 * @method MedicationList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicationListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicationList::class);
    }

    // /**
    //  * @return MedicationList[] Returns an array of MedicationList objects
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
    public function findOneBySomeField($value): ?MedicationList
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
