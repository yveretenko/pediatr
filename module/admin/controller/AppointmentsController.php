<?php

use App\Entity\Appointments;
use App\Entity\Blacklist;
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
    global $em;

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
            ];

        $blacklist = $appointment->getTel() ? $em->find(Blacklist::class, $appointment->getTel()) : null;

        $data[]=[
            'id'                 => $appointment->getId(),
            'name'               => trim($appointment->getName()),
            'tel'                => $appointment->getTel(),
            'comment'            => $appointment->getComment(),
            'file'               => $appointment->getFile(),
            'readable_date'      => $date,
            'date'               => date('Y-m-d', $appointment->getDate()),
            'time'               => date('H:i', $appointment->getDate()),
            'is_future'          => $appointment->getDate()>time(),
            'is_today'           => date('Y-m-d', $appointment->getDate())===date('Y-m-d'),
            'is_tomorrow'        => date('Y-m-d', $appointment->getDate())===date('Y-m-d', strtotime('tomorrow')),
            'visits_to_date'     => $visits_to_date,
            'blacklisted'        => !is_null($blacklist),
            'blacklisted_reason' => $blacklist?->getReason() ? htmlentities($blacklist->getReason()) : '',
            'vaccines'           => $vaccines,
            'neurology'          => $appointment->getNeurology(),
            'earlier'            => $appointment->getEarlier(),
            'call_back'          => $appointment->getCallBack(),
            'created_at'         => format_date($appointment->getCreatedAt()),
            'updated_at'         => format_date($appointment->getUpdatedAt()),
        ];
    }

    die(json_encode([
        'data'            => $data,
        'recordsFiltered' => $appointments_filtered_count,
        'recordsTotal'    => count($em->getRepository(Appointments::class)->findAll()),
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

    $tel=StringHelper::normalizeTelephone($_POST['tel']);

    if ($tel && strlen($tel)!==10)
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
        if ($appointment->getFile())
            @unlink(APPLICATION_TOP_PATH.'/public/files/'.$appointment->getFile());

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
            'comment'   => nl2br($appointment->getComment()),
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
        'blacklisted_reason' => $blacklist?->getReason(),
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