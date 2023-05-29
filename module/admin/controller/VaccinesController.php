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

    /** @var Vaccines[] $vaccines */
    $vaccines=$em->getRepository(Vaccines::class)->findBy([], [$order_mapping[$columns[$order['column']]['data']] => $order['dir']]);

    $data=[];
    foreach ($vaccines as $vaccine)
    {
        $data[]=[
            'name'             => $vaccine->getName(),
            'type'             => $vaccine->getType(),
            'available'        => $vaccine->getAvailable(),
            'age'              => $vaccine->getAge(),
            'required'         => $vaccine->getRequired(),
            'comment'          => $vaccine->getComment(),
            'analogue_vaccine' => $vaccine->getAnalogueVaccine()?->getName(),
            'country'          => $vaccine->getCountry(),
            'purchase_price'   => $vaccine->getPurchasePrice(),
            'link'             => $vaccine->getLink(),
        ];
    }

    die(json_encode([
        'data'            => $data,
        'recordsFiltered' => count($em->getRepository(Vaccines::class)->findAll()),
        'recordsTotal'    => count($em->getRepository(Vaccines::class)->findAll()),
    ]));
}