<?php

namespace App\Repository;

use App\Entity\Conseil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conseil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conseil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conseil[]    findAll()
 * @method Conseil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConseilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conseil::class);
    }

    /**
     * @return Conseil[] Returns an array of Conseil objects
     */
 
    public function findByType($type)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.type = :type')
            ->setParameter('type', $type)
            ->orderBy('a.publierAt', 'desc')

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

     /**
     * Retourne les articles publiés pour une année
     *
     * @param [type] $value
     * @return void
     */
    public function articlesParAnnee($value)
    {
        $query = $this->createQueryBuilder('a')
            ->select('a')
            ->addSelect('SUBSTRING(a.publierAt, 1, 4) as articleDate')
            ->addSelect('COUNT(a) as count')
            ->groupBy('articleDate')
            ->andWhere('a.valide = :valide')
            ->setParameter('valide', true)
            ->andWhere('SUBSTRING(a.publierAt, 1, 4) = :annee ')
            ->setParameter('annee', $value)
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * Recherche article en fonction du formulaire de recherche
     * @param $mots
     * @param $categorie
     * 
     */
    public function recherche($mots = null, $categorie = null)
    {
        $query = $this->createQueryBuilder('a');
        $query->where('a.valide = 1');
        
        if($mots != null){
            $query->andWhere('MATCH_AGAINST(a.titre, a.legende, a.sommaire, a.contenu) AGAINST(:mots boolean)>0 ')
                ->setParameter('mots', $mots);
        }
        if($categorie != null){
            // jointure sur la table Conseil avec la table Catégorie
            $query->leftJoin('a.categorie', 'c');
            $query->andWhere('c.id = :id')
                ->setParameter('id', $categorie);
        }

        return $query->getQuery()->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Conseil
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
