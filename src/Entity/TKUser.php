<?php

namespace App\Entity;

use App\Repository\TKUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


#[ORM\Entity(repositoryClass: TKUserRepository::class)]
class TKUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:255, unique:true)]
    private ?string $email = null;

    #[ORM\Column(type:"json")]
    private $roles = [];

    #[ORM\Column(type:"string")]
    private $firstName;

    #[ORM\Column(type:"string")]
    private $secondName;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    private ?string $plainPassword;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: TKPost::class)]
    private Collection $tKPosts;

    public function __construct()
    {
        $this->tKPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    // Its only written for backwards compatibility. Its never used. It coluld be removed in Symfony 6.
    public function getPassword(): ?string 
    {
        return $this->password;
    }
    
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSecondName(): ?string
    {
        return $this->secondName;
    }

    public function setSecondName(string $secondName): self
    {
        $this->secondName = $secondName;

        return $this;
    }

    public function getUserIdentifier(): string {
        return (string) $this->email;
    }

    public function getRoles(): array {
        $roles = $this->roles;

        // Adds this value to the end of tha array.
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(?array $roles): self {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials() {

        // If you store any temporary, sensitive data on the user, clear it here
         $this->plainPassword = null;

    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPlainPassword(): ?string 
    {
        return $this->plainPassword;
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
            $tKPost->setAuthor($this);
        }

        return $this;
    }

    public function removeTKPost(TKPost $tKPost): static
    {
        if ($this->tKPosts->removeElement($tKPost)) {
            // set the owning side to null (unless already changed)
            if ($tKPost->getAuthor() === $this) {
                $tKPost->setAuthor(null);
            }
        }

        return $this;
    }
}
