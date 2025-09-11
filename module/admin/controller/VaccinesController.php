<?php

use App\Entity\Vaccines;

function indexAction()
{
    global $layout;

    $layout['title']='<i class="fa fa-vial" aria-hidden="true"></i> Вакцини';

    ViewHelper::render();
}

function filterAction()
{
    global $em;

    $columns=$_GET['columns'];
    $order=$_GET['order'][0];

    $order_mapping=[
        'name'           => 'name',
        'purchase_price' => 'purchasePrice',
        'required'       => 'required',
    ];
    
    $order_by = $columns[$order['column']]['data']==='price' ? 'purchasePrice' : $order_mapping[$columns[$order['column']]['data']];

    /** @var Vaccines[] $vaccines */
    $vaccines=$em->getRepository(Vaccines::class)->findBy([], [$order_by => $order['dir']]);

    $data=[];
    foreach ($vaccines as $vaccine)
    {
        $data[]=[
            'id'               => $vaccine->getId(),
            'name'             => $vaccine->getName(),
            'type'             => $vaccine->getType(),
            'available'        => $vaccine->getAvailable(),
            'comment'          => $vaccine->getComment(),
            'analogue_vaccine' => $vaccine->getAnalogueVaccine()?->getName(),
            'country'          => $vaccine->getCountry(),
            'purchase_price'   => $vaccine->getPurchasePrice(),
            'price'            => $vaccine->getPrice(),
            'link'             => $vaccine->getLink(),
        ];
    }

    die(json_encode([
        'data'            => $data,
        'recordsFiltered' => count($em->getRepository(Vaccines::class)->findAll()),
        'recordsTotal'    => count($em->getRepository(Vaccines::class)->findAll()),
    ]));
}

function saveAction()
{
    global $em;

    $errors=[];

    try
    {
        /** @var Vaccines $vaccine */
        $vaccine=$em->getRepository(Vaccines::class)->find($_POST['id']);

        $vaccine
            ->setPurchasePrice($_POST['purchase_price'] ?: null)
            ->setAvailable($_POST['available']>0)
        ;

        $em->persist($vaccine);

        $em->flush();
    }
    catch (Exception)
    {
        $errors[]='Помилка збереження даних';
    }

    die(json_encode([
        'errors' => $errors,
    ]));
}