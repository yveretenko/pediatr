<?php

namespace App\Console\Commands;

use App\Services\DropboxService;
use Illuminate\Console\Command;
use Exception;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    protected $signature='db:backup';
    protected $description='Create a daily backup of the database and upload to Dropbox';

    public function handle()
    {
        $log=[];

        $backup_dest=storage_path('backups');
        $backup_name=date("Y-m-d_H-i").".sql.gz";
        $keep_days=30;

        try
        {
            if (strtoupper(substr(PHP_OS, 0, 3))!=='WIN')
            {
                $db=config('database.connections.mysql');

                $command=sprintf(
                    "mysqldump --single-transaction -h %s -u%s -p%s %s | gzip > %s/%s",
                    $db['host'],
                    $db['username'],
                    $db['password'],
                    $db['database'],
                    $backup_dest,
                    $backup_name
                );

                exec($command, $output, $res);
            }
            else
                throw new Exception('Not Unix system');

            if ($res<=0)
            {
                $log[]='Command executed, backup file created';

                if (env('UPLOAD_BACKUP_TO_DROPBOX', false))
                {
                    $log[]='Trying to upload backup to Dropbox';

                    $service = new DropboxService;

                    try
                    {
                        if ($service->uploadFile("$backup_dest/$backup_name", '/db'))
                            $log[]='File uploaded to Dropbox';
                        else
                            throw new Exception('Failed to upload file to Dropbox');
                    }
                    catch (Exception $e)
                    {
                        throw new Exception('Failed to upload file to Dropbox: '.$e->getMessage());
                    }
                }
            }
            else
            {
                $log[]='$res = '.var_export($res, true);
                $log[]='$output:';
                $log[]=print_r(array_slice($output, -5), true);

                throw new Exception('Command was not executed correctly, see result and output above');
            }

            // Delete old backups, except Monday files
            $files_deleted=0;
            foreach (glob("$backup_dest/*") as $filename)
            {
                $file_age=time()-filectime($filename);

                if (date('N', filectime($filename))!=1 && $file_age>$keep_days*24*60*60)
                {
                    unlink($filename);
                    $files_deleted++;
                }
            }

            $log[]="$files_deleted old backup files deleted";
            $log[]='Backup has been successfully created';
        }
        catch (Exception $e)
        {
            $log[]='Error: '.$e->getMessage();
        }

        Log::info(implode(PHP_EOL, $log));
    }
}
