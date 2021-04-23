<?php

namespace App\Entity;

use App\Entity\Media;
use App\Entity\Motcle;
use App\Entity\Categorie;
use App\Entity\Commentaire;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArticleRepository;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
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
     * @ORM\Column(type="string", length=255)
     */
    private $legende;

    /**
     * @Gedmo\Slug(fields={"titre"})
     * @ORM\Column(type="string", length=255)S
     * 
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $sommaire;

    /**
     * @ORM\Column(type="text")
     */
    private $contenu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valide = false;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="date")
     *
     */
    private $publierAt;

    /**
     * @Gedmo\Timestampable(on="change", field={"titre", "contenu"})
     * @ORM\Column(type="date", nullable=true)
     * 
     */
    private $modifierAt;

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="article", orphanRemoval=true, cascade={"persist"})
     */
    private $media;

    /**
     * @ORM\ManyToMany(targetEntity=Motcle::class, inversedBy="articles")
     */
    private $motcle;

    /**
     * @ORM\ManyToMany(targetEntity=Categorie::class, inversedBy="articles")
     */
    private $categorie;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="article", orphanRemoval=true)
     */
    private $commentaire;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;

    public function __construct()
    {
        $this->media = new ArrayCollection();
        $this->motcle = new ArrayCollection();
        $this->categorie = new ArrayCollection();
        $this->commentaire = new ArrayCollection();
    }

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

    public function getLegende(): ?string
    {
        return $this->legende;
    }

    public function setLegende(string $legende): self
    {
        $this->legende = $legende;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /** fonction setSlug inutile  */

    public function getSommaire(): ?string
    {
        return $this->sommaire;
    }

    public function setSommaire(string $sommaire): self
    {
        $this->sommaire = $sommaire;

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

    public function getValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(bool $valide): self
    {
        $this->valide = $valide;

        return $this;
    }

    public function getPublierAt(): ?\DateTimeInterface
    {
        return $this->publierAt;
    }

    /** fonction setPublierAt inutile */

    public function getModifierAt(): ?\DateTimeInterface
    {
        return $this->modifierAt;
    }

    /*
    public function setModifierAt(?\DateTimeInterface $modifierAt): self
    {
        $this->modifierAt = $modifierAt;

        return $this;
    }
    */

    /**
     * @return Collection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    /**
     * Ajouter ; mettre cascade={'persist'} sur la propriété $media
     *
     * @param Media $medium
     * @return self
     */
    public function addMedium(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media[] = $medium;
            $medium->setArticle($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getArticle() === $this) {
                $medium->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Motcle[]
     */
    public function getMotcle(): Collection
    {
        return $this->motcle;
    }

    public function addMotcle(Motcle $motcle): self
    {
        if (!$this->motcle->contains($motcle)) {
            $this->motcle[] = $motcle;
        }

        return $this;
    }

    public function removeMotcle(Motcle $motcle): self
    {
        $this->motcle->removeElement($motcle);

        return $this;
    }

    /**
     * @return Collection|Categorie[]
     */
    public function getCategorie(): Collection
    {
        return $this->categorie;
    }

    public function addCategorie(Categorie $categorie): self
    {
        if (!$this->categorie->contains($categorie)) {
            $this->categorie[] = $categorie;
        }

        return $this;
    }

    public function removeCategorie(Categorie $categorie): self
    {
        $this->categorie->removeElement($categorie);

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaire(): Collection
    {
        return $this->commentaire;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaire->contains($commentaire)) {
            $this->commentaire[] = $commentaire;
            $commentaire->setArticle($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaire->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getArticle() === $this) {
                $commentaire->setArticle(null);
            }
        }

        return $this;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }
}
