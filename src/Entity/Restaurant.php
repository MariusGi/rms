<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RestaurantRepository::class)
 */
class Restaurant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="integer", unique=true)
     */
    private $identification_number;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $max_table_count;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Table", mappedBy="restaurant")
     */
    private $table;

    public function __construct()
    {
        $this->table = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getIdentificationNumber(): ?int
    {
        return $this->identification_number;
    }

    public function setIdentificationNumber(int $identification_number): self
    {
        $this->identification_number = $identification_number;

        return $this;
    }

    public function getMaxTableCount(): ?int
    {
        return $this->max_table_count;
    }

    public function setMaxTableCount(int $max_table_count): self
    {
        $this->max_table_count = $max_table_count;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Table[]
     */
    public function getTable(): Collection
    {
        return $this->table;
    }

    public function addTable(Table $table): self
    {
        if (!$this->table->contains($table)) {
            $this->table[] = $table;
            $table->setRestaurant($this);
        }

        return $this;
    }

    public function removeTable(Table $table): self
    {
        if ($this->table->contains($table)) {
            $this->table->removeElement($table);
            // set the owning side to null (unless already changed)
            if ($table->getRestaurant() === $this) {
                $table->setRestaurant(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return strval($this->id);
    }
}
