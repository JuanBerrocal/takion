<?php

namespace App\Repository;

use App\Entity\TKPost;
use App\Entity\TKUser;
use App\Entity\TKTopic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<TKPost>
 *
 * @method TKPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method TKPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method TKPost[]    findAll()
 * @method TKPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TKPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TKPost::class);
    }

    public function saveTKPost(array $data) {
                
        // Perhaps we should check the data is really an ISO 8061 date string here.
        $createdDate = new \DateTimeImmutable('@'.strtotime($data['created']));
        $updatedDate = new \DateTime('@'.strtotime($data['updated']));

        $author = $this->getEntityManager()->getRepository(TKUser::class)->findOneBy(['email' => $data['author']['email']]);
        if ($author == null) {
            throw new NotFoundHttpException('Unknown user');
        }

        $subject = $this->getEntityManager()->getRepository(TKTopic::class)->findOneBy(['subject' => $data['subject']]);

        if (!$subject) {
            $subject = new TKTopic();
            $subject->setSubject($data['subject']);
            $this->getEntityManager()->persist($subject);
            $this->getEntityManager()->flush($subject);
        }

        $newPost = new TKPost();
        $newPost->setCreated($createdDate);
        $newPost->setUpdated($updatedDate);
        $newPost->setAuthor($author);
        $newPost->setTitle($data['title']);
        $newPost->setText($data['text']);
        $newPost->setSubject($subject);

        $this->getEntityManager()->persist($newPost);
        $this->getEntityManager()->flush();
    }

    public function updateTKPost(TKPost $post) {

        $topicName = $post->getSubject()->getSubject();

        $subject = $this->getEntityManager()->getRepository(TKTopic::class)->findOneBy(['subject' => $topicName]);

        if (!$subject) {
            $subject = new TKTopic();
            $subject->setSubject($topicName);
            $this->getEntityManager()->persist($subject);
            $this->getEntityManager()->flush($subject);
        }

        
        $post->setSubject($subject);
        
        $this->getEntityManager()->flush();
    }
        
    public function removeTKPost($post) {
                
        $this->getEntityManager()->remove($post);
        $this->getEntityManager()->flush();
    }


//    /**
//     * @return TKPost[] Returns an array of TKPost objects
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

//    public function findOneBySomeField($value): ?TKPost
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
