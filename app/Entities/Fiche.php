<?php

namespace App\Entities;

use App\Traits\Fetchable;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** 
 * @ORM\Entity
 * @ORM\Table(name="fiches")
 */
class Fiche
{
    use Fetchable;

    /**
     * L'ensemble des états que peut prendre une
     * fiche pour chaque Utilisateur.
     */    
    const ETAT_INACCESSIBLE = 0;
    const ETAT_ACCESSIBLE   = 1;
    const ETAT_RENDUE       = 2;
    const ETAT_REFUSEE      = 3;
    const ETAT_VALIDEE      = 4;

    /**
     * L'identifiant unique de la fiche.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Le titre de la fiche.
     *
     * @ORM\Column(type="string")
     */
    protected $titre;

    /**
     * Le langage de la fiche.
     *
     * @ORM\ManyToOne(targetEntity="Langage", inversedBy="fiches")
     * @var Langage
     */
    protected $langage;

    /**
     * Le chemin de la fiche sur le serveur Git.
     *
     * @ORM\Column(type="string")
     */
    protected $cheminGit;

    /**
     * Le chemin des fichiers complémentaires sur le serveur Git,
     * ou une chaîne vide s'il n'y en a pas.
     *
     * @ORM\Column(type="string")
     */
    protected $cheminGitComplementaires;

    /**
     * Si la fiche est ou non un mini-projet.
     *
     * @ORM\Column(type="boolean")
     */
    protected $miniProjet;

    /**
     * L'ensemble des exercices que comporte la fiche.
     *
     * @ORM\OneToMany(targetEntity="Exercice", mappedBy="fiche")
     * @ORM\OrderBy({"numero" = "ASC"})
     */
    protected $exercices;

    /**
     * L'ensemble des fiches dont dépend celle-ci.
     *
     * Notons que, pour faciliter la construction d'un graphe
     * de dépendances, on commence par créer une fiche "racine"
     * d'identifiant 0 qui ne dépend d'aucune autre. 
     *
     * On adopte ensuite la convention que, lorsqu'une fiche
     * "standard" ne dépend d'aucune autre fiche "standard", 
     * elle dépend uniquement de la fiche "racine".
     *
     * @ORM\ManyToMany(targetEntity="Fiche", inversedBy="suivantes")
     * @ORM\JoinTable(
     *     name="dependances",
     *     joinColumns={@ORM\JoinColumn(name="suivante_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="precedente_id", referencedColumnName="id")})     
     */
    protected $precedentes;

    /**
     * L'ensemble des fiches qui dépendent de celle-ci.
     *
     * @ORM\ManyToMany(targetEntity="Fiche", mappedBy="precedentes")
     */
    protected $suivantes;

    /**
     * L'ensemble des rendus de la fiche.
     *
     * @ORM\OneToMany(targetEntity="Rendu", mappedBy="fiche")
     */
    protected $rendus;

    /**
     * Initialise une Fiche.
     */
    public function __construct()
    {
        $this->exercices = new ArrayCollection;
        $this->precedentes = new ArrayCollection;
        $this->suivantes = new ArrayCollection;
        $this->rendus = new ArrayCollection;
    }

    /**
     * Renvoie l'identifiant unique de la fiche.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Renvoie le titre de la fiche.
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Remplace le titre de la fiche.
     *
     * @param string $titre
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    /**
     * Renvoie le langage de la fiche.
     *
     * @return Langage
     */
    public function getLangage()
    {
        return $this->langage;
    }

    /**
     * Modifie le langage de la fiche.
     *
     * @param Langage $langage
     */
    public function setLangage(Langage $langage)
    {
        $this->langage = $langage;
    }

    /**
     * Renvoie le chemin de la fiche sur le serveur Git.
     *
     * @return string
     */
    public function getCheminGit()
    {
        return $this->cheminGit;
    }

