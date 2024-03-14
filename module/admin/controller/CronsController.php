<?php

use App\Entity\Appointments;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

function sendSmsAction()
{
    global $em, $config;

    $log=[];

    if ((int)date('G')<9)
        $log[]='Ранувато будити людей';
    else
    {
        $sms_helper = new SMSHelper($config['sms']['login'], $config['sms']['password']);

        $criteria = new Criteria;

        $criteria
            ->andWhere($criteria->expr()->neq('tel', null))
            ->andWhere($criteria->expr()->eq('smsNotified', 0))
            ->andWhere($criteria->expr()->gte('date', time()))
            ->andWhere($criteria->expr()->lte('date', time()+((int)date('G')===20 ? 16 : 6)*60*60))
        ;

        /** @var Appointments[]|ArrayCollection $appointments */
        $appointments=$em->getRepository(Appointments::class)->matching($criteria);

        $log[]="Знайдено записів на найближчі години: ".$appointments->count();

        foreach ($appointments as $appointment)
        {
            $sms_text=sprintf('Чекаємо Вас%s о%s %s в кабінеті педіатра ДітиКвіти, %s', date('Y-m-d', $appointment->getDate())!==date('Y-m-d') ? ' завтра' : '', date('G', $appointment->getDate())=='11' ? 'б' : '', date('G:i', $appointment->getDate()), $config['address']);

            $log[]="<div>Відправляємо смс на номер `".$appointment->getTel()."` з текстом: <i>$sms_text</i>";

            if ($config['send_sms'])
            {
                $sms_helper->sendSMS('DitiKviti', $appointment->getTel(), $sms_text);

                if ($sms_helper->hasErrors())
                {
                    EmailHelper::send('yura11v@gmail.com', 'ДітиКвіти - помилка при відправленні смс', "СМС на номер `".$appointment->getTel()."` з текстом: <blockquote>$sms_text</blockquote>Помилки:<blockquote>".implode('<br><br>', $sms_helper->getErrors())."</blockquote>");

                    $log[]="Помилки при відправці смс: ".implode('<br><br>', $sms_helper->getErrors());
                }
            }

            $appointment->setSmsNotified(1);

            $em->persist($appointment);
        }

        $em->flush();
    }

    die(implode(PHP_EOL, $log));
}

function sendReviewRequestAction()
{
    global $em, $config;

    $log=[];

    $sms_helper = new SMSHelper($config['sms']['login'], $config['sms']['password']);

    /** @var Appointments[]|ArrayCollection $appointments */
    $appointments=$em->getRepository(Appointments::class)->getThirdVisits();

    $log[]="Знайдено телефонів для відправки смс: ".count($appointments);

    foreach ($appointments as $appointment)
    {
        $sms_text=sprintf("Нещодавно ви відвідали кабінет дитячого лікаря ДітиКвіти по %s\n\nМи стараємося і цінуємо вашу думку. Поділіться своїми враженнями: https://g.page/r/CSGLSyAY-HosEAg/review\n\nДякуємо", $config['address']);

        $log[]="<div>Відправляємо смс на номер `".$appointment->getTel()."` з текстом: <i>$sms_text</i>";

        if ($config['send_sms'])
        {
            $sms_helper->sendSMS('DitiKviti', $appointment->getTel(), $sms_text);

            if ($sms_helper->hasErrors())
            {
                EmailHelper::send('yura11v@gmail.com', 'ДітиКвіти - помилка при відправленні смс', "СМС на номер `".$appointment->getTel()."` з текстом: <blockquote>$sms_text</blockquote>Помилки:<blockquote>".implode('<br><br>', $sms_helper->getErrors())."</blockquote>");

                $log[]="Помилки при відправці смс: ".implode('<br><br>', $sms_helper->getErrors());
            }
        }
    }

    die(implode(PHP_EOL, $log));
}

