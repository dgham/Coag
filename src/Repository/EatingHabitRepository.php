<?php

namespace App\Repository;

use App\Entity\EatingHabit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EatingHabit|null find($id, $lockMode = null, $lockVersion = null)
 * @method EatingHabit|null findOneBy(array $criteria, array $orderBy = null)
 * @method EatingHabit[]    findAll()
 * @method EatingHabit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EatingHabitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EatingHabit::class);
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

public function findHabits($value)
{
    return $this->createQueryBuilder('p')
        ->select('p')
        ->Where('p.created_by  IN (:val) ')
        ->setParameter('val', $value)
        ->orderBy('p.created_by', 'DESC')
        ->getQuery()
        ->getResult();
}


    // /**
    //  * @return EatingHabit[] Returns an array of EatingHabit objects
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
    public function findOneBySomeField($value): ?EatingHabit
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
