<?php

namespace App\Repository;

use App\Entity\InvatedUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InvatedUsers|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvatedUsers|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvatedUsers[]    findAll()
 * @method InvatedUsers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvatedUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvatedUsers::class);
    }

    // /**
    //  * @return InvatedUsers[] Returns an array of InvatedUsers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InvatedUsers
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
