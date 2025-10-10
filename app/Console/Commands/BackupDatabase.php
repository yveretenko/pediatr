<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

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

                if ($res!==0)
                    throw new Exception('Backup command failed: '.implode(PHP_EOL, $output));

                $log[]="Backup created: $backup_name";

                if (env('UPLOAD_BACKUP_TO_DROPBOX', false))
                {
                    try
                    {
                        $response=Http::asForm()->post('https://api.dropboxapi.com/oauth2/token', [
                            'grant_type'    => 'refresh_token',
                            'refresh_token' => env('DROPBOX_REFRESH_TOKEN'),
                            'client_id'     => env('DROPBOX_APP_KEY'),
                            'client_secret' => env('DROPBOX_APP_SECRET'),
                        ]);

                        $access_token=$response->json()['access_token'];

                        config([
                            'filesystems.disks.dropbox.authorization_token' => $access_token,
                        ]);

                        Storage::disk('dropbox')->put('db/'.$backup_name, file_get_contents("$backup_dest/$backup_name"));

                        $log[]="Backup uploaded to Dropbox";
                    }
                    catch (Exception $e)
                    {
                        $log[]="Failed to upload backup to Dropbox: ".$e->getMessage();
                    }
                }
            }
            else
                throw new Exception('This command works only on Unix-like systems');

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

        Log::channel('cron')->info(implode(PHP_EOL, $log));
    }
}