    /**
     * Modifie le chemin de la fiche sur le serveur Git.
     *
     * @param string $cheminGit
     */
    public function setCheminGit($cheminGit)
    {
        $this->cheminGit = $cheminGit;
    }

    /**
     * Renvoie le chemin des fichiers complémentaires sur le serveur Git.
     *
     * @return string
     */
    public function getCheminGitComplementaires()
    {
        return $this->cheminGitComplementaires;
    }

    /**
     * Modifie le chemin des fichiers complémentaires sur le serveur Git.
     *
     * @param string $cheminGit
     */
    public function setCheminGitComplementaires($cheminGit)
    {
        $this->cheminGitComplementaires = $cheminGit;
    }

    /**
     * Renvoie si la fiche est un mini-projet.
     *
     * @return bool
     */
    public function isMiniProjet()
    {
        return $this->miniProjet;
    }

    /**
     * Modifie si la fiche est un mini-projet.
     *
     * @param bool $miniProjet
     */
    public function setMiniProjet($miniProjet)
    {
        $this->miniProjet = $miniProjet;
    }

    /**
     * Renvoie l'ensemble des exercices que comporte la fiche.
     *
     * @return ArrayCollection
     */
    public function getExercices()
    {
        return $this->exercices;
    }

    /**
     * Renvoie l'exercice de la fiche avec un numéro donné.
     *
     * @param int $numero
     * @return Exercice|null
     */
    public function getExercice($numero)
    {
        return $this->getExercices()->filter(function (Exercice $exercice) use ($numero) {
            return $exercice->getNumero() == $numero;
        })->first();
    }

    /**
     * Renvoie l'ensemble des fiches qui dépendent de celle-ci.
     *
     * @return ArrayCollection[Fiche]
     */
    public function getSuivantes()
    {
        return $this->suivantes;
    }

    /**
     * Renvoie l'ensemble des fiches dont dépend celle-ci.
     *
     * @return ArrayCollection[Fiche]
     */
    public function getPrecedentes()
    {
        return $this->precedentes;
    }

    /** 
     * Ajoute une fiche dont dépend celle-ci.
     *
     * @param Fiche $precedente
     */
    public function addPrecedente(Fiche $precedente)
    {
        $this->precedentes->add($precedente);
    }

    /**
     * Retire une fiche dont dépend celle-ci.
     *
     * @param Fiche $precedente
     */
    public function removePrecedente(Fiche $precedente)
    {
        $this->precedentes->removeElement($precedente);
    }

    /**
     * Retire l'ensemble des fiches dont dépend celle-ci.
     */
    public function clearPrecedentes()
    {
        $this->precedentes->clear();
    }

    /**
     * Renvoie l'ensemble des rendus de la fiche.
     *
     * @return ArrayCollection[Rendu]
     */
    public function getRendus()
    {
        return $this->rendus;
    }

    /**
     * Renvoie l'ensemble des rendus au moins soumis de la fiche.
     *
     * @return ArrayCollection[Rendu]
     */
    public function getRendusSoumis()
    {
        return $this->getRendus()->filter(function (Rendu $rendu) {
            return $rendu->getEtat() >= Rendu::ETAT_SOUMIS;
        });
    }

    /**
     * Renvoie l'ensemble des rendus validés de la fiche.
     *
     * @return ArrayCollection[Rendu]
     */
    public function getRendusValides()
    {
        return $this->getRendus()->filter(function (Rendu $rendu) {
            return $rendu->getEtat() == Rendu::ETAT_VALIDE;
        });
    }

