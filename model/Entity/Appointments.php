<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Appointments
 *
 * @ORM\Table(name="appointments")
 * @ORM\Entity(repositoryClass="\App\Repository\AppointmentsRepository")
 */
class Appointments
{
    public function __construct()
    {
        $this->vaccines = new ArrayCollection;
    }

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
     * @ORM\Column(name="date", type="integer", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=10, nullable=true)
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=false)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="text", nullable=false)
     */
    private $file;

    /**
     * @var int
     *
     * @ORM\Column(name="sms_notified", type="integer", nullable=false)
     */
    private $smsNotified='0';

    /**
     * @var int
     *
     * @ORM\Column(name="neurology", type="boolean", nullable=false)
     */
    private $neurology=false;

    /**
     * @var int
     *
     * @ORM\Column(name="earlier", type="boolean", nullable=false)
     */
    private $earlier=false;

    /**
     * @var int
     *
     * @ORM\Column(name="call_back", type="boolean", nullable=false)
     */
    private $call_back=false;

    /**
     * @var int|null
     *
     * @ORM\Column(name="created_at", type="integer", nullable=true)
     */
    private $createdAt=null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="updated_at", type="integer", nullable=true)
     */
    private $updatedAt=null;

    /**
     * @var ArrayCollection|Vaccines[]
     *
     * @ORM\ManyToMany(targetEntity="Vaccines")
     * @ORM\JoinTable(name="appointment_vaccines",
     *   joinColumns={@ORM\JoinColumn(name="appointment_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="vaccine_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $vaccines;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id=$id;
    }

    /**
     * @return int
     */
    public function getDate(): int
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date=$date;
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
     */
    public function setName(string $name): void
    {
        $this->name=trim($name);
    }

    /**
     * @return null|string
     */
    public function getTel(): ?string
    {
        return $this->tel;
    }

    /**
     * @param null|int $tel
     */
    public function setTel(?int $tel): void
    {
        $this->tel=$tel;
    }

    /**
     * @param bool $formatted
     *
     * @return string
     */
    public function getComment(bool $formatted=false): string
    {
        if (!$formatted)
            return $this->comment;

        return implode("<br>", array_map(function($line){
            if (str_starts_with($line, '!'))
                return '<span class="text-danger font-weight-bold">'.substr($line, 1).'</span>';

            return $line;
        }, explode("\n", $this->comment)));
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment=trim($comment);
    }

    /**
     * @return string|null
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * @param string|null $file
     */
    public function setFile(?string $file): void
    {
        $this->file=$file;
    }

    /**
     * @return int
     */
    public function getSmsNotified(): int
    {
        return $this->smsNotified;
    }

    /**
     * @param int $smsNotified
     */
    public function setSmsNotified(int $smsNotified): void
    {
        $this->smsNotified=$smsNotified;
    }

    /**
     * @return bool
     */
    public function getNeurology(): bool
    {
        return $this->neurology;
    }

    /**
     * @param bool $neurology
     */
    public function setNeurology(bool $neurology): void
    {
        $this->neurology=$neurology;
    }

    /**
     * @return bool
     */
    public function getEarlier(): bool
    {
        return $this->earlier;
    }

    /**
     * @param bool $earlier
     */
    public function setEarlier(bool $earlier): void
    {
        $this->earlier=$earlier;
    }

    /**
     * @return bool
     */
    public function getCallBack(): bool
    {
        return $this->call_back;
    }

    /**
     * @param bool $call_back
     */
    public function setCallBack(bool $call_back): void
    {
        $this->call_back=$call_back;
    }

    /**
     * @return ArrayCollection|Vaccines[]
     */
    public function getVaccines()
    {
        return $this->vaccines;
    }

    public function setVaccines(ArrayCollection $vaccines): void
    {
        $this->getVaccines()->clear();

        foreach ($vaccines as $vaccine)
            $this->getVaccines()->add($vaccine);
    }

    /**
     * @return int|null
     */
    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    /**
     * @param int|null $createdAt
     */
    public function setCreatedAt(?int $createdAt): void
    {
        $this->createdAt=$createdAt;
    }

    /**
     * @return int|null
     */
    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    /**
     * @param int|null $updatedAt
     */
    public function setUpdatedAt(?int $updatedAt): void
    {
        $this->updatedAt=$updatedAt;
    }
}