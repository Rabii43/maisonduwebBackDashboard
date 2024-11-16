<?php

namespace App\Entity;

use App\Entity\Traits\TimeStampableTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email', 'phone'])]
#[HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimeStampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 8)]
    private ?string $phone = null;

    #[ORM\Column(length: 30)]
    private ?string $firstName = null;

    #[ORM\Column(length: 30)]
    private ?string $lastName = null;

    //add this Archived boolean field
    #[ORM\Column(type: 'boolean')]
    private bool $archived = false;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $imageUser = null;

    //add this userImage field and nullable
    #[ORM\Column(length: 80, nullable: true)]
    private ?string $qrImageUrl = null;
    #[ORM\Column(length: 40, nullable: true)]
    private ?string $qrImageName = null;

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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticleUser(): Collection
    {
        return $this->articleUser;
    }

    public function addArticleUser(Article $articleUser): static
    {
        if (!$this->articleUser->contains($articleUser)) {
            $this->articleUser->add($articleUser);
            $articleUser->setClient($this);
        }

        return $this;
    }

    public function removeArticleUser(Article $articleUser): static
    {
        if ($this->articleUser->removeElement($articleUser)) {
            // set the owning side to null (unless already changed)
            if ($articleUser->getClient() === $this) {
                $articleUser->setClient(null);
            }
        }

        return $this;
    }

    public function getImageUser(): ?string
    {
        return $this->imageUser;
    }

    public function setImageUser(string $imageUser): static
    {
        $this->imageUser = $imageUser;

        return $this;
    }

    public function getQrImageUrl(): ?string
    {
        return $this->qrImageUrl;
    }

    public function setQrImageUrl(?string $qrImageUrl): void
    {
        $this->qrImageUrl = $qrImageUrl;
    }


    public function getQrImageName(): ?string
    {
        return $this->qrImageName;
    }

    public function setQrImageName(string $qrImageName): static
    {
        $this->qrImageName = $qrImageName;

        return $this;
    }
}
