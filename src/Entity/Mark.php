<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mark
 *
 * @ORM\Table(name="mark", indexes={@ORM\Index(name="id_student", columns={"id_student"})})
 * @ORM\Entity
 */
class Mark
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
     * @var float
     *
     * @ORM\Column(name="value", type="float", nullable=false)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="lesson", type="string", length=255, nullable=false)
     */
    private $lesson;

    /**
     * @var \Student
     *
     * @ORM\ManyToOne(targetEntity="Student")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_student", referencedColumnName="id")
     * })
     */
    private $idStudent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getLesson(): ?string
    {
        return $this->lesson;
    }

    public function setLesson(string $lesson): self
    {
        $this->lesson = $lesson;

        return $this;
    }

    public function getIdStudent(): ?Student
    {
        return $this->idStudent;
    }

    public function setIdStudent(?Student $idStudent): self
    {
        $this->idStudent = $idStudent;

        return $this;
    }
}
