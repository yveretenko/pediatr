<?php

use App\Entity\Appointments;
use App\Entity\Blacklist;

function indexAction()
{
    global $em;

    if (empty($_POST['name']) || empty($_POST['message']) || empty($_POST['phone']))
    {
        http_response_code(500);
        exit();
    }

    $name=strip_tags(htmlspecialchars(trim($_POST['name'])));
    $tel=trim(strip_tags(htmlspecialchars($_POST['phone'])));
    $message=strip_tags(htmlspecialchars($_POST['message']));
    $date=strip_tags(htmlspecialchars($_POST['date']));
    $age=strip_tags(htmlspecialchars($_POST['age']));

    $visits_count=count($em->getRepository(Appointments::class)->findBy(['tel' => StringHelper::normalizeTelephone($tel)]));

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $convert_link='http://pediatr.cv.ua/admin/appointments/?'.http_build_query($_POST);

    $convert_button='<A href="'.$convert_link.'">Конвертувати в запис</A>';

    /** @var Blacklist $blacklist_record */
    $blacklist_record=$em->getRepository(Blacklist::class)->findOneBy(['tel'=> StringHelper::normalizeTelephone($tel)]);

    $body=[
        "Ім'я дитини: $name",
        "Вік дитини: $age",
        "Телефон: <A href='tel:".StringHelper::normalizeTelephone($tel)."'>$tel</A> (".($visits_count ? "за цим номером знайдено <A href='http://pediatr.cv.ua/admin/appointments?tel=".StringHelper::normalizeTelephone($tel)."'>$visits_count запис".($visits_count> 1 ? ($visits_count<5 ? 'и' : 'ів') : '')."</A>" : 'за цим номером не знайдено записів').")",
        $blacklist_record ? 'Телефон в чорному списку, причина: '.($blacklist_record->getReasonAndName() ?: '-') : null,
        $date ? "Бажана дата: $date (".['', 'понеділок', 'вівторок', 'середа', 'четвер', 'п\'ятниця'][date('N', strtotime($date))].')' : null,
        "Причина звернення:",
        nl2br($message),
        $convert_button,
    ];

    if (!EmailHelper::send('yura11v@gmail.com', sprintf('Запит з pediatr.cv.ua (%s)%s', strip_tags(trim($_POST['name'])), !is_null($blacklist_record) ? ' телефон в чорному списку' : ''), implode('<br><br>', array_filter($body))))
        http_response_code(500);
}