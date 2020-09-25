<?php

namespace App\Repository;

use App\Entity\DrugType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DrugType|null find($id, $lockMode = null, $lockVersion = null)
 * @method DrugType|null findOneBy(array $criteria, array $orderBy = null)
 * @method DrugType[]    findAll()
 * @method DrugType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DrugTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DrugType::class);
    }

    // /**
    //  * @return DrugType[] Returns an array of DrugType objects
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
    public function findOneBySomeField($value): ?DrugType
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
