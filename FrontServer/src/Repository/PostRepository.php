<?php

namespace App\Repository;

use App\Entity\Post;
use App\Model\Post\ArrayOfPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $task) : Post
    {
        $this->_em->persist($task);
        $this->_em->flush();
        return $task;
    }

    public function update(Post $task)
    {
        $this->_em->persist($task);
        $this->_em->flush();
    }

    public function delete(Post $task) : void
    {
        $this->_em->remove($task);
        $this->_em->flush();
    }

    public function getReadyPosts() : array
    {
        $now = new \DateTime();

        $qb = $this->createQueryBuilder("e");
        $qb
            ->addSelect("e.id")
            ->andWhere("e.timeExecute < :now")
            ->setParameter('now', $now)
            ->andWhere('e.status = :status')
            ->setParameter('status', Post::STATUS_WAITING);
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function getUserPosts(int $id)
    {
        $qb = $this->createQueryBuilder("e");
        $qb
            ->addSelect("*")
            ->andWhere("e.user_id = :id")
            ->setParameter('id', $id);
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
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
