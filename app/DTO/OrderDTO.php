<?php

namespace App\DTO;

class OrderDTO
{
    public function __construct(
        public string $column='id',
        public string $dir='asc'
    ) {}
}
