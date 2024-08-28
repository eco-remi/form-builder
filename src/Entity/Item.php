<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $uuid = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\ManyToMany(targetEntity: Answer::class, inversedBy: 'items')]
    private Collection $Options;

    public function __construct()
    {
        $this->Options = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getOptions(): Collection
    {
        return $this->Options;
    }

    public function addAnswer(Answer $answer): static
    {
        if (!$this->Options->contains($answer)) {
            $this->Options->add($answer);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): static
    {
        $this->Options->removeElement($answer);

        return $this;
    }
}
