<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NewsToPlayer
 *
 * @ORM\Table(name="appointment_vaccines")
 * @ORM\Entity(repositoryClass="\App\Repository\AppointmentVaccinesRepository")
 */
class AppointmentVaccines extends AbstractEntity
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
     * @var Appointments
     *
     * @ORM\ManyToOne(targetEntity="Appointments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="appointment_id", referencedColumnName="id")
     * })
     */
    private $appointment;

    /**
     * @var Vaccines
     *
     * @ORM\ManyToOne(targetEntity="Vaccines")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vaccine_id", referencedColumnName="id")
     * })
     */
    private $vaccine;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Appointments
     */
    public function getAppointment(): Appointments
    {
        return $this->appointment;
    }

    /**
     * @return Vaccines
     */
    public function getVaccine(): Vaccines
    {
        return $this->vaccine;
    }
}