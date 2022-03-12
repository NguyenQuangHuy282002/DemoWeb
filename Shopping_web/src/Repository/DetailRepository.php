<?php

namespace App\Repository;

use App\Entity\Detail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Detail|null find($id, $lockMode = null, $lockVersion = null)
 * @method Detail|null findOneBy(array $criteria, array $orderBy = null)
 * @method Detail[]    findAll()
 * @method Detail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Detail::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Detail $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Detail $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Detail[] Returns an array of Detail objects
     */

    public function findDetailByOID($id)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.orders = :val')
            ->setParameter('val', $id)
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function getIt($date)
    {
        $intoDB = $this->getEntityManager()->getConnection();

        $sql = "SELECT SUM(price * quantity) as totalPrice FROM `detail` AS d INNER JOIN `order` AS o on d.orders_id = o.id WHERE o.date = :date";

        $getItNow = $intoDB -> prepare($sql);

        $result = $getItNow->executeQuery(['date' => $date]);


        return $result -> fetchAllAssociative();
    }

    public function getIt1($name)
    {
        $intoDB = $this->getEntityManager()->getConnection();

        $sql = "SELECT SUM(price * quantity) as totalPrice FROM `detail` AS d INNER JOIN `order` AS o on d.orders_id = o.id WHERE o.username = :date";

        $getItNow = $intoDB -> prepare($sql);

        $result = $getItNow->executeQuery(['date' => $name]);


        return $result -> fetchAllAssociative();
    }


    // /**
    //  * @return Detail[] Returns an array of Detail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Detail
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
