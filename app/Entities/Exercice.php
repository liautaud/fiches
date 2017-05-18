<?php

namespace App\Entities;

use App\Traits\Fetchable;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="exercices")
 */
class Exercice
{
    use Fetchable;

    /**
     * L'identifiant unique de l'exercice.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Le numéro de l'exercice.
     *
     * @ORM\Column(type="integer")
     */
    protected $numero;

    /**
     * Le type de rendu attendu pour l'exercice.
     *
     * On peut attendre un rendu de texte, ou un rendu de code
     * écrit dans un certain langage de programmation. L'intérêt
     * de spécifier le type de rendu est que l'on peut adapter
     * l'interface de rendu de fiches en conséquence - en proposant
     * par exemple un éditeur de code avec coloration syntaxique,
     * voire un environnement de développement en ligne (cf repl.it).
     *
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * La fiche parente de l'exercice.
     *
     * @ORM\ManyToOne(targetEntity="Fiche", inversedBy="exercices")
     * @ORM\JoinColumn(name="fiche_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Fiche
     */
    protected $fiche;

    /**
     * Toutes les réponses enregistrées à cet exercice.
     *
     * @ORM\OneToMany(targetEntity="Reponse", mappedBy="exercice")
     */
    protected $reponses;

    /**
     * Initialise un exercice.
     */
    public function __construct()
    {
        $this->reponses = new ArrayCollection;
    }

    /**
     * Renvoie l'identifiant unique de l'exercice.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Renvoie le numéro de l'exercice.
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Remplace le numéro de l'exercice.
     *
     * @param int $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * Renvoie le type de rendu attendu pour l'exercice.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Remplace le type de rendu attendu pour l'exercice.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Renvoie la fiche parente de l'exercice.
     *
     * @return Fiche
     */
    public function getFiche()
    {
        return $this->fiche;
    }

    /**
     * Modifie la fiche parente de l'exercice.
     *
     * @param Fiche $fiche
     */
    public function setFiche(Fiche $fiche)
    {
        $this->fiche = $fiche;
    }

    /**
     * Renvoie toutes les réponses enregistrées à cet exercice.
     *
     * @return ArrayCollection[Reponse]
     */
    public function getReponses()
    {
        return $this->reponses;
    }
}