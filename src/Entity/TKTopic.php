<?php

namespace App\Entity;

use App\Repository\TKTopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TKTopicRepository::class)]
class TKTopic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $subject = null;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: TKPost::class)]
    private Collection $tKPosts;

    public function __construct()
    {
        $this->tKPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return Collection<int, TKPost>
     */
    public function getTKPosts(): Collection
    {
        return $this->tKPosts;
    }

    public function addTKPost(TKPost $tKPost): static
    {
        if (!$this->tKPosts->contains($tKPost)) {
            $this->tKPosts->add($tKPost);
            $tKPost->setSubject($this);
        }

        return $this;
    }

    public function removeTKPost(TKPost $tKPost): static
    {
        if ($this->tKPosts->removeElement($tKPost)) {
            // set the owning side to null (unless already changed)
            if ($tKPost->getSubject() === $this) {
                $tKPost->setSubject(null);
            }
        }

        return $this;
    }
}
