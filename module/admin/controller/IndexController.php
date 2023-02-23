<?php

use App\Entity\Appointments;
use App\Entity\Users;

function indexAction()
{
    global $layout;

    if (isset($_SESSION['id']))
    {
        header('Location: /admin/appointments/');
        die;
   	}

    $layout['title']='<i class="fa fa-sign-in-alt" aria-hidden="true"></i> Вхід';

    ViewHelper::render();
}

function loginAction()
{
	global $em;

	if (!isset($_SESSION['id']))
    {
        $username=$_POST['username'];
       	$password=md5("ditikviti_".$_POST['password']);

       	if (preg_match("[^A-Za-z0-9_]", $username))
       		die('Incorrect username or password');

        /** @var Users $row */
        $row=$em->getRepository(Users::class)->findOneBy(['username' => $username]);

        if ($row && strcmp($row->getPassword(), $password)===0)
        {
            $_SESSION['id']=$row->getId();
            setcookie(session_name(), session_id(), time()+7*24*60*60, '/', $_SERVER['HTTP_HOST']);
        }
        else
            die('Incorrect username or password');
    }
    else
    {
        header('Location: /admin/');
        die;
    }
}

function logoutAction()
{
	if (isset($_SESSION['id']))
        session_destroy();

	header('Location: /admin/');
    die;
}

function uploadAction()
{
    global $em;

    /** @var $appointment Appointments */
    if (!$_GET['id'] || !$appointment=$em->find(Appointments::class, $_GET['id']))
        die('Appointment not found');

    try
    {
        if (empty($_FILES))
            throw new Exception('No files uploaded');

        if ($appointment->getFile())
            @unlink(APPLICATION_TOP_PATH.'/public/files/'.$appointment->getFile());

        $file=$_FILES[key($_FILES)];

        $filename=mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', basename($file['name']));

        $dst=APPLICATION_TOP_PATH.'/public/files/'.$filename;

        [$name, $ext]=explode('.', $filename);

        $i=1;
        while (is_file($dst))
        {
            $filename="$name ($i).$ext";

            $dst=APPLICATION_TOP_PATH.'/public/files/'.$filename;

            $i++;
        }

        if (!move_uploaded_file($file['tmp_name'], $dst))
            throw new Exception('Can not move uploaded file');

        $appointment->setFile($filename);

        $em->persist($appointment);
        $em->flush();

        die(json_encode([
            'error' => null,
        ]));
    }
    catch (Exception $e)
    {
        die(json_encode([
            'error' => $e->getMessage(),
        ]));
    }
}