    /**
     * Renvoie l'état actuel de la fiche pour un utilisateur donné.
     *
     * @param  Utilisateur $utilisateur
     * @return int
     */
    public function getEtatForUtilisateur(Utilisateur $utilisateur)
    {
        // On traite séparément le cas des administrateurs
        if ($utilisateur->isAdministrateur()) {
            return static::ETAT_ACCESSIBLE;
        }

        // On traite séparément le cas de la fiche racine
        if ($this->getId() == 0) {
            return static::ETAT_VALIDEE;
        }

        // On commence par regarder si l'utilisateur a déjà
        // soumis au moins un rendu pour la fiche, en excluant
        // les brouillons.
        $rendus = $utilisateur->getRendusSoumisForFiche($this);

        // S'il n'a soumis aucun rendu, on vérifie alors si la
        // fiche lui est accessible, autrement dit si toutes
        // les fiches dont elle dépend ont été rendues et ne
        // sont pas refusées, avec au moins une fiche validée
        // à distance 5 de celle-ci.
        if ($rendus->isEmpty()) {
            $accessible = $this->getPrecedentes()->forAll(
                function ($index, Fiche $precedente) use ($utilisateur) {
                    return $precedente->isValideeWithin($utilisateur, 3);
                }
            );

            if ($accessible) {
                return static::ETAT_ACCESSIBLE;
            } else {
                return static::ETAT_INACCESSIBLE;
            }
        }

        // On parcourt les rendus, et on regarde si un au moins
        // un est dans l'état VALIDE, sinon si au moins un est
        // dans l'état REFUSE, et sinon on considère que la 
        // fiche n'a pas encore été traitée.
        $etat = static::ETAT_RENDUE;

        $rendus->forAll(function ($index, Rendu $rendu) use (&$etat) {
            if ($rendu->getEtat() >= $etat) {
                $etat = $rendu->getEtat();
            }
        });

        return $etat;
    }

    /**
     * Renvoie si la fiche est validée, ou si elle est rendue
     * et possède un parent validée dans toutes ses branches
     * ascendantes à une profondeur maximale donnée.
     *
     * @return bool
     */
    public function isValideeWithin(Utilisateur $utilisateur, $profondeur)
    {
        if ($profondeur <= 0) {
            return false;
        }

        // Cas de la racine
        if ($this->getId() == 0) {
            return true;
        }

        $rendus = $utilisateur->getRendusSoumisForFiche($this);

        // Si la fiche n'est pas rendue, elle ne peut à fortiori
        // pas être validée.
        if ($rendus->isEmpty()) {
            return false;
        }

        // Sinon, on récupère l'état exact de la fiche.
        $etat = static::ETAT_RENDUE;
        $rendus->forAll(function ($index, Rendu $rendu) use (&$etat) {
            if ($rendu->getEtat() >= $etat) {
                $etat = $rendu->getEtat();
            }
        });

        if ($etat == static::ETAT_REFUSEE) {
            return false;
        } else if ($etat == static::ETAT_VALIDEE) {
            return true;
        }

        // Si la fiche est seulement RENDUE, on doit vérifier qu'elle a
        // au moins un parent VALIDE dans toutess ses branches ascendantes.
        return $this->getPrecedentes()->forAll(
            function ($index, Fiche $precedente) use ($utilisateur, $profondeur) {
                return $precedente->isValideeWithin($utilisateur, $profondeur - 1);
            }
        );
    }

    /**
     * Renvoie un tableau représentant le graphe de toutes
     * les fiches en base de données.
     *
     * @return array
     */
    public static function getGraphe()
    {
        // $fiches = static::allPositive();
        $fiches = static::all();
        $nodes = [];
        $edges = [];

        foreach ($fiches as $fiche) {
            $nodes[] = [
                'id' => $fiche->getId(),
                'label' => $fiche->getTitre(),
                'fiche' => $fiche
            ];

            // Cette opération ne recharge pas une nouvelle
            // fois les fiches depuis la base de données,
            // puisque Doctrine maintient un cache des
            // objets précédemment chargés.
            foreach ($fiche->getSuivantes() as $suivante) {
                $edges[] = [
                    'from' => $fiche->getId(),
                    'to' => $suivante->getId()
                ];
            }
        }

        return compact('nodes', 'edges');
    }
}