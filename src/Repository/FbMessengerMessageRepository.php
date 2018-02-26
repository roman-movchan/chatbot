<?php

namespace App\Repository;

use App\Entity\FbMessengerMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FbMessengerMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method FbMessengerMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method FbMessengerMessage[]    findAll()
 * @method FbMessengerMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FbMessengerMessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FbMessengerMessage::class);
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
