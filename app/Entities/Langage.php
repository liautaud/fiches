<?php

namespace App\Entities;

use App\Traits\Fetchable;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="langages")
 */
class Langage
{
    use Fetchable;

    /**
     * L'identifiant unique du langage.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Le titre du langage.
     *
     * @ORM\Column(type="string")
     */
    protected $titre;

    /**
     * L'ensemble des fiches qui portent sur le langage.
     *
     * @ORM\OneToMany(targetEntity="Fiche", mappedBy="langage")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $fiches;

    /**
     * Initialise un Langage.
     */
    public function __construct()
    {
        $this->fiches = new ArrayCollection;
    }

    /**
     * Renvoie l'identifiant unique du langage.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Renvoie le titre du langage.
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Remplace le titre du langage.
     *
     * @param string $titre
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    /**
     * Renvoie l'ensemble des fiches qui portent sur le langage.
     *
     * @return ArrayCollection[Fiche]
     */
    public function getFiches()
    {
        return $this->fiches;
    }
}