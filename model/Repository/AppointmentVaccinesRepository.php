<?php

namespace App\Repository;

use App\Entity\AppointmentVaccines;
use Doctrine\ORM\EntityRepository;

class AppointmentVaccinesRepository extends EntityRepository
{
    public function findFutureAppointmentsVaccines()
    {
        $queryBuilder=$this->getEntityManager()->createQueryBuilder();

        $queryBuilder
            ->select('av')
            ->from(AppointmentVaccines::class, 'av')
            ->leftJoin('av.vaccine', 'v')
            ->leftJoin('av.appointment', 'a')
            ->where('a.date>'.time())
            ->orderBy('v.name', 'ASC')
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}