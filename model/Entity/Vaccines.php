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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", nullable=false)
     */
    private string $short_name;

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
}