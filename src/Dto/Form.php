<?php

namespace App\Dto;

use App\Entity\HydrateStaticTrait;

class Form
{
    use HydrateStaticTrait;

    public ?string $uuid = null;
    public string $title = '';
    /** @var Item[] $items */
    public array $items = [];

    public function setItem(array $items): self
    {
        $this->items = array_map(fn ($user) => (new Item())->hydrate($user), $items);

        return $this;
    }
}