<?php

namespace App\Repository;

use DateTime;
use App\Entity\Diagnostic;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Diagnostic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Diagnostic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Diagnostic[]    findAll()
 * @method Diagnostic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiagnosticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Diagnostic::class);
    }

   //public function findcount($assigned)
//{
  // $entityManager= $this->getEntityManager($id,$createdby,$assigned);
  // $query=$entityManager->createQuery(
      // 'select count(d)
      // from App\Entity\Diagnostic d
      // join user u 
     ///  where u.id= :id
     ///  join App\Entity\Patient p 
     //  where p.created_by= :createdby and p.assigned= :assigned'
 //  )->setParmeters(array('id'=>$id,'createdby'=>$createdby,'assigned'=>$assigned));
//return $query->getOnOrNullResult();
//}


public function findByINRMesureNormal($value,$indication)
{
    return $this->createQueryBuilder('p')
         ->Where('p.created_by IN (:val)')
         ->setParameter('val',$value)
         ->andWhere('p.indication LIKE :indication')
         ->setParameter('indication',$indication)
        ->getQuery()
        ->getResult();
        
}
public function findByINRMesureINormal($value,$indication)
{
    return $this->createQueryBuilder('p')
         ->Where('p.created_by IN (:val)')
         ->setParameter('val',$value)
         ->andWhere('p.indication LIKE :indication')
         ->setParameter('indication',$indication)
        ->getQuery()
        ->getResult();
        
}
public function findBymaxDate($value)
{
    return $this->createQueryBuilder('p')
         ->select('p')
         ->Where('p.created_by IN (:val)')
         ->setParameter('val',$value)
         ->orderBy('p.created_at', 'DESC')
         ->setMaxResults( 1 )
        ->getQuery()
        ->getOneOrNullResult();
        
}
public function getassigned($value,$indication)
{
    return $this->createQueryBuilder('p')
         ->select('p')
         ->Where('p.created_by IN (:val)')
         ->setParameter('val',$value)
         ->andWhere('p.indication LIKE :indication')
         ->setParameter('indication',$indication)
        ->getQuery()
        ->getResult();
        
}

public function findByMesuremaxDate($value)
{
    return $this->createQueryBuilder('p')
         ->select('p')
         ->Where('p.created_by  IN (:val) ')
         ->setParameter('val',$value)
         ->orderBy('p.id', 'DESC')
         ->groupBy('p.created_by')
        ->getQuery()
        ->getResult();
}



public function findByLatestMesureByPatient($value)
{
    return $this->createQueryBuilder('p')
         ->select('p')
         ->Where('p.created_by  IN (:val) ')
         ->setParameter('val',$value)
         ->orderBy('p.created_at', 'DESC')
         ->setMaxResults( 5 )
        ->getQuery()
        ->getResult();        

}

    // /**
    //  * @return Diagnostic[] Returns an array of Diagnostic objects
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
    public function findOneBySomeField($value): ?Diagnostic
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
