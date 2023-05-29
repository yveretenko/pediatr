<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vaccines
 *
 * @ORM\Entity
 * @ORM\Table(name="vaccines")
 */
class Vaccines
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", nullable=false)
     */
    private string $short_name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    private ?string $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    private ?string $country;

    /**
     * @var string
     *
     * @ORM\Column(name="age", type="string", nullable=false)
     */
    private string $age;

    /**
     * @var bool
     *
     * @ORM\Column(name="available", type="boolean", nullable=false)
     */
    private bool $available;

    /**
     * @var int|null
     *
     * @ORM\Column(name="purchase_price", type="integer", nullable=true)
     */
    private ?int $purchasePrice;

    /**
     * @var ?string
     *
     * @ORM\Column(name="link", type="string", nullable=true)
     */
    private ?string $link;

    /**
     * @var Vaccines|null
     *
     * @ORM\OneToOne(targetEntity="Vaccines", cascade={"persist"})
     * @ORM\JoinColumn(name="analogue_vaccine_id", referencedColumnName="id")
     */
    private ?Vaccines $analogueVaccine;

    /**
     * @var bool
     *
     * @ORM\Column(name="required", type="boolean", nullable=false)
     */
    private bool $required;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="string", nullable=true)
     */
    private ?string $comment;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name=$name;

        return $this;
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->short_name;
    }

    /**
     * @param string $short_name
     *
     * @return $this
     */
    public function setShortName(string $short_name): self
    {
        $this->short_name=$short_name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return $this
     */
    public function setType(?string $type): self
    {
        $this->type=$type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     *
     * @return $this
     */
    public function setCountry(?string $country): self
    {
        $this->country=$country;

        return $this;
    }

    /**
     * @return string
     */
    public function getAge(): string
    {
        return $this->age;
    }

    /**
     * @param string $age
     *
     * @return $this
     */
    public function setAge(string $age): self
    {
        $this->age=$age;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAvailable(): bool
    {
        return $this->available;
    }

    /**
     * @param bool $available
     *
     * @return $this
     */
    public function setAvailable(bool $available): self
    {
        $this->available=$available;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPurchasePrice(): ?int
    {
        return $this->purchasePrice;
    }

    /**
     * @param int|null $purchasePrice
     *
     * @return $this
     */
    public function setPurchasePrice(?int $purchasePrice): self
    {
        $this->purchasePrice=$purchasePrice;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     *
     * @return $this
     */
    public function setLink(?string $link): self
    {
        $this->link=$link;

        return $this;
    }

    /**
     * @return Vaccines|null
     */
    public function getAnalogueVaccine(): ?Vaccines
    {
        return $this->analogueVaccine;
    }

    /**
     * @param Vaccines|null $analogueVaccine
     *
     * @return $this
     */
    public function setAnalogueVaccine(?Vaccines $analogueVaccine): self
    {
        $this->analogueVaccine=$analogueVaccine;

        return $this;
    }

    /**
     * @return bool
     */
    public function getRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     *
     * @return $this
     */
    public function setRequired(bool $required): self
    {
        $this->required=$required;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     *
     * @return $this
     */
    public function setComment(?string $comment): self
    {
        $this->comment=$comment;

        return $this;
    }
}