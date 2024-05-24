<?php

use App\Entity\Appointments;
use App\Entity\Blacklist;
use App\Entity\DateComments;
use App\Entity\DatesDisabled;
use App\Entity\Vaccines;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

function days_ago($timestamp)
{
    $days_ago=(strtotime('today')-strtotime(date('Y-m-d', $timestamp)))/86400;

    if ($days_ago<1)
        return '';
    elseif ($days_ago<2)
        return '<div class="text-success font-weight-bold">вчора</div>';
    elseif ($days_ago<7)
        return '<div class="text-success font-weight-bold">&lt;7д</div>';
    elseif ($days_ago<14)
        return '<14д';
    elseif ($days_ago<30)
        return '<1м';
    elseif ($days_ago<60)
        return '<2м';
    elseif ($days_ago<90)
        return '<3м';
    elseif ($days_ago<180)
        return '<6м';
    elseif ($days_ago<365)
        return '<1р';
    elseif ($days_ago<365*2)
        return '<div class="text-danger font-weight-bold">&gt;1р</div>';
    else
        return '<div class="text-danger font-weight-bold">&gt;2р</div>';
}

function format_date($timestamp)
{
    if (!$timestamp)
        return null;

    if (date('Ymd', $timestamp)===date('Ymd'))
        $date='Сьогодні';
    elseif (date('Ymd', $timestamp)===date('Ymd', strtotime('-1 day')))
        $date='Вчора';
    else
        $date=date('d/m/Y', $timestamp);

    return $date.' '.date('H:i', $timestamp);
}

function indexAction()
{
    global $layout, $em;

    $layout['title']='<i class="fa fa-list" aria-hidden="true"></i> Записи';

    $vaccines=$em->getRepository(Vaccines::class)->findBy([], ['name' => 'ASC']);

    ViewHelper::render(compact('vaccines'));
}

