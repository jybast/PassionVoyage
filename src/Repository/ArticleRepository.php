<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
 
    public function findByValide($value, $limite)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.valide = :valide')
            ->setParameter('valide', $value)
            ->orderBy('a.publierAt', 'desc')
            ->setMaxResults($limite)
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
     * Retourne les articles publiés par date de publication
     *
     * @param [type] $value
     * @return void
     */
    public function articlesParDate($value)
    {
        $query = $this->createQueryBuilder('a')
            ->select('SUBSTRING(a.publierAt, 1, 10) as articleDate')
            ->addSelect('COUNT(a) as count')
            ->groupBy('articleDate')
            ->andWhere('a.valide = :valide')
            ->setParameter('valide', $value)
        ;

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
