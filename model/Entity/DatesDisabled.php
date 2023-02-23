<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * DatesDisabled
 *
 * @ORM\Table(name="dates_disabled")
 * @ORM\Entity
 */
class DatesDisabled
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
     * @var DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private DateTime $date;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return $this
     */
    public function setDate(DateTime $date): self
    {
        $this->date=$date;

        return $this;
    }
}