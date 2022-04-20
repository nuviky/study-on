<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'text')]
    private $lessonContent;

    #[ORM\Column(type: 'integer')]
    #[Assert\Range(min: 0, max: 10000)]
    private $lessonNumber;

    #[ORM\ManyToOne(targetEntity: Course::class, cascade: ['persist'], inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private $courseRelation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLessonContent(): ?string
    {
        return $this->lessonContent;
    }

    public function setLessonContent(string $lessonContent): self
    {
        $this->lessonContent = $lessonContent;

        return $this;
    }

    public function getLessonNumber(): ?int
    {
        return $this->lessonNumber;
    }

    public function setLessonNumber(int $lessonNumber): self
    {
        $this->lessonNumber = $lessonNumber;

        return $this;
    }

    public function getCourseRelation(): ?Course
    {
        return $this->courseRelation;
    }

    public function setCourseRelation(?Course $courseRelation): self
    {
        $this->courseRelation = $courseRelation;

        return $this;
    }
}
