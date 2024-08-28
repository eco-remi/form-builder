<?php

namespace App\Dto;

use App\Entity\HydrateStaticTrait;

class Item
{
    use HydrateStaticTrait;

    public ?string $uuid = null;
    public string $question = '';
    public array $options = [];
    public ?string $answer = null;
    public ?string $result = '';
}