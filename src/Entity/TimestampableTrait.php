<?php

/**
 * copied/pasted from vendor/gedmo/doctrine-extensions/src/Timestampable/Traits/TimestampableTrait.php
 * for serialization groups (+ full PHP 8)
 */

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait TimestampableTrait
{
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected \DateTime $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected \DateTime $updatedAt;

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
}