function filterAction()
{
    global $em, $config;

    $weekdays=[
        '',
        'ПН',
        'ВТ',
        'СР',
        'ЧТ',
        'ПТ',
        'СБ',
        'НД',
    ];

    $months=[
        'січня',
        'лютого',
        'березня',
        'квітня',
        'травня',
        'червня',
        'липня',
        'серпня',
        'вересня',
        'жовтня',
        'листопада',
        'грудня',
    ];

    $start_timestamp=$end_timestamp=null;

    if ($_GET['filters']['date'])
    {
        $start_timestamp=strtotime($_GET['filters']['date']);
        $end_timestamp=strtotime($_GET['filters']['date'].' 23:59:59');
    }

    $filters=[
        'start_timestamp' => $start_timestamp,
        'end_timestamp'   => $end_timestamp,
        'tel'             => $_GET['filters']['tel'],
        'name'            => $_GET['filters']['name'],
        'comment'         => $_GET['filters']['comment'],
        'vaccine'         => $_GET['filters']['vaccine'],
    ];

    $appointments_filtered_count=count($em->getRepository(Appointments::class)->getByFilters($filters, 'a.date', 'DESC'));

    $appointments_data=$em->getRepository(Appointments::class)->getByFilters($filters, 'a.date', 'DESC', $_GET['start'], $_GET['length']);

    $data=[];
    foreach ($appointments_data as $appointment_data)
    {
        /** @var Appointments $appointment */
        $appointment=$appointment_data['appointment'];

        if (date('Ymd')===date('Ymd', $appointment->getDate()))
            $date='Сьогодні';
        elseif (date('Ymd', strtotime('tomorrow'))===date('Ymd', $appointment->getDate()))
            $date='Завтра';
        else
            $date=date('d/m/y', $appointment->getDate()).' <small>'.$weekdays[date('N', $appointment->getDate())].'</small>';

        $visits_to_date=0;

        if ($appointment->getTel())
        {
            $criteria = new Criteria;

            $criteria->andWhere($criteria->expr()->eq('tel', $appointment->getTel()));
            $criteria->andWhere($criteria->expr()->lte('date', time()));

            $visits_to_date=$em->getRepository(Appointments::class)->matching($criteria)->count();
        }

        $vaccines=[];
        foreach ($appointment->getVaccines() as $vaccine)
            $vaccines[]=[
                'id'         => $vaccine->getId(),
                'name'       => $vaccine->getName(),
                'short_name' => $vaccine->getShortName(),
                'available'  => $vaccine->getAvailable(),
            ];

        $blacklist = $appointment->getTel() ? $em->find(Blacklist::class, $appointment->getTel()) : null;

        $appointment_text=sprintf(
            "Лікар чекатиме Вас %s %s\n\nНаша адреса %s\n\n%s вiзиту Вам надiйде смс-нагадування\n\nДякуємо, що довіряєте нам! ❤️",
            date('j', $appointment->getDate()).' '.$months[date('n', $appointment->getDate())-1],
            (date('H', $appointment->getDate())==='11' ? 'об' : 'о').' '.date('H:i', $appointment->getDate()),
            $config['address'],
            (date('H', $appointment->getDate())<12 || (date('H:i', $appointment->getDate())==='12:00')) ? 'За день до' : 'В день'
        );

        $next_appointment_criteria = new Criteria;

        $next_appointment_criteria
            ->andWhere($next_appointment_criteria->expr()->gt('date', $appointment->getDate()))
            ->andWhere($next_appointment_criteria->expr()->lt('date', strtotime('tomorrow', $appointment->getDate())))
            ->orderBy(['date' => 'ASC'])
        ;

        /** @var Appointments $next_appointment */
        $next_appointment=$em->getRepository(Appointments::class)->matching($next_appointment_criteria)->first();

        $data[]=[
            'id'                 => $appointment->getId(),
            'name'               => trim($appointment->getName()),
            'tel'                => $appointment->getTel(),
            'comment'            => $appointment->getComment(),
            'comment_formatted'  => $appointment->getComment(true),
            'file'               => $appointment->getFile(),
            'readable_date'      => $date,
            'date'               => date('Y-m-d', $appointment->getDate()),
            'time'               => date('H:i', $appointment->getDate()),
            'duration'           => $next_appointment ? round(($next_appointment->getDate()-$appointment->getDate())/60) : null,
            'is_future'          => $appointment->getDate()>time(),
            'is_today'           => date('Y-m-d', $appointment->getDate())===date('Y-m-d'),
            'is_tomorrow'        => date('Y-m-d', $appointment->getDate())===date('Y-m-d', strtotime('tomorrow')),
            'visits_to_date'     => $visits_to_date,
            'blacklisted'        => !is_null($blacklist),
            'blacklisted_reason' => htmlentities($blacklist?->getReasonAndName() ?? ''),
            'vaccines'           => $vaccines,
            'neurology'          => $appointment->getNeurology(),
            'earlier'            => $appointment->getEarlier(),
            'call_back'          => $appointment->getCallBack(),
            'created_at'         => format_date($appointment->getCreatedAt()),
            'updated_at'         => format_date($appointment->getUpdatedAt()),
            'appointment_text'   => $appointment_text,
        ];
    }

    $start_date = new DateTime('Monday'.(date('N')<6 ? ' this week' : ''));

    $dates = new DatePeriod($start_date, new DateInterval('P1D'), new DateTime($start_date->format('Y-m-d').' + 28 days'));

    $data_by_dates=[];
    foreach ($dates as $date)
    {
        $appointments=$em->getRepository(Appointments::class)->getByFilters(['start_timestamp' => $date->getTimestamp(), 'end_timestamp' => $date->getTimestamp()+24*60*60]);

        $vaccines_count=0;

        foreach ($appointments as $appointment_data)
        {
            /** @var Appointments $appointment */
            $appointment=$appointment_data['appointment'];

            $vaccines_count+=$appointment->getVaccines()->count();
        }

        $data_by_dates[$date->format('Y-m-d')]=[
            'appointments_count' => count($appointments),
            'vaccines_count'     => $vaccines_count,
            'is_disabled'        => !is_null($em->getRepository(DatesDisabled::class)->findOneBy(['date' => $date])),
            'comment'            => $em->getRepository(DateComments::class)->findOneBy(['date'=> $date])?->getComment(),
            'is_future'          => $date >= new DateTime('today'),
        ];
    }

    $criteria = new Criteria;

    $tel=trim($filters['tel']);

    $blacklist=[];
    if ($tel)
    {
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

        foreach ($blacklisted as $blacklisted_item)
            $blacklist[$blacklisted_item->getTel()]=$blacklisted_item->getReasonAndName();
    }

    die(json_encode([
        'data'            => $data,
        'recordsFiltered' => $appointments_filtered_count,
        'recordsTotal'    => count($em->getRepository(Appointments::class)->findAll()),
        'dates'           => $data_by_dates,
        'blacklist'       => $blacklist,
    ]));
}

