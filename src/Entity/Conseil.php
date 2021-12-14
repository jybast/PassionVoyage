<?php

namespace App\Entity;

use App\Repository\ConseilRepository;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ConseilRepository::class)
 */
class Conseil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     */
    private $contenu;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="date")
     */
    private $publierAt;

    /**
     * @Gedmo\Timestampable(on="change", field={"titre", "contenu"})
     * @ORM\Column(type="date", nullable=true)
     */
    private $modifierAt;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $domaine;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getPublierAt(): ?\DateTimeInterface
    {
        return $this->publierAt;
    }

    public function setPublierAt(\DateTimeInterface $publierAt): self
    {
        $this->publierAt = $publierAt;

        return $this;
    }

    public function getModifierAt(): ?\DateTimeInterface
    {
        return $this->modifierAt;
    }

    public function setModifierAt(?\DateTimeInterface $modifierAt): self
    {
        $this->modifierAt = $modifierAt;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDomaine(): ?string
    {
        return $this->domaine;
    }

    public function setDomaine(?string $domaine): self
    {
        $this->domaine = $domaine;

        return $this;
    }
}
