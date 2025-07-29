<?php

namespace App\Repository;

use App\Entity\TKUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<TKUser>
 *
 * @method TKUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method TKUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method TKUser[]    findAll()
 * @method TKUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TKUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, TKUser::class);
        $this->manager = $manager;
    }

    public function saveTKUser($email) {
        $newTKUser = new TKUser();

        $newTKUser->setEmail($email);

        $this->manager->persist($newTKUser);
        $this->manager->flush();
    }

//    /**
//     * @return TKUser[] Returns an array of TKUser objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TKUser
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
