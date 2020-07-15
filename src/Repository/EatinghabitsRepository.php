<?php

namespace App\Repository;

use App\Entity\Eatinghabits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Eatinghabits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eatinghabits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eatinghabits[]    findAll()
 * @method Eatinghabits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EatinghabitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eatinghabits::class);
    }

    public function findcount($value)
{
    return $this->createQueryBuilder('p')
        ->select('count(p)')
        ->Where('p.createdBy = :val')
        ->setParameter('val', $value)
        ->getQuery()
        ->getResult()
    ;
}

    // /**
    //  * @return Eatinghabits[] Returns an array of Eatinghabits objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Eatinghabits
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
