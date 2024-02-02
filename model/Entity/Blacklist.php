<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Blacklist
 *
 * @ORM\Table(name="blacklist")
 * @ORM\Entity
 */
class Blacklist
{
    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected string $tel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reason", type="string", nullable=true)
     */
    private ?string $reason;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private ?string $name;

    /**
     * @return string
     */
    public function getTel(): string
    {
        return $this->tel;
    }

    /**
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param string|null $reason
     *
     * @return $this
     */
    public function setReason(?string $reason): self
    {
        $this->reason=$reason;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name=$name;

        return $this;
    }

    public function getReasonAndName(): ?string
    {
        if ($this->getReason() && $this->getName())
            return sprintf('%s (%s)', $this->getReason(), $this->getName());

        if ($this->getReason())
            return $this->getReason();

        return $this->getName();
    }
}