function saveAction()
{
    global $em;

    $errors=[];

    if (!$_POST['date'])
        $errors[]='Введіть дату';
    else
    {
        [$year, $month, $date]=explode('-', $_POST['date']);

        if (!checkdate($month, $date, $year))
            $errors[]='Невірна дата';
    }

    if (!$_POST['time'])
        $errors[]='Введіть час';
    else
    {
        list($hour, $minute)=explode(':', $_POST['time']);

        if ($hour<8 || ($hour>21 || ($hour==='21' && $minute>0)))
            $errors[]='Введіть час між 8:00 та 21:00';
    }

    $tel=StringHelper::normalizeTelephone($_POST['tel']);

    if ($_POST['tel'] && strlen($tel)!==10)
        $errors[]='Невірний номер телефону';

    if ($_POST['date'] && $_POST['time'])
    {
        $exists_criteria = new Criteria;

        $exists_criteria
            ->andWhere($exists_criteria->expr()->eq('date', strtotime($_POST['date'].' '.$_POST['time'])))
            ->andWhere($exists_criteria->expr()->neq('id', $_POST['id']))
        ;

        if ($em->getRepository(Appointments::class)->matching($exists_criteria)->count())
            $errors[]='На цей час вже є запис';
    }

    if (!count($errors))
    {
        try
        {
            /** @var Appointments $appointment */
            $appointment = $_POST['id'] ? $em->getRepository(Appointments::class)->find($_POST['id']) : new Appointments;

            if ($_POST['id'])
                $old_date=$appointment->getDate();

            $vaccines = new ArrayCollection;
            foreach($_POST['vaccines'] as $vaccine_id)
                $vaccines->add($em->getRepository(Vaccines::class)->find($vaccine_id));

            $appointment->setName($_POST['name']);
            $appointment->setTel($tel);
            $appointment->setDate(strtotime($_POST['date'].' '.$_POST['time']));
            $appointment->setComment($_POST['comment']);
            $appointment->setVaccines($vaccines);
            $appointment->setNeurology($_POST['neurology']==='1');
            $appointment->setEarlier($_POST['earlier']==='1');
            $appointment->setCallBack($_POST['call_back']==='1');

            if (!$_POST['id'])
                $appointment->setCreatedAt(time());
            else
                $appointment->setUpdatedAt(time());

            if (isset($old_date) && $old_date!==$appointment->getDate())
                $appointment->setSmsNotified(0);

            $em->persist($appointment);

            $em->flush();
        }
        catch (Exception $e)
        {
            $errors[]=$e->getMessage();
        }
    }

    die(json_encode([
        'errors' => $errors,
    ]));
}

