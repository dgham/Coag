<?php

namespace App\Repository;

use App\Entity\Matricule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Matricule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Matricule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Matricule[]    findAll()
 * @method Matricule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatriculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matricule::class);
    }

    // /**
    //  * @return Matricule[] Returns an array of Matricule objects
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
    public function findOneBySomeField($value): ?Matricule
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
