<?php

namespace App\Repository;

use App\Entity\Employe;
use App\Entity\Projet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Projet>
 */
class ProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projet::class);
    }

    public function findNonArchives(): array
    {
    return $this->createQueryBuilder('p')
        ->andWhere('p.isArchived = false')
        ->getQuery()
        ->getResult();
    }


    public function findByEmployeAndNonArchives(Employe $employe): array
{
    return $this->createQueryBuilder('p')
        ->leftJoin('p.employe', 'e') 
        ->andWhere('e = :employe')
        ->andWhere('p.isArchived = false')
        ->setParameter('employe', $employe)
        ->getQuery()
        ->getResult();
}

    //    /**
    //     * @return Projet[] Returns an array of Projet objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Projet
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
