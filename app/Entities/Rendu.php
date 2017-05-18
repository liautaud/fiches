<?php

namespace App\Entities;

use App\Traits\Fetchable;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="rendus")
 */
class Rendu
{
    use Fetchable;

    const ETAT_BROUILLON = -1;
    const ETAT_SOUMIS    = 2;
    const ETAT_REFUSE    = 3;
    const ETAT_VALIDE    = 4;

    /**
     * L'identifiant unique du rendu de fiche.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * L'état du rendu.
     *  
     * @ORM\Column(type="integer")
     */
    protected $etat;

    /**
     * La date à laquelle le rendu a été créé.
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var \DateTime
     */
    protected $dateCreation;

    /**
     * La date à laquelle le rendu a été traité par
     * un chargé de groupe.
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $dateTraitement;
    
    /**
     * La fiche sur laquelle porte le rendu.
     *
     * @ORM\ManyToOne(targetEntity="Fiche", inversedBy="rendus")
     */
    protected $fiche;
    
    /**
     * L'utilisateur qui a effectué le rendu.
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="rendus")
     */
    protected $utilisateur;

    /**
     * L'ensemble de réponses du rendu.
     *
     * @ORM\OneToMany(targetEntity="Reponse", mappedBy="rendu")
     * @ORM\OrderBy({"numero" = "ASC"})
     */
    protected $reponses;

    /**
     * Initialise un rendu.
     */
    public function __construct()
    {
        $this->reponses = new ArrayCollection;
    }

    /**
     * Renvoie l'identifiant unique du rendu.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Renvoie l'état du rendu.
     *
     * @return int
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Remplace l'état du rendu, et dans le cas d'un refus,
     * répercute le refus sur l'ensemble des rendus du même
     * utilisateur pour des fiches qui dépendent de celle-ci.
     *
     * @param int $etat
     */
    public function setEtat($etat)
    {
        if ( ! in_array($etat, [static::ETAT_BROUILLON, static::ETAT_SOUMIS, static::ETAT_VALIDE, static::ETAT_REFUSE])) {
            return;
        }

        // Si on est face à un refus, on refuse tous les rendus
        // du même utilisateur pour les fiches suivantes.
        if ($etat == static::ETAT_REFUSE) {
            $this->getFiche()->getPrecedentes()->forAll(function ($i, Fiche $fiche) {
                $rendu = $this->getUtilisateur()->getDernierRenduForFiche($fiche);

                if ($rendu) {
                    $rendu->setEtat(static::ETAT_REFUSE);
                }
            });
        }

        # TODO : Vérifier que les entités modifiées étaient bien
        # déjà MANAGED par l'EntityManager, histoire que quand
        # on flush toutes les entités soient bien sauvegardées.

        $this->etat = $etat;
    }

    /**
     * Renvoie la date à laquelle le rendu a été créé.
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Renvoie la date à laquelle le rendu a été traité
     * par un chargé de groupe.
     *
     * @return \DateTime
     */
    public function getDateTraitement()
    {
        return $this->dateTraitement;
    }

    /**
     * Modifie la date à laquelle le rendu a été traité
     * par un chargé de groupe.
     *
     * @param \DateTime $dateTraitement
     */
    public function setDateTraitement($dateTraitement)
    {
        $this->dateTraitement = $dateTraitement;
    }

    /**
     * Renvoie la fiche sur laquelle porte le rendu.
     *
     * @return Fiche
     */
    public function getFiche()
    {
        return $this->fiche;
    }

    /**
     * Remplace la fiche sur laquelle porte le rendu.
     *
     * @param Fiche $fiche
     */
    public function setFiche(Fiche $fiche)
    {
        $this->fiche = $fiche;
    }

    /**
     * Renvoie l'utilisateur qui a effectué le rendu.
     *
     * @return Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Remplace l'utilisateur qui a effectué le rendu.
     *
     * @param Utilisateur $utilisateur
     */
    public function setUtilisateur(Utilisateur $utilisateur)
    {
        $this->utilisateur = $utilisateur;
    }

    /**
     * Renvoie l'ensemble des réponses du rendu.
     *
     * @return ArrayCollection[Reponse]
     */
    public function getReponses()
    {
        return $this->reponses;
    }

    /**
     * Renvoie la réponse du rendu avec un numéro donné.
     *
     * @param int $numero
     * @return Reponse|null
     */
    public function getReponse($numero)
    {
        return $this->getReponses()->filter(function (Reponse $reponse) use ($numero) {
            return $reponse->getNumero() == $numero;
        })->first();
    }
}