<?php

namespace App\Repository;

use App\Entity\Appointments;
use Doctrine\ORM\EntityRepository;
use Exception;
use StringHelper;

class AppointmentsRepository extends EntityRepository
{
    public function getByFilters(array $filters, $order_field=null, $order_where=null, $start=null, $limit=null)
    {
        $queryBuilder=$this->getEntityManager()->createQueryBuilder();

        $queryBuilder
            ->select('a AS appointment')
            ->from(Appointments::class, 'a')
        ;

        if (!is_null($order_field) && !is_null($order_where))
            $queryBuilder->orderBy($order_field, $order_where);

        if (!is_null($start) && !is_null($limit))
        {
            $queryBuilder
                ->setFirstResult($start)
                ->setMaxResults($limit)
            ;
        }

        if (!empty($filters['start_timestamp']))
        {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->gt('a.date', ':start_timestamp'))
                ->setParameter('start_timestamp', $filters['start_timestamp'])
            ;
        }

        if (!empty($filters['end_timestamp']))
        {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->lt('a.date', ':end_timestamp'))
                ->setParameter('end_timestamp', $filters['end_timestamp'])
            ;
        }

        if (!empty($filters['tel']))
        {
            if (str_starts_with($filters['tel'], '*') || str_ends_with($filters['tel'], '*'))
            {
                $tel=trim($filters['tel']);

                if (str_starts_with($tel, '*'))
                    $tel='%'.substr($tel, 1);

                if (str_ends_with($tel, '*'))
                    $tel=substr($tel, 0, -1).'%';
            }
            else
                $tel='%'.StringHelper::normalizeTelephone($filters['tel']).'%';

            $queryBuilder
                ->andWhere($queryBuilder->expr()->like('a.tel', ':tel'))
                ->setParameter('tel', $tel)
            ;
        }

        if (!empty($filters['name']))
        {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->like('a.name', ':name'))
                ->setParameter('name', '%'.$filters['name'].'%')
            ;
        }

        if (!empty($filters['comment']))
        {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->like('a.comment', ':comment'))
                ->setParameter('comment', '%'.$filters['comment'].'%')
            ;
        }

        if (!empty($filters['min_visits']))
        {
            $queryBuilder
                ->groupBy('a.tel')
                ->having('COUNT(a)>=:min_visits')
                ->andWhere($queryBuilder->expr()->isNotNull('a.tel'))
                ->setParameter('min_visits', $filters['min_visits'])
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $telephone
     *
     * @return string
     */
    public function getNameByTelephone(string $telephone): string
    {
        $queryBuilder=$this->getEntityManager()->createQueryBuilder();

        $queryBuilder
            ->select('a.name')
            ->from(Appointments::class, 'a')
            ->where('a.tel = :telephone')
            ->andWhere('a.tel != \'\'')
            ->setParameter('telephone', $telephone)
            ->groupBy('a.name')
            ->addOrderBy('COUNT(a.name)', 'DESC')
            ->addOrderBy('LENGTH(a.name)', 'DESC')
            ->setMaxResults(1)
        ;

        try
        {
            return $queryBuilder->getQuery()->getSingleScalarResult();
        }
        catch (Exception)
        {
            return '';
        }
    }

    /**
     * @return Appointments[]
     */
    public function get3rdVisits()
    {
        $queryBuilder=$this->getEntityManager()->createQueryBuilder();

        $queryBuilder
            ->select('a')
            ->from(Appointments::class, 'a')
            ->groupBy('a.tel')
            ->having('COUNT(a)=3')
            ->andHaving($queryBuilder->expr()->between('MAX(a.date)', strtotime('yesterday'), strtotime('today')))
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}