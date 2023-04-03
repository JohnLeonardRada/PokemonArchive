<?php

namespace App\Entity;

use App\Repository\ElementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ElementRepository::class)
 */
class Element
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Element")
     * @ORM\JoinTable(name="__pokemon_strong_against_relationship",
     *      joinColumns={@ORM\JoinColumn(name="element_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="strong_against_element_id", referencedColumnName="id")}
     * )
     */
    private $strongAgainst;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Element")
     * @ORM\JoinTable(name="__pokemon_weak_against_relationship",
     *      joinColumns={@ORM\JoinColumn(name="element_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="weak_against_element_id", referencedColumnName="id")}
     * )
     */
    private $weakAgainst;

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
        $this->strongAgainst = new ArrayCollection();
        $this->weakAgainst = new ArrayCollection();
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

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Element>
     */
    public function getStrongAgainst(): Collection
    {
        return $this->strongAgainst;
    }

    public function setStrongAgainst(array $strongAgainst): self
    {
        $this->strongAgainst = new ArrayCollection($strongAgainst);

        return $this;
    }

    public function addStrongAgainst(Element $strongAgainst): self
    {
        if (!$this->strongAgainst->contains($strongAgainst)) {
            $this->strongAgainst[] = $strongAgainst;
        }

        return $this;
    }

    public function removeStrongAgainst(Element $strongAgainst): self
    {
        $this->strongAgainst->removeElement($strongAgainst);

        return $this;
    }

    /**
     * @return Collection<int, Element>
     */
    public function getWeakAgainst(): Collection
    {
        return $this->weakAgainst;
    }

    public function setWeakAgainst(array $weakAgainst): self
    {
        $this->weakAgainst = new ArrayCollection($weakAgainst);

        return $this;
    }

    public function addWeakAgainst(Element $weakAgainst): self
    {
        if (!$this->weakAgainst->contains($weakAgainst)) {
            $this->weakAgainst[] = $weakAgainst;
        }

        return $this;
    }

    public function removeWeakAgainst(Element $weakAgainst): self
    {
        $this->weakAgainst->removeElement($weakAgainst);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

}
