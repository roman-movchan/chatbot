<?php

namespace App\Repository;

use App\Entity\FbMessengerUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FbMessengerUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method FbMessengerUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method FbMessengerUser[]    findAll()
 * @method FbMessengerUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FbMessengerUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FbMessengerUser::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('f')
            ->where('f.something = :value')->setParameter('value', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
