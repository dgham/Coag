<?php

namespace App\Repository;

use App\Entity\DoctorAssignement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DoctorAssignement|null find($id, $lockMode = null, $lockVersion = null)
 * @method DoctorAssignement|null findOneBy(array $criteria, array $orderBy = null)
 * @method DoctorAssignement[]    findAll()
 * @method DoctorAssignement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctorAssignementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DoctorAssignement::class);
    }

    // /**
    //  * @return DoctorAssignement[] Returns an array of DoctorAssignement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DoctorAssignement
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
