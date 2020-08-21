<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     *
     * @ORM\Column(name="value", type="float", nullable=false)
     * @Assert\Type(type="float")
     * @Assert\Regex(pattern="/^([0-9]|0[0-9]|1[0-9])(\.\d+)|20|0$/i", match=true, message="La note doit être un nombre compris entre 0 et 20")
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="lesson", type="string", length=255, nullable=false)
     */
    private $lesson;

    /**
     * @ORM\ManyToOne(targetEntity="Student", inversedBy="marks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_student", referencedColumnName="id")
     * })
     */
    private $student;

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

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }
}
