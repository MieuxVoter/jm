<?php

namespace App\Repository;

use App\Entity\Proposal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Proposal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proposal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proposal[]    findAll()
 * @method Proposal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProposalRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Proposal::class);
    }

//    /**
//     * @return Proposal[] Returns an array of Proposal objects
//     */

    public function findByDateDeleteBefore(\DateTime $date): array
    {
         return $this->createQueryBuilder('p')
            ->andWhere('p.date_delete < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
//     * @return Proposal[] Returns an array of Proposal objects
//     */

    public function findByDateEndBetween(\DateTime $dateBefore,\DateTime $dateAfter): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.date_end > :date_before')
            ->andWhere('p.date_end < :date_after')
            ->setParameter('date_before', $dateBefore)
            ->setParameter('date_after', $dateAfter)
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Proposal
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
