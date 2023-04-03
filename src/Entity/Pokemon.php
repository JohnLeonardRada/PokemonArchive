<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PokemonRepository::class)
 */
class Pokemon
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $region;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Element")
     * @ORM\JoinTable(name="__pokemon_element_relationship",
     *      joinColumns={@ORM\JoinColumn(name="pokemon_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="element_id", referencedColumnName="id")}
     * )
     */
    private $elements;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Trainer", mappedBy="pokemon")
     */
    private $trainer;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

     /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
        $this->trainer = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Element>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Element $element): self
    {
        if (!$this->elements->contains($element)) {
            $this->elements[] = $element;
        }

        return $this;
    }

    public function removeElement(Element $element): self
    {
        $this->elements->removeElement($element);

        return $this;
    }

    /**
     * @return Collection<int, Trainer>
     */
    public function getTrainer(): Collection
    {
        return $this->trainer;
    }

    public function addTrainer(Trainer $trainer): self
    {
        if (!$this->trainer->contains($trainer)) {
            $this->trainer[] = $trainer;
            $trainer->addPokemon($this);
        }

        return $this;
    }

    public function removeTrainer(Trainer $trainer): self
    {
        if ($this->trainer->removeElement($trainer)) {
            $trainer->removePokemon($this);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
