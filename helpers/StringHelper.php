<?php

class StringHelper
{
    public static function camelize($string, $separator='_'): string
    {
        return lcfirst(str_replace($separator, '', ucwords($string, $separator)));
    }

    public static function decamelize($string, $glue='_'): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', $glue.'$0', $string));
    }

    public static function normalizeTelephone($tel): ?string
    {
        $tel = $tel ? preg_replace('/[^0-9.]+/', '', $tel) : null;

        if ($tel && strlen($tel)===12 && str_starts_with($tel, '38'))
            $tel=substr($tel, 2);

        return $tel;
    }
}