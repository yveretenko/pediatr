<?php

use App\Entity\DatesDisabled;

function indexAction()
{
    global $em, $layout;

    $layout['title']='<i class="fa fa-calendar-times" aria-hidden="true"></i> Закриті дати';

    /** @var DatesDisabled[] $dates_disabled */
    $dates_disabled=$em->getRepository(DatesDisabled::class)->findAll();

    $close_dates=[];
    foreach ($dates_disabled as $date)
        $close_dates[]=$date->getDate()->format('d.m.Y');

    ViewHelper::render(compact('close_dates'));
}

function saveAction()
{
    global $em;

    $success=true;

    try
    {
        $em->getConnection()->query('TRUNCATE TABLE dates_disabled');

        foreach ($_POST['dates'] as $date)
        {
            $date = new DateTime(implode(' ', array_slice(explode(' ', $date), 1, 3)));

            $dates_disabled = new DatesDisabled;
            $dates_disabled->setDate($date);

            $em->persist($dates_disabled);
        }

        $em->flush();
    }
    catch (Exception)
    {
        $success=false;
    }

    die(json_encode([
        'success' => $success,
    ]));
}