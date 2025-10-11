<?php

namespace App\Events;

class SmsFailed
{
    public function __construct(
        public string $to,
        public string $text,
        public array $errors
    ) {}
}
