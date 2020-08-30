<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Mark
 *
 * @ORM\Table(name="mark", indexes={@ORM\Index(name="id_student", columns={"id_student"})})
 * @ORM\Entity
 * @ApiResource
 */
class Mark
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups("mark:read")
     */
    private $id;

    /**
     *
     * @ORM\Column(name="value", type="float", nullable=false)
     * @Assert\Type(type="float")
     * @Assert\Regex(pattern="/^([0-1]?[0-9])([,.][0-9][0-9]?)?$|^(20)$/i", match=true, message="La note doit être un nombre décimal avec 2 chiffres après la virgule au maximum et compris entre 0 et 20")
     * @Groups("mark:read")
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="lesson", type="string", length=255, nullable=false)
     * @Groups("mark:read")
     */
    private $lesson;

    /**
     * @ORM\ManyToOne(targetEntity="Student", inversedBy="marks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_student", referencedColumnName="id")
     * })
     */
    public $student;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
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
