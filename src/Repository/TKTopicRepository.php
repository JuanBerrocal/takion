<?php

namespace App\Repository;

use App\Entity\TKTopic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TKTopic>
 *
 * @method TKTopic|null find($id, $lockMode = null, $lockVersion = null)
 * @method TKTopic|null findOneBy(array $criteria, array $orderBy = null)
 * @method TKTopic[]    findAll()
 * @method TKTopic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TKTopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TKTopic::class);
    }

    public function saveTKTopic($subject) {
        $newTKTopic = new TKTopic();

        $newTKTopic->setSubject($subject);

        $this->getEntityManager()->persist($newTKTopic);
        $this->getEntityManager()->flush();
    }
    
    public function updateTKTopic($topic) {
                
        //$this->getEntityManager()->persist($topic);
        $this->getEntityManager()->flush();
    }
    
    public function removeTKTopic($topic) {
         
        $this->getEntityManager()->remove($topic);
        $this->getEntityManager()->flush();    
    }

    
//    /**
//     * @return TKTopic[] Returns an array of TKTopic objects
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

//    public function findOneBySomeField($value): ?TKTopic
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