function deleteAction()
{
    global $em;

    /** @var Appointments $appointment */
    $appointment=$em->getRepository(Appointments::class)->find($_POST['id']);

    if (!$appointment)
    {
        http_response_code(404);
        die;
    }

    $success=true;

    try
    {
        if ($appointment->getFile() && is_file(APPLICATION_TOP_PATH.'/public/files/'.$appointment->getFile()))
            unlink(APPLICATION_TOP_PATH.'/public/files/'.$appointment->getFile());

        $em->remove($appointment);

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

function historyAction()
{
    global $em;

    $criteria = new Criteria;

    $criteria
        ->andWhere($criteria->expr()->eq('tel', $_POST['tel']))
        ->andWhere($criteria->expr()->lt('date', time()))
        ->orderBy(['date' => 'DESC'])
    ;

    /** @var Appointments[] $appointments */
    $appointments=$em->getRepository(Appointments::class)->matching($criteria);

    $data=[];
    foreach ($appointments as $appointment)
    {
        $vaccines=[];
        foreach ($appointment->getVaccines() as $vaccine)
            $vaccines[]=[
                'id'         => $vaccine->getId(),
                'name'       => $vaccine->getName(),
                'short_name' => $vaccine->getShortName(),
            ];

        $data[]=[
            'id'        => $appointment->getId(),
            'date'      => date('Y-m-d', $appointment->getDate())===date('Y-m-d') ? 'Сьогодні' : date('d/m/y', $appointment->getDate()),
            'days_ago'  => days_ago($appointment->getDate()),
            'name'      => $appointment->getName(),
            'comment'   => $appointment->getComment(true),
            'file'      => $appointment->getFile(),
            'vaccines'  => $vaccines,
            'neurology' => $appointment->getNeurology(),
            'earlier'   => $appointment->getEarlier(),
            'call_back' => $appointment->getCallBack(),
        ];
    }

    die(json_encode($data));
}

function graphAction()
{
    global $layout;

    $layout['title']='<i class="fa fa-chart-bar" aria-hidden="true"></i> Статистика';

    ViewHelper::render();
}

function graphDataAction()
{
    global $em;

    $criteria = new Criteria;

    $criteria
        ->where($criteria->expr()->gt('date', strtotime('2021-01-04')))
        ->orderBy(['date' => 'ASC'])
    ;

    /** @var Appointments[] $appointments */
    $appointments=$em->getRepository(Appointments::class)->matching($criteria);

    $appointments_by_date=[];
    foreach ($appointments as $appointment)
        $appointments_by_date[date('Y-m-d', $appointment->getDate())]++;

    die(json_encode($appointments_by_date));
}

function getByTelephoneAction()
{
    global $em;

    $tel=StringHelper::normalizeTelephone($_POST['tel']);

    $name=$em->getRepository(Appointments::class)->getNameByTelephone(StringHelper::normalizeTelephone($tel));

    $blacklist=$em->find(Blacklist::class, $tel);

    die(json_encode([
        'name'               => $name,
        'blacklisted'        => !is_null($blacklist),
        'blacklisted_reason' => $blacklist?->getReasonAndName(),
    ]));
}

function appointmentsCountByDateAction()
{
    global $em;

    $appointments=$em->getRepository(Appointments::class)->findAll();

    $count_by_date=[];
    foreach ($appointments as $appointment)
        $count_by_date[date('Y-m-d', $appointment->getDate())]++;

    die(json_encode($count_by_date));
}

function fileAction()
{
    global $em;

    /** @var Appointments $appointment */
    $appointment = $_GET['id'] ? $em->find(Appointments::class, $_GET['id']) : null;

    if (!$appointment || !$appointment->getFile() || !is_file(APPLICATION_TOP_PATH.'/public/files/'.$appointment->getFile()))
    {
        http_response_code(404);
        die('<h1>404 сторінку не знайдено</h1>');
    }

    $file_path=APPLICATION_TOP_PATH.'/public/files/'.$appointment->getFile();

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file_path)).'"';
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: '.filesize($file_path));

    ob_clean();
    flush();
    readfile($file_path);
    exit;
}

function filesAction()
{
    global $layout, $em;

    $layout['title']='<i class="far fa-file-alt" aria-hidden="true"></i> Файли';

    $criteria = new Criteria;

    $criteria->andWhere($criteria->expr()->neq('file', null));
    $criteria->orderBy(['file' => 'ASC']);

    $appointments_with_file=$em->getRepository(Appointments::class)->matching($criteria);

    ViewHelper::render(compact('appointments_with_file'));
}