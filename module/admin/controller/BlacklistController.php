<?php

use App\Entity\Blacklist;
use Doctrine\Common\Collections\Criteria;

function getByTelephoneAction()
{
    global $em;

    $criteria = new Criteria;

    $tel=$_POST['tel'];

    if (str_starts_with($tel, '*') || str_ends_with($tel, '*'))
    {
        if (str_starts_with($tel, '*'))
            $criteria->andWhere(Criteria::expr()->endsWith('tel', substr($tel, 1)));

        if (str_ends_with($tel, '*'))
            $criteria->andWhere(Criteria::expr()->startsWith('tel', substr($tel, 0, -1)));
    }
    else
        $criteria->andWhere(Criteria::expr()->contains('tel', StringHelper::normalizeTelephone($tel)));

    /** @var Blacklist[] $blacklisted */
    $blacklisted=$em->getRepository(Blacklist::class)->matching($criteria);

    $result=[];
    foreach ($blacklisted as $blacklisted_item)
        $result[$blacklisted_item->getTel()]=$blacklisted_item->getReason();

    die(json_encode($result));
}