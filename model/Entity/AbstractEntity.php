<?php

namespace App\Entity;

use Doctrine\Inflector\InflectorFactory;

class AbstractEntity
{
    public function fromArray(array $array)
    {
        $inflector=InflectorFactory::create()->build();

        foreach ($array as $property => $value)
        {
            $cammel_case_property=$inflector->classify($property);

            if (method_exists($this, 'set'.$cammel_case_property))
                $this->{'set'.$cammel_case_property}($value);
        }
    }

    public function toArray()
    {
        $inflector=InflectorFactory::create()->build();

        $methods=get_class_methods($this);

        $array=[];
        foreach ($methods as $method_name)
        {
            $matches=null;
            if (preg_match('/^get(.+)$/', $method_name, $matches))
                $array[$inflector->tableize($matches[1])]=call_user_func([$this, $method_name]);
        }

        return $array;
    }
}