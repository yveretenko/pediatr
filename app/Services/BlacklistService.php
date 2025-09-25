<?php

namespace App\Services;

use App\Models\Blacklist;
use App\Helpers\StringHelper;

class BlacklistService
{
    public function isBlacklisted(string $tel): bool
    {
        $tel=StringHelper::normalizeTelephone($tel);

        return Blacklist::where('tel', $tel)->exists();
    }

    public function getBlacklistReason(string $tel): ?string
    {
        if (!$this->isBlacklisted($tel))
            return null;

        $tel=StringHelper::normalizeTelephone($tel);

        return Blacklist::where('tel', $tel)->first()?->reason_and_name;
    }

    public function search(string $tel)
    {
        $tel=trim($tel);

        $query=Blacklist::query();

        if (str_starts_with($tel, '*') || str_ends_with($tel, '*'))
        {
            if (str_starts_with($tel, '*'))
                $query->where('tel', 'like', '%'.substr($tel, 1));

            if (str_ends_with($tel, '*'))
                $query->where('tel', 'like', substr($tel, 0, -1).'%');
        }
        else
            $query->where('tel', 'like', '%'.StringHelper::normalizeTelephone($tel).'%');

        return $query->get();
    }
}
