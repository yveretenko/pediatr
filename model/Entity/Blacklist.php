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
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", nullable=true)
     */
    private $reason;

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
    public function setReason(?string $reason)
    {
        $this->reason=$reason;

        return $this;
    }
}