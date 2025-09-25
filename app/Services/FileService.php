<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    protected string $disk='public';
    protected string $folder='files';

    public function upload(UploadedFile $file): string
    {
        // sanitize original filename
        $filename=pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension=$file->getClientOriginalExtension();
        $safeName=preg_replace("/([^\p{Cyrillic}\w\s\d\-_~,;\[\]\(\).])/u", '', $filename);
        $finalName=$safeName.'.'.$extension;

        // ensure uniqueness
        $i=1;
        while (Storage::disk($this->disk)->exists("$this->folder/$finalName"))
        {
            $finalName=$safeName." ($i).".$extension;
            $i++;
        }

        $file->storeAs($this->folder, $finalName, $this->disk);

        return $finalName;
    }

    public function delete(?string $filename): bool
    {
        if ($filename && Storage::disk($this->disk)->exists("$this->folder/$filename"))
            return Storage::disk($this->disk)->delete("$this->folder/$filename");

        return false;
    }

    public function getPath(string $filename): string
    {
        return storage_path("app/$this->disk/$this->folder/$filename");
    }

    public function exists(string $filename): bool
    {
        return Storage::disk($this->disk)->exists("$this->folder/$filename");
    }
}
