<?php

namespace App\Repository;

use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Patient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Patient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Patient[]    findAll()
 * @method Patient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }

 /**
 * @return Patient[] Returns an array of Patient objects
 */
    public function findByAssigned()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.assignedBy IS NOT NULL')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

public function findByAssignedid($value): ?Patient
{
    return $this->createQueryBuilder('p')
        ->Where('p.id = :val')
        ->andWhere('p.assignedBy IS NOT NULL')
        ->setParameter('val', $value)
        ->getQuery()
        ->getOneOrNullResult()
    ;
}
public function findcount($value)
{
    return $this->createQueryBuilder('p')
        ->select('p')
        ->Where('p.assignedBy = :val')
        ->setParameter('val', $value)
        ->getQuery()
        ->getResult()
    ;
}

    /*
    public function findOneBySomeField($value): ?Patient
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