function sendNewYearGreetingsAction()
{
    global $em, $config;

    $log=[];

    $sms_helper = new SMSHelper($config['sms']['login'], $config['sms']['password']);

    $appointment_data=$em->getRepository(Appointments::class)->getByFilters(['min_visits' => 5, 'start_timestamp' => strtotime('January 1')], 'a.id', 'DESC');

    $log[]="Знайдено телефонів для відправки смс: ".count($appointment_data);

    $sent=0;
    foreach ($appointment_data as $appointment)
    {
        /** @var Appointments $appointment */
        $appointment=$appointment['appointment'];

        $sms_text='Кабінет дитячого лікаря "ДітиКвіти" щиро вітає Вас з Новим '.((int)date('Y')+1).' роком. Бажаємо міцного здоров\'я Вашій родині та лише профілактичних оглядів. Дякуємо за довіру!';

        if ($config['send_sms'])
        {
            $sms_helper->sendSMS('DitiKviti', $appointment->getTel(), $sms_text);

            if ($sms_helper->hasErrors())
            {
                EmailHelper::send('yura11v@gmail.com', 'ДітиКвіти - помилка при відправленні смс', "СМС на номер `".$appointment->getTel()."` з текстом: <blockquote>$sms_text</blockquote>Помилки:<blockquote>".implode('<br><br>', $sms_helper->getErrors())."</blockquote>");

                $log[]="Помилки при відправці смс: ".implode('<br><br>', $sms_helper->getErrors());
            }
            else
                $sent++;
        }
    }

    $log[]="Відправлено $sent смс";

    die(implode(PHP_EOL, $log));
}

function backupDbAction()
{
    global $em, $config;

    $log=[];

    try
    {
        // settings
        define('BACKUP_DEST', APPLICATION_TOP_PATH."/backups");
        define('BACKUP_NAME', date("Y-m-d_H-i").".sql.gz");
        define('KEEP_DAYS', 30);

        if (strtoupper(substr(PHP_OS, 0, 3))!=='WIN')
        {
            $db_credentials=$em->getConnection()->getParams();

            $command="mysqldump --single-transaction -h {$db_credentials['host']} -u{$db_credentials['user']} -p{$db_credentials['password']} {$db_credentials['dbname']} | gzip > ".BACKUP_DEST."/".BACKUP_NAME;

            exec($command, $output, $res);
        }
        else
            throw new Exception('Not Unix system');

        if ($res<=0)
        {
            $log[]='Command executed, backup file created';

            if ($config['upload_backups_to_dropbox'])
            {
                $log[]='Trying to upload backup to Dropbox';

                $dropbox_helper = new DropboxHelper;

                try
                {
                    $dropbox_upload_result=$dropbox_helper->uploadFile(BACKUP_DEST.'/'.BACKUP_NAME, '/db');
                }
                catch (Exception $e)
                {
                    $dropbox_upload_result=false;
                    $upload_error=$e->getMessage();
                }

                if ($dropbox_upload_result)
                    $log[]='File uploaded to Dropbox';
                else
                    throw new Exception('Failed to upload file to Dropbox'.(!empty($upload_error) ? ': '.$upload_error : ''));
            }
        }
        else
        {
            $log[]='$res = '.var_export($res, 1);
            $log[]='$output:';
            $log[]='<pre>'.print_r(array_slice($output, -5), 1).'</pre>';

            throw new Exception('Command was not executed correcly, see result and output above');
        }

        // delete backups older than KEEP_DAYS days, but keep Monday backups, so we save 1 weekly backup file, just in case...
        $files_deleted=0;
        $expire_time=KEEP_DAYS*24*60;
        foreach (glob(BACKUP_DEST.'/*') as $filename)
        {
            $file_creation_time=filectime($filename);

            $file_age=time()-$file_creation_time;

            if (date('N', $file_creation_time)!=1 && $file_age>$expire_time*60)
            {
                unlink($filename);
                $files_deleted++;
            }
        }

        $log[]=$files_deleted.' old backup files deleted';

        $log[]=['Backup has been successfully created', 'success'];
    }
    catch (Exception $e)
    {
        $log[]='Error: '.$e->getMessage();
    }

    die(implode(PHP_EOL, $log));